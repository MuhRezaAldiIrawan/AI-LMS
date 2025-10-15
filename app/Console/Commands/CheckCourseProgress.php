<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Course;

class CheckCourseProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:check-progress {userId} {courseId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check course completion progress for a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $courseId = $this->argument('courseId');

        $user = User::with(['completedLessons', 'quizAttempts'])->find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $course = Course::with(['modules.lessons', 'modules.quiz'])->find($courseId);

        if (!$course) {
            $this->error("Course with ID {$courseId} not found!");
            return 1;
        }

        $this->info("=== Course Progress Report ===");
        $this->info("User: {$user->name} (ID: {$user->id})");
        $this->info("Course: {$course->title} (ID: {$course->id})");
        $this->newLine();

        // Check enrollment status
        $isEnrolled = $user->isEnrolledIn($course);
        $hasAccess = $user->hasAccessToCourse($course);

        $this->info("Enrollment Status:");
        $this->line("  - Has Access: " . ($hasAccess ? '✓ Yes' : '✗ No'));
        $this->line("  - Is Enrolled: " . ($isEnrolled ? '✓ Yes' : '✗ No'));
        $this->newLine();

        // Course structure
        $totalLessons = $course->modules->sum(fn($module) => $module->lessons->count());
        $totalQuizzes = $course->modules->whereNotNull('quiz')->count();
        $totalItems = $totalLessons + $totalQuizzes;

        $this->info("Course Structure:");
        $this->line("  - Total Modules: {$course->modules->count()}");
        $this->line("  - Total Lessons: {$totalLessons}");
        $this->line("  - Total Quizzes: {$totalQuizzes}");
        $this->line("  - Total Items: {$totalItems}");
        $this->newLine();

        // Completed lessons
        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))
            ->get();

        $this->info("Completed Lessons ({$completedLessons->count()}/{$totalLessons}):");
        if ($completedLessons->count() > 0) {
            foreach ($completedLessons as $lesson) {
                $this->line("  ✓ [{$lesson->id}] {$lesson->title}");
            }
        } else {
            $this->line("  - No lessons completed yet");
        }
        $this->newLine();

        // Passed quizzes
        $passedQuizzes = 0;
        $quizDetails = [];
        $allQuizzes = $course->modules->map->quiz->filter();

        foreach ($allQuizzes as $quiz) {
            $attempt = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->latest()
                ->first();

            if ($attempt) {
                $passedQuizzes++;
                $quizDetails[] = "  ✓ [{$quiz->id}] {$quiz->title} - Score: {$attempt->score}/{$attempt->total_questions}";
            } else {
                $quizDetails[] = "  ✗ [{$quiz->id}] {$quiz->title} - Not passed";
            }
        }

        $this->info("Quiz Results ({$passedQuizzes}/{$totalQuizzes}):");
        foreach ($quizDetails as $detail) {
            $this->line($detail);
        }
        $this->newLine();

        // Progress calculation
        $completedItems = $completedLessons->count() + $passedQuizzes;
        $progressPercentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        // Using Course model method
        $courseMethodProgress = $course->getCompletionPercentage($user);
        $isCompleted = $course->isCompletedByUser($user);

        $this->info("Progress Summary:");
        $this->line("  - Completed Items: {$completedItems}/{$totalItems}");
        $this->line("  - Manual Calculation: {$progressPercentage}%");
        $this->line("  - Course Method Result: {$courseMethodProgress}%");
        $this->line("  - Is Course Completed: " . ($isCompleted ? '✓ Yes' : '✗ No'));
        $this->newLine();

        // Show progress bar
        $this->output->progressStart(100);
        $this->output->progressAdvance($progressPercentage);
        $this->output->progressFinish();
        $this->newLine();

        // Recommendations
        if ($progressPercentage < 100) {
            $this->warn("Recommendations:");

            if ($completedLessons->count() < $totalLessons) {
                $remaining = $totalLessons - $completedLessons->count();
                $this->line("  - Complete {$remaining} more lesson(s)");
            }

            if ($passedQuizzes < $totalQuizzes) {
                $remaining = $totalQuizzes - $passedQuizzes;
                $this->line("  - Pass {$remaining} more quiz(zes)");
            }
        } else {
            $this->info("🎉 Congratulations! Course is 100% completed!");
        }

        return 0;
    }
}
