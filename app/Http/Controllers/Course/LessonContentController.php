<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\DocumentAI\V1\RawDocument;
use Illuminate\Support\Facades\File;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Illuminate\Support\Facades\Auth;
use CloudConvert\CloudConvert;
use CloudConvert\Models\Job;
use CloudConvert\Models\Task;
use Exception;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleServiceDrive;

use Google\Cloud\Speech\V2\SpeechClient;
use Google\Cloud\Speech\V2\RecognitionConfig;
use Google\Cloud\Speech\V2\Recognizer;
use Google\Cloud\Speech\V2\Client\SpeechClient as SpeechV2Client; // Kita butuh alias untuk menghindari konflik nama
use Google\Cloud\Speech\V2\RecognitionAudio;
use Google\Cloud\Storage\StorageClient;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Google\Cloud\Speech\V2\BatchRecognizeRequest;
use Google\Cloud\Speech\V2\BatchRecognizeFileMetadata;
use Google\Cloud\Speech\V2\RecognitionFeatures;
use Google\Cloud\Speech\V2\ExplicitDecodingConfig;
use Google\Cloud\Speech\V2\ExplicitDecodingConfig\AudioEncoding;
use Google\ApiCore\ApiException;
use Google\ApiCore\OperationResponse;
use Google\Cloud\Speech\V2\RecognitionOutputConfig;
use Google\Cloud\Speech\V2\GcsOutputConfig;

