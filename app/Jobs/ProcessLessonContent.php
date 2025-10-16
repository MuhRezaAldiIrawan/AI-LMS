<?php

namespace App\Jobs;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Course\LessonContentController;
use Illuminate\Support\Facades\Log;

class ProcessLessonContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Lesson $lesson)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting to process content for lesson {$this->lesson->id}");

        try {
            // Buat instance dari controller untuk memanggil metodenya
            $controller = new LessonContentController();

            // Panggil metode pemrosesan publik dari controller
            $controller->processAndStoreEmbeddings($this->lesson);

            Log::info("Finished processing content for lesson {$this->lesson->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process lesson {$this->lesson->id}: " . $e->getMessage());

            // Re-throw exception untuk retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed permanently for lesson {$this->lesson->id}: " . $exception->getMessage());
    }
}
