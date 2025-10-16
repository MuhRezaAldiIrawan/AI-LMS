<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AiChatHistory;
use App\Models\User;
use Carbon\Carbon;

class AiAssistantController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sessionId = $request->get('session_id', $user->getCurrentChatSession());

        // Ambil history chat untuk session ini (maksimal 50 pesan terakhir)
        $chatHistory = $user->aiChatHistories()
            ->forSession($sessionId)
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        return view('pages.aiassistant.aiassistant', compact('chatHistory', 'sessionId'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:2000',
            'session_id' => 'nullable|string'
        ]);

        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key untuk AI tidak dikonfigurasi.'], 500);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userQuestion = $request->input('question');
        $sessionId = $request->input('session_id', $user->getCurrentChatSession());

        if (rand(1, 10) === 1) {
            $user->cleanupOldChats();
        }

        $userMessage = AiChatHistory::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'message_type' => 'user',
            'message' => $userQuestion,
            'metadata' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        ]);

        $recentHistory = AiChatHistory::forUser($user->id)
            ->forSession($sessionId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        $contents = [
            ['role' => 'user', 'parts' => [['text' => "Anda adalah asisten AI yang ramah dan membantu di Learning Management System (LMS) Bosowa. Nama user adalah {$user->name}. Selalu jawab pertanyaan dengan sopan dan jelas."]]]
        ];

        foreach ($recentHistory as $history) {
            if ($history->id !== $userMessage->id) {
                $role = $history->message_type === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $history->message]]
                ];
            }
        }

        $contents[] = ['role' => 'user', 'parts' => [['text' => $userQuestion]]];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
        $payload = ['contents' => $contents];

        try {
            $response = Http::timeout(30)->post($url, $payload);

            if ($response->successful() && isset($response->json()['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = $response->json()['candidates'][0]['content']['parts'][0]['text'];

                AiChatHistory::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'message_type' => 'ai',
                    'message' => $answer,
                    'metadata' => [
                        'response_time' => $response->transferStats?->getTransferTime(),
                        'model_used' => 'gemini-2.5-flash'
                    ]
                ]);

                return response()->json([
                    'answer' => $answer,
                    'session_id' => $sessionId,
                    'timestamp' => now()->format('H:i')
                ]);
            }

            Log::error('Gemini API Error:', $response->json());
            return response()->json([
                'error' => 'Maaf, terjadi kesalahan saat menghubungi asisten AI.',
                'details' => $response->json()
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('AI Assistant Error:', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
                'session_id' => $sessionId
            ]);

            return response()->json([
                'error' => 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return response()->json(['error' => 'Session ID required'], 400);
        }

        $history = $user->aiChatHistories()
            ->forSession($sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'message_type' => $chat->message_type,
                    'message' => $chat->message,
                    'timestamp' => $chat->created_at->format('H:i'),
                    'formatted_time' => $chat->getFormattedTimeAttribute()
                ];
            });

        return response()->json(['history' => $history]);
    }

    public function clearHistory(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sessionId = $request->input('session_id');

        if ($sessionId) {
            $user->aiChatHistories()->forSession($sessionId)->delete();
        } else {
            $user->aiChatHistories()->delete();
        }

        return response()->json(['message' => 'Chat history cleared successfully']);
    }

    public function newSession()
    {
        $sessionId = AiChatHistory::generateSessionId();
        return response()->json([
            'session_id' => $sessionId,
            'message' => 'New chat session created'
        ]);
    }

    /**
     * Handle AI chat in lesson page
     */
    public function lessonChat(Request $request)
    {
        try {
            // Validate incoming request
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'lesson_id' => 'required|exists:lessons,id',
            ]);

            $message = $validated['message'];
            $lessonId = $validated['lesson_id'];

            $geminiApiKey = config('services.gemini.api_key');
            $pineconeApiKey = config('services.pinecone.api_key');
            $pineconeHost = config('services.pinecone.host');
            $pineconeNamespace = config('services.pinecone.index');

            if (!$geminiApiKey) {
                return response()->json([
                    'error' => 'Gemini API key tidak dikonfigurasi.',
                    'answer' => 'Maaf, layanan AI belum tersedia.'
                ], 500);
            }

            // Step 1: Generate embedding for user question
            $embeddingResponse = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key={$geminiApiKey}",
                [
                    'model' => 'models/text-embedding-004',
                    'content' => ['parts' => [['text' => $message]]]
                ]
            );


            if (!$embeddingResponse->successful()) {
                Log::error('Embedding generation failed', [
                    'status' => $embeddingResponse->status(),
                    'response' => $embeddingResponse->json()
                ]);

                // Fallback: Answer without RAG context
                return $this->answerWithoutContext($message, $geminiApiKey);
            }

            $questionEmbedding = $embeddingResponse->json()['embedding']['values'] ?? null;



            if (!$questionEmbedding) {
                Log::error('No embedding values returned');
                return $this->answerWithoutContext($message, $geminiApiKey);
            }

            // Step 2: Query Pinecone for relevant context (only if configured)
            $context = "";
            if ($pineconeApiKey && $pineconeHost && $pineconeNamespace) {
                $pineconeResponse = Http::timeout(30)->withHeaders([
                    'Api-Key' => $pineconeApiKey,
                    'Content-Type' => 'application/json'
                ])->post("https://{$pineconeHost}/query", [
                    'vector' => $questionEmbedding,
                    'topK' => 5,
                    'includeMetadata' => true,
                    'namespace' => $pineconeNamespace,
                    'filter' => ['lesson_id' => (int)$lessonId] // Fixed filter format
                ]);

                if ($pineconeResponse->successful() && !empty($pineconeResponse->json()['matches'])) {
                    foreach ($pineconeResponse->json()['matches'] as $match) {
                        if (isset($match['metadata']['text'])) {
                            $context .= $match['metadata']['text'] . "\n\n";
                        }
                    }
                } else {
                    Log::warning('Pinecone query failed or no matches', [
                        'status' => $pineconeResponse->status(),
                        'response' => $pineconeResponse->json()
                    ]);
                }
            }

            // Step 3: Generate AI response with or without context
            if (!empty(trim($context))) {
                $finalPrompt = "Anda adalah asisten pengajar yang membantu di LMS Bosowa. Berdasarkan konteks materi pelajaran berikut:\n\n---\n{$context}---\n\nJawab pertanyaan ini dengan jelas dan hanya berdasarkan informasi dari konteks yang diberikan. Jika konteks tidak cukup untuk menjawab, katakan bahwa informasi tersebut tidak ada di materi ini, tapi tetap berikan jawaban umum yang membantu.\n\nPertanyaan: '{$message}'";
            } else {
                $finalPrompt = "Anda adalah asisten pengajar yang membantu di LMS Bosowa. Jawab pertanyaan berikut dengan jelas dan informatif. Jika ini tentang materi pelajaran spesifik, beritahu bahwa Anda tidak memiliki akses ke konten materi tersebut, tapi tetap berikan informasi umum yang relevan.\n\nPertanyaan: '{$message}'";
            }

            $generationResponse = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$geminiApiKey}",
                [
                    'contents' => [['parts' => [['text' => $finalPrompt]]]]
                ]
            );

            // dd($generationResponse->json());

            if (!$generationResponse->successful()) {
                Log::error('Gemini generation failed', [
                    'status' => $generationResponse->status(),
                    'response' => $generationResponse->json()
                ]);

                return response()->json([
                    'error' => 'Gagal mendapatkan respons dari AI.',
                    'answer' => 'Maaf, terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi.'
                ], 500);
            }

            $answer = $generationResponse->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, tidak ada respons yang diterima.';

            // Return successful response
            return response()->json([
                'success' => true,
                'answer' => $answer,
                'timestamp' => now()->format('H:i'),
                'has_context' => !empty(trim($context))
            ]);

        } catch (\Exception $e) {
            Log::error('Lesson chat error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'lesson_id' => $request->input('lesson_id')
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan sistem.',
                'answer' => 'Maaf, terjadi kesalahan. Silakan coba lagi nanti.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Answer without RAG context (fallback)
     */
    private function answerWithoutContext($message, $geminiApiKey)
    {
        try {
            $prompt = "Anda adalah asisten pengajar yang membantu di LMS Bosowa. Jawab pertanyaan berikut dengan jelas dan informatif:\n\nPertanyaan: '{$message}'";

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$geminiApiKey}",
                [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]
            );

            if ($response->successful()) {
                $answer = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, tidak ada respons.';

                return response()->json([
                    'success' => true,
                    'answer' => $answer,
                    'timestamp' => now()->format('H:i'),
                    'has_context' => false
                ]);
            }

            return response()->json([
                'error' => 'Gagal mendapatkan respons.',
                'answer' => 'Maaf, layanan AI sedang tidak tersedia.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Kesalahan sistem.',
                'answer' => 'Maaf, terjadi kesalahan.'
            ], 500);
        }
    }
}