class LessonContentController extends Controller
{
    public function processAndStoreEmbeddings(Lesson $lesson)
    {
        Log::info("=== START processAndStoreEmbeddings for Lesson ID: {$lesson->id} ===");
        Log::info("Lesson content_type: {$lesson->content_type}");

        $textContent = null;

        if ($lesson->content_type === 'text' && !empty($lesson->content_text)) {
            Log::info("Processing TEXT content for lesson {$lesson->id}");
            $textContent = $lesson->content_text;
            Log::info("Text content length: " . strlen($textContent));
        } elseif ($lesson->content_type === 'file' && !empty($lesson->attachment_path)) {
            Log::info("Processing FILE content for lesson {$lesson->id}, path: {$lesson->attachment_path}");
            $textContent = $this->extractTextFromFile($lesson->attachment_path);
            Log::info("Extracted text length: " . (is_null($textContent) ? 'NULL' : strlen($textContent)));
        } elseif ($lesson->content_type === 'video' && !empty($lesson->video_url)) {
            Log::info("Processing VIDEO content for lesson {$lesson->id}, URL: {$lesson->video_url}");
            // Cek apakah ini URL YouTube yang valid
            if (preg_match('/(youtube.com|youtu.be)/', $lesson->video_url)) {
                Log::info("YouTube URL detected for lesson {$lesson->id}. Starting transcription process.");
                $textContent = $this->transcribeYoutubeVideo($lesson->video_url);
                Log::info("Transcription result length: " . (is_null($textContent) ? 'NULL' : strlen($textContent)));
            } else {
                Log::info("Video URL for lesson {$lesson->id} is not a YouTube link. Skipping transcription.");
            }
        }


        if (empty($textContent)) {
            Log::warning("Text content is empty for lesson {$lesson->id}. Cleaning up old embeddings.");
            $this->deleteEmbeddingsForLesson($lesson);
            Log::info("No processable text content for lesson {$lesson->id}. Old embeddings (if any) have been cleaned up.");
            Log::info("=== END processAndStoreEmbeddings (empty content) ===");
            return;
        }

        Log::info("Text content is available. Proceeding to create embeddings...");

        try {
            Log::info("Loading API configurations...");
            $geminiApiKey = config('services.gemini.api_key');
            $pineconeApiKey = config('services.pinecone.api_key');
            $pineconeHost = config('services.pinecone.host');
            $pineconeNamespace = config('services.pinecone.index');

            if (!$geminiApiKey || !$pineconeApiKey || !$pineconeHost || !$pineconeNamespace) {
                Log::error("Missing API configuration for lesson {$lesson->id}");
                throw new Exception('API Keys, host, atau index name untuk Gemini/Pinecone belum diatur di .env');
            }
            Log::info("API configurations loaded successfully.");

            Log::info("Splitting text into chunks...");
            $chunks = preg_split('/(\r\n|\n|\r){2,}/', $textContent);
            $chunks = array_map('trim', $chunks);
            $chunks = array_filter($chunks); // removes empty strings but preserves keys
            $chunks = array_values($chunks); // reindex to avoid undefined [0]
            if (empty($chunks)) {
                Log::warning("Content for lesson {$lesson->id} resulted in zero chunks after splitting.");
                return;
            }

            Log::info("Processing " . count($chunks) . " chunks for lesson {$lesson->id}.");
            $firstPreview = isset($chunks[0]) ? mb_substr($chunks[0], 0, 100) : '';
            Log::debug("First chunk preview: " . $firstPreview . "...");


            // === PERBAIKAN #1: TAMBAHKAN TIMEOUT ===
            Log::info("Sending request to Gemini API for embeddings...");
            $embeddingResponse = Http::timeout(120)->post("https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:batchEmbedContents?key={$geminiApiKey}", [
                'requests' => collect($chunks)->map(fn ($chunk) => ['model' => 'models/text-embedding-004', 'content' => ['parts' => [['text' => $chunk]]]])
            ]);
            // =======================================

            if (!$embeddingResponse->successful()) {
                Log::error('Gemini Embedding API Error', [
                    'status' => $embeddingResponse->status(),
                    'body' => $embeddingResponse->json()
                ]);
                throw new Exception('Gagal membuat embeddings dari Gemini API.');
            }
            Log::info("Gemini API response received successfully.");
            $embeddings = $embeddingResponse->json()['embeddings'];
            Log::info("Received " . count($embeddings) . " embeddings from Gemini.");

            Log::info("Preparing vectors for Pinecone...");
            $vectorsToUpsert = [];
            foreach ($embeddings as $index => $embedding) {
                $vectorsToUpsert[] = [
                    'id' => "lesson-{$lesson->id}-chunk-{$index}",
                    'values' => $embedding['values'],
                    'metadata' => [
                        'lesson_id' => (int)$lesson->id,
                        'course_id' => (int)$lesson->module->course_id,
                        'text' => mb_strimwidth($chunks[$index], 0, 1000, "...")
                    ]
                ];
            }
            Log::info("Prepared " . count($vectorsToUpsert) . " vectors for upsert.");

            Log::info("Deleting old embeddings for lesson {$lesson->id}...");
            $this->deleteEmbeddingsForLesson($lesson);

            Log::info("Upserting vectors to Pinecone...");
            $pineconeResponse = Http::withHeaders([
                'Api-Key' => $pineconeApiKey,
                'Content-Type' => 'application/json'
            ])->post("https://{$pineconeHost}/vectors/upsert", [
                'vectors' => $vectorsToUpsert,
                'namespace' => $pineconeNamespace
            ]);

            if (!$pineconeResponse->successful()) {
                Log::error('Pinecone Upsert Error', [
                    'status' => $pineconeResponse->status(),
                    'body' => $pineconeResponse->json()
                ]);
                throw new Exception('Gagal menyimpan vectors ke Pinecone.');
            }

            Log::info("Successfully stored " . count($vectorsToUpsert) . " chunks for lesson {$lesson->id}.");
            Log::info("=== END processAndStoreEmbeddings (SUCCESS) ===");
        } catch (Exception $e) {
            Log::error("Failed to process content for lesson {$lesson->id}: " . $e->getMessage());
            Log::error("Exception trace: " . $e->getTraceAsString());
            Log::info("=== END processAndStoreEmbeddings (FAILED) ===");
        }
    }
    private function extractTextFromFile(string $filePath): ?string
    {
        $driveService = null;
        $createdFileId = null;
        $convertedFileId = null;

        try {
            $fullPath = Storage::disk('public')->path($filePath);
            $extension = strtolower(File::extension($fullPath));

            if (in_array($extension, ['ppt', 'pptx'])) {
                Log::info("PPTX file detected. Uploading and converting via Google Drive API...");

                $sharedDriveId = config('services.google.shared_drive_id');
                if (!$sharedDriveId) throw new Exception('GOOGLE_SHARED_DRIVE_ID not set in .env');

                $client = new GoogleClient();
                $client->useApplicationDefaultCredentials();
                $client->addScope(GoogleServiceDrive::DRIVE);
                $driveService = new GoogleServiceDrive($client);

                $file = new \Google\Service\Drive\DriveFile(['name' => basename($filePath), 'parents' => [$sharedDriveId]]);

                $createdFile = $driveService->files->create($file, [
                    'data' => file_get_contents($fullPath),
                    'mimeType' => File::mimeType($fullPath),
                    'uploadType' => 'resumable',
                    'fields' => 'id',
                    'supportsAllDrives' => true
                ]);
                $createdFileId = $createdFile->id;

                $gSlidesFile = new \Google\Service\Drive\DriveFile(['mimeType' => 'application/vnd.google-apps.presentation']);
                $convertedFile = $driveService->files->copy($createdFileId, $gSlidesFile, ['supportsAllDrives' => true, 'fields' => 'id']);
                $convertedFileId = $convertedFile->id;

                $response = $driveService->files->export($convertedFileId, 'text/plain', ['alt' => 'media']);
                $textContent = $response->getBody()->getContents();

                Log::info("Successfully extracted text from PPTX via Google Drive API.");
                return $textContent;
            } elseif ($extension === 'pdf') {

                Log::info("PDF file detected. Extracting text via Document AI...");
                $projectId = config('services.google.project_id');
                $locationId = config('services.google.location_id', 'us');
                $processorId = config('services.google.document_ai_processor_id');

                if (!$projectId || !$processorId) {
                    throw new Exception('GOOGLE_PROJECT_ID atau GOOGLE_DOCUMENT_AI_PROCESSOR_ID belum diatur.');
                }

                // Prefer regional endpoint to match the processor's location to avoid timeouts
                $apiEndpoint = sprintf('%s-documentai.googleapis.com', $locationId);
                $clientOptions = [
                    'credentials' => config('services.google.credentials_path'),
                    'apiEndpoint' => $apiEndpoint,
                ];

                try {
                    $client = new DocumentProcessorServiceClient($clientOptions);
                    $name = $client->processorName((string)$projectId, (string)$locationId, (string)$processorId);
                    $rawDocument = new RawDocument([
                        'content' => file_get_contents($fullPath),
                        'mime_type' => 'application/pdf'
                    ]);
                    $request = (new ProcessRequest())
                        ->setName($name)
                        ->setRawDocument($rawDocument);

                    // Reduce long retries and cap timeout so we can fail fast and fallback
                    $callOptions = [
                        'timeoutMillis' => 60000, // 60s per attempt
                        'retrySettings' => [
                            'totalTimeoutMillis' => 90000, // 90s total
                        ],
                    ];

                    $response = $client->processDocument($request, $callOptions);
                    $extractedText = $response->getDocument()->getText();
                    $client->close();

                    if (is_string($extractedText) && strlen(trim($extractedText)) > 0) {
                        Log::info("Successfully extracted text from PDF via Document AI (endpoint: {$apiEndpoint}).");
                        return $extractedText;
                    }

                    Log::warning('Document AI returned empty text. Will try local pdf-parse fallback.');
                } catch (\Throwable $e) {
                    Log::error("Document AI extraction failed: " . $e->getMessage());
                    Log::info('Attempting local pdf-parse fallback...');
                }

                // Fallback: use local Node pdf-parse script
                $fallbackText = $this->extractPdfTextLocally($fullPath);
                if (is_string($fallbackText) && strlen(trim($fallbackText)) > 0) {
                    Log::info('Local pdf-parse fallback succeeded.');
                    return $fallbackText;
                }

                Log::warning('Local pdf-parse fallback produced no text.');
                return null;
            } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
                return $this->transcribeVideo($fullPath);
            }

            return null;
        } catch (Exception $e) {
            Log::error("Failed to extract text from file {$filePath}: " . $e->getMessage());
            return null;
        } finally {
            // === PERBAIKAN #2: LOGIKA PEMBERSIHAN YANG LEBIH AMAN ===
            if ($driveService) {
                // Hapus file hasil konversi (.gslides)
                if ($convertedFileId) {
                    try {
                        $driveService->files->delete($convertedFileId, ['supportsAllDrives' => true]);
                        Log::info("Cleaned up converted Google Slide file: " . $convertedFileId);
                    } catch (Exception $e) {
                        Log::warning("Could not clean up converted file {$convertedFileId}: " . $e->getMessage());
                    }
                }
                // Hapus file asli yang diupload (.pptx)
                if ($createdFileId) {
                    try {
                        $driveService->files->delete($createdFileId, ['supportsAllDrives' => true]);
                        Log::info("Cleaned up original uploaded file: " . $createdFileId);
                    } catch (Exception $e) {
                        Log::warning("Could not clean up original file {$createdFileId}: " . $e->getMessage());
                    }
                }
            }
            // ========================================================
        }
    }

    /**
     * Fallback PDF text extraction using local Node script (pdf-parse).
     */
    private function extractPdfTextLocally(string $absolutePdfPath): ?string
    {
        try {
            // Verify Node and script exist
            $scriptPath = base_path('scripts/extract-pdf-text.cjs');
            if (!file_exists($scriptPath)) {
                Log::warning('PDF fallback script not found at ' . $scriptPath);
                return null;
            }

            // Create a temp output file
            $outputPath = storage_path('app/pdf_text_' . uniqid() . '.txt');

            $process = new Process([
                // On Windows, rely on association: call `node` explicitly
                'node',
                $scriptPath,
                $absolutePdfPath,
                $outputPath
            ], base_path());
            $process->setTimeout(120);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('pdf-parse fallback process failed: ' . $process->getErrorOutput());
                return null;
            }

            if (!file_exists($outputPath)) {
                Log::warning('pdf-parse fallback did not produce an output file.');
                return null;
            }

            $text = file_get_contents($outputPath);
            @unlink($outputPath);
            return is_string($text) ? $text : null;
        } catch (\Throwable $e) {
            Log::error('extractPdfTextLocally error: ' . $e->getMessage());
            return null;
        }
    }

    private function transcribeVideo(string $videoPath): ?string
    {
        $audioPath = null;
        $gcsUri = null;

        try {
            // 1. Ekstrak audio dari video menggunakan FFmpeg
            $audioPath = $this->extractAudio($videoPath);

            // 2. Upload file audio ke Google Cloud Storage
            $gcsUri = $this->uploadToGCS($audioPath);

            // 3. Lakukan transkripsi dari file di GCS
            $transcript = $this->transcribeAudioFromGCS($gcsUri);

            return $transcript;
        } catch (Exception $e) {
            Log::error("Video Transcription Error: " . $e->getMessage());
            return null;
        } finally {
            // 4. Bersihkan file-file sementara
            if ($audioPath && file_exists($audioPath)) {
                unlink($audioPath);
            }
            if ($gcsUri) {
                $this->deleteFromGCS($gcsUri);
            }
        }
    }

    private function extractAudio(string $videoPath): string
    {
        $tempAudioFilename = 'temp_audio_' . uniqid() . '.wav'; // Ganti ke .wav
        $tempAudioPath = storage_path('app/' . $tempAudioFilename);

        // === PERBAIKAN: Gunakan full path ke ffmpeg.exe ===
        $ffmpegPath = config('services.media.ffmpeg_path');
        if (!$ffmpegPath || !is_string($ffmpegPath)) {
            throw new Exception('FFMPEG_PATH is not configured correctly in .env');
        }

        $ffmpegExecutable = $ffmpegPath;
        if (!file_exists($ffmpegExecutable)) {
            throw new Exception("FFmpeg executable not found at: {$ffmpegExecutable}");
        }

        Log::info("Extracting audio using FFmpeg at: {$ffmpegExecutable}");

        // Perintah FFmpeg diubah untuk menghasilkan WAV PCM 16-bit
        $process = new Process([
            $ffmpegExecutable, '-i', $videoPath,
            '-vn', // Hapus video
            '-acodec', 'pcm_s16le', // Format audio standar (WAV)
            '-ar', '16000', // Sample rate 16kHz
            '-ac', '1', // Mono channel
            '-y', // Overwrite output file if exists
            $tempAudioPath
        ]);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        Log::info("Audio extracted to: " . $tempAudioPath);
        return $tempAudioPath;
    }

    /**
     * Mengunggah file ke Google Cloud Storage dan mengembalikan URI-nya.
     */
    private function uploadToGCS(string $filePath): string
    {
        $storage = new StorageClient([
            'keyFilePath' => config('services.google.credentials_path'),
        ]);

        $bucketName = config('services.google.storage_bucket');
        if (!$bucketName) {
            throw new Exception('GOOGLE_STORAGE_BUCKET is not set in .env');
        }

        $bucket = $storage->bucket($bucketName);
        $objectName = 'audio-for-transcription/' . basename($filePath);

        $file = fopen($filePath, 'r');
        $bucket->upload($file, [
            'name' => $objectName
        ]);

        $gcsUri = "gs://{$bucketName}/{$objectName}";
        Log::info("Audio uploaded to GCS: " . $gcsUri);
        return $gcsUri;
    }

    /**
     * Menghapus file dari Google Cloud Storage.
     */
    private function deleteFromGCS(string $gcsUri)
    {
        try {
            $storage = new StorageClient(['keyFilePath' => config('services.google.credentials_path')]);

            // Parse URI untuk mendapatkan nama bucket dan object
            $parts = explode('/', str_replace('gs://', '', $gcsUri), 2);
            $bucketName = $parts[0];
            $objectName = $parts[1];

            $bucket = $storage->bucket($bucketName);
            $object = $bucket->object($objectName);
            $object->delete();

            Log::info("Cleaned up GCS file: {$gcsUri}");
        } catch (Exception $e) {
            Log::warning("Could not clean up GCS file {$gcsUri}: " . $e->getMessage());
        }
    }

    private function transcribeAudioFromGCS(string $gcsUri): string
    {
        $speechClient = new SpeechV2Client([
            'credentials' => config('services.google.credentials_path')
        ]);

        // Variabel untuk menyimpan NAMA OBJEK output agar bisa dihapus
        $gcsOutputObjectName = null;

        try {
            $projectId = config('services.google.project_id');
            if (!$projectId) {
                throw new Exception('GOOGLE_PROJECT_ID belum diatur.');
            }

            $recognizerName = $speechClient->recognizerName(
                (string)$projectId,
                'global',
                '_'
            );

            $decodingConfig = new ExplicitDecodingConfig([
                'encoding' => AudioEncoding::LINEAR16,
                'sample_rate_hertz' => 16000,
                'audio_channel_count' => 1
            ]);

            $features = new RecognitionFeatures([
                'enable_automatic_punctuation' => true,
                'enable_word_time_offsets' => false
            ]);

            // === PERBAIKAN: Support multiple languages dengan auto-detection ===
            $config = new RecognitionConfig([
                'language_codes' => ['id-ID', 'en-US', 'en-GB'], // Support ID dan EN
                'model' => 'long',
                'features' => $features,
                'explicit_decoding_config' => $decodingConfig
            ]);

            $outputUri = "gs://" . config('services.google.storage_bucket') . "/transcription-results/";
            $gcsOutputConfig = new GcsOutputConfig(['uri' => $outputUri]);
            $outputConfig = new RecognitionOutputConfig(['gcs_output_config' => $gcsOutputConfig]);

            $fileMetadata = new BatchRecognizeFileMetadata(['uri' => $gcsUri]);

            $request = new BatchRecognizeRequest([
                'recognizer' => $recognizerName,
                'config' => $config,
                'files' => [$fileMetadata],
                'recognition_output_config' => $outputConfig
            ]);

            $operation = $speechClient->batchRecognize($request);

            Log::info('Batch recognition operation started. Polling for completion...');
            $operation->pollUntilComplete();

            if ($operation->operationSucceeded()) {
                $response = $operation->getResult();
                $transcript = '';

                // =================================================================
                // == PERBAIKAN: Baca hasil dari file JSON di GCS dengan validasi ==
                // =================================================================
                $storage = new StorageClient(['keyFilePath' => config('services.google.credentials_path')]);
                $bucketName = config('services.google.storage_bucket');
                $bucket = $storage->bucket($bucketName);

                // Dapatkan URI LENGKAP ke file hasil dari respons
                $results = $response->getResults();

                if (empty($results) || !isset($results[$gcsUri])) {
                    Log::error("Speech-to-Text results empty or missing for URI: {$gcsUri}");
                    throw new Exception('No transcription results returned from Speech-to-Text API');
                }

                $fullResultUri = $results[$gcsUri]->getUri();
                Log::info("Transcription result file URI: {$fullResultUri}");

                // Ekstrak HANYA nama objek (path file di dalam bucket)
                $gcsOutputObjectName = str_replace("gs://{$bucketName}/", '', $fullResultUri);

                $object = $bucket->object($gcsOutputObjectName);
                $jsonData = $object->downloadAsString();
                $resultData = json_decode($jsonData, true);

                Log::info("Downloaded transcription JSON, size: " . strlen($jsonData) . " bytes");

                // Validasi struktur hasil
                if (!isset($resultData['results']) || !is_array($resultData['results'])) {
                    Log::error("Invalid transcription result structure. JSON data: " . substr($jsonData, 0, 500));
                    throw new Exception('Invalid transcription result structure from Speech-to-Text API');
                }

                foreach ($resultData['results'] as $result) {
                    if (!empty($result['alternatives'])) {
                        $transcript .= $result['alternatives'][0]['transcript'] . ' ';
                    }
                }
                // =================================================================

                Log::info('Transcription completed successfully. Length: ' . strlen(trim($transcript)));
                return trim($transcript);
            } else {
                $error = $operation->getError();
                throw new Exception('Batch recognition operation failed: ' . $error->getMessage());
            }
        } catch (Exception $e) {
            throw new Exception('Google Speech-to-Text V2 Error: ' . $e->getMessage());
        } finally {
            // Hapus file hasil transkripsi dari GCS menggunakan nama objek yang sudah benar
            if ($gcsOutputObjectName) {
                try {
                    $storage = new StorageClient(['keyFilePath' => config('services.google.credentials_path')]);
                    $bucket = $storage->bucket(config('services.google.storage_bucket'));
                    $object = $bucket->object($gcsOutputObjectName);
                    $object->delete();
                    Log::info("Cleaned up GCS output file: {$gcsOutputObjectName}");
                } catch (Exception $e) {
                    Log::warning("Could not clean up GCS output file {$gcsOutputObjectName}: " . $e->getMessage());
                }
            }
            $speechClient->close();
        }
    }

    public function askLessonAssistant(Request $request, Lesson $lesson)
    {
        $request->validate(['question' => 'required|string|max:1000']);
        $userQuestion = $request->input('question');

        try {
            $geminiApiKey = config('services.gemini.api_key');
            $pineconeApiKey = config('services.pinecone.api_key');
            $pineconeHost = config('services.pinecone.host');
            $pineconeIndex = config('services.pinecone.index');

            $embeddingResponse = Http::post("https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key={$geminiApiKey}", [
                'model' => 'models/text-embedding-004',
                'content' => ['parts' => [['text' => $userQuestion]]]
            ]);
            $questionEmbedding = $embeddingResponse->json()['embedding']['values'];

            $pineconeResponse = Http::withHeaders([
                'Api-Key' => $pineconeApiKey,
                'Content-Type' => 'application/json'
            ])->post("https://{$pineconeHost}/query", [
                'vector' => $questionEmbedding,
                'topK' => 3,
                'includeMetadata' => true,
                'namespace' => $pineconeIndex,
                'filter' => ['lesson_id' => ['$eq' => $lesson->id]]
            ]);

            $context = "";
            if ($pineconeResponse->successful() && !empty($pineconeResponse->json()['matches'])) {
                foreach ($pineconeResponse->json()['matches'] as $match) {
                    $context .= $match['metadata']['text'] . "\n\n";
                }
            } else {
                $context = "Tidak ada informasi spesifik yang ditemukan di materi pelajaran ini.";
            }

            $finalPrompt = "Anda adalah seorang asisten pengajar yang membantu. Berdasarkan konteks materi pelajaran berikut:\n---\n{$context}---\n\nJawab pertanyaan ini dengan jelas dan hanya berdasarkan informasi dari konteks yang diberikan. Jika konteks tidak cukup untuk menjawab, katakan Anda tidak menemukan jawabannya di materi ini.\n\nPertanyaan: '{$userQuestion}'";

            $generationResponse = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$geminiApiKey}", [
                'contents' => [['parts' => [['text' => $finalPrompt]]]]
            ]);

            $answer = $generationResponse->json()['candidates'][0]['content']['parts'][0]['text'];

            return response()->json(['answer' => $answer]);
        } catch (\Exception $e) {
            Log::error('Lesson Assistant Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Maaf, terjadi kesalahan saat berkomunikasi dengan asisten materi.'], 500);
        }
    }

    private function deleteEmbeddingsForLesson(Lesson $lesson)
    {
        try {
            $pineconeApiKey = config('services.pinecone.api_key');
            $pineconeHost = config('services.pinecone.host');
            $pineconeIndex = config('services.pinecone.index');

            $idsToDelete = [];
            for ($i = 0; $i < 50; $i++) {
                $idsToDelete[] = "lesson-{$lesson->id}-chunk-{$i}";
            }

            $response = Http::withHeaders([
                'Api-Key' => $pineconeApiKey,
                'Content-Type' => 'application/json'
            ])->post("https://{$pineconeHost}/vectors/delete", [
                'ids' => $idsToDelete,
                'namespace' => $pineconeIndex
            ]);

            if ($response->successful()) {
                Log::info("Cleaned up old embeddings for lesson {$lesson->id}.");
            } else {
                Log::warning('Pinecone Delete Warning/Error', $response->json());
            }
        } catch (\Exception $e) {
            Log::error("Failed to delete embeddings for lesson {$lesson->id}: " . $e->getMessage());
        }
    }

    public function serveFile(Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $course = $lesson->module->course;

        // 1. Keamanan: Pastikan pengguna terdaftar di kursus ini
        if (!$user->isEnrolledIn($course)) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Pastikan file ada
        if (empty($lesson->attachment_path) || !Storage::disk('public')->exists($lesson->attachment_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // 3. Siapkan path dan header
        $path = Storage::disk('public')->path($lesson->attachment_path);
        $mimeType = File::mimeType($path);
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"' // INI BAGIAN KUNCINYA
        ];

        // 4. Kirim file ke browser
        return response()->file($path, $headers);
    }

    // app/Http/Controllers/LessonContentController.php

    // app/Http/Controllers/LessonContentController.php

    private function transcribeYoutubeVideo(string $youtubeUrl): ?string
    {
        $ytDlpPath = config('services.media.youtube_dl_path');
        $ffmpegPath = config('services.media.ffmpeg_path');

        if (!$ytDlpPath || !is_string($ytDlpPath) || !file_exists($ytDlpPath) || !$ffmpegPath || !is_string($ffmpegPath)) {
            Log::error('YOUTUBE_DL_PATH or FFMPEG_PATH is not configured correctly.');
            return null;
        }

        // Gunakan path Windows native tanpa mixing slash
        $tempDownloadedAudioPath = storage_path('app\\temp_download_' . uniqid());
        $tempWavAudioPath = storage_path('app\\temp_audio_' . uniqid() . '.wav');

        $filesToClean = [];

        try {
            Log::info("Starting YouTube download process for: {$youtubeUrl}");

            // =================================================================
            // == FIX: Download dengan format spesifik yang lebih reliable ==
            // =================================================================
            $downloadProcess = new Process([
                $ytDlpPath,
                '--extract-audio',
                '--audio-format', 'mp3',  // Gunakan mp3 yang lebih reliable
                '--audio-quality', '0',    // Best quality
                '--no-playlist',           // Hindari playlist
                '--prefer-free-formats',   // Prefer format free
                '--no-check-certificate',  // Skip SSL check
                '--ffmpeg-location', $ffmpegPath,  // Specify FFmpeg location
                '-o', $tempDownloadedAudioPath . '.%(ext)s',
                $youtubeUrl
            ]);
            $downloadProcess->setTimeout(3600);

            Log::info("Executing yt-dlp command: " . $downloadProcess->getCommandLine());
            $downloadProcess->mustRun();

            // Cari file yang berhasil didownload
            $pattern = $tempDownloadedAudioPath . '.*';
            $downloadedFiles = glob($pattern);

            Log::info("Looking for downloaded file with pattern: {$pattern}");
            Log::info("Found files: " . json_encode($downloadedFiles));

            if (empty($downloadedFiles)) {
                throw new Exception("yt-dlp ran but the output file was not found. Pattern: {$pattern}");
            }

            $actualDownloadedFile = $downloadedFiles[0];
            $filesToClean[] = $actualDownloadedFile;
            Log::info("Successfully downloaded audio from YouTube to: {$actualDownloadedFile}");

            // =================================================================
            // == Konversi ke WAV menggunakan FFmpeg ==
            // =================================================================
            $ffmpegExecutable = $ffmpegPath;
            if (!file_exists($ffmpegExecutable)) {
                throw new Exception("FFmpeg executable not found at: {$ffmpegExecutable}");
            }

            Log::info("Converting audio to WAV format using: {$ffmpegExecutable}");
            $downloadFileSize = filesize($actualDownloadedFile);
            Log::info("Downloaded file size: {$downloadFileSize} bytes");

            $convertProcess = new Process([
                $ffmpegExecutable,
                '-i', $actualDownloadedFile,
                '-vn',
                '-acodec', 'pcm_s16le',
                '-ar', '16000',
                '-ac', '1',
                '-y',  // Overwrite output file if exists
                $tempWavAudioPath
            ]);
            $convertProcess->setTimeout(3600);
            $convertProcess->mustRun();
            $filesToClean[] = $tempWavAudioPath;

            $wavFileSize = filesize($tempWavAudioPath);
            Log::info("Successfully converted downloaded audio to WAV: {$tempWavAudioPath}");
            Log::info("WAV file size: {$wavFileSize} bytes");

            // === PERBAIKAN: Langsung upload dan transcribe, skip extractAudio ===
            // File sudah dalam format WAV yang benar, tidak perlu ekstrak lagi
            Log::info("Uploading WAV file to GCS for transcription...");
            Log::info("WAV file path to upload: {$tempWavAudioPath}");
            Log::info("WAV file exists: " . (file_exists($tempWavAudioPath) ? 'YES' : 'NO'));

            $gcsUri = $this->uploadToGCS($tempWavAudioPath);
            $filesToClean[] = $gcsUri; // Tambahkan untuk cleanup

            Log::info("Starting transcription from GCS...");
            Log::info("GCS URI for transcription: {$gcsUri}");
            $transcript = $this->transcribeAudioFromGCS($gcsUri);            Log::info("Transcription completed. Cleaning up GCS file...");
            $this->deleteFromGCS($gcsUri);

            return $transcript;
            // ==================================================================

        } catch (ProcessFailedException $exception) {
            Log::error("Failed during YouTube audio processing: " . $exception->getMessage());
            Log::error("Process output: " . $exception->getProcess()->getOutput());
            Log::error("Process error output: " . $exception->getProcess()->getErrorOutput());
            return null;
        } catch (Exception $e) {
            Log::error("Exception during YouTube transcription: " . $e->getMessage());
            return null;
        } finally {
            // Bersihkan semua file sementara lokal (bukan GCS URI)
            foreach ($filesToClean as $file) {
                // Skip GCS URIs (yang dimulai dengan gs://)
                if (strpos($file, 'gs://') === 0) {
                    continue;
                }

                if (file_exists($file)) {
                    unlink($file);
                    Log::info("Cleaned up temporary file: {$file}");
                }
            }
        }
    }
}
