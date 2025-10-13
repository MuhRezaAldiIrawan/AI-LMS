<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiChatHistory;
use App\Models\User;
use Carbon\Carbon;

class CleanupChatHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup
                            {--days=30 : Number of days to keep chat history}
                            {--max-messages=500 : Maximum messages per user to keep}
                            {--user= : Specific user ID to cleanup (optional)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old AI chat history to prevent database bloat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $maxMessages = $this->option('max-messages');
        $userId = $this->option('user');
        $dryRun = $this->option('dry-run');

        $this->info("Starting chat history cleanup...");
        $this->info("Configuration:");
        $this->line("- Keep last {$days} days");
        $this->line("- Keep max {$maxMessages} messages per user");
        $this->line("- Dry run: " . ($dryRun ? 'Yes' : 'No'));

        if ($userId) {
            $this->line("- Target user ID: {$userId}");
        }

        $this->newLine();

        $deletedCount = 0;
        $totalUsers = 0;

        if ($userId) {
            // Cleanup specific user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return Command::FAILURE;
            }

            $deletedCount += $this->cleanupUserChats($user, $days, $maxMessages, $dryRun);
            $totalUsers = 1;
        } else {
            // Cleanup all users with chat history
            $usersWithChats = User::whereHas('aiChatHistories')->get();
            $totalUsers = $usersWithChats->count();

            $this->info("Found {$totalUsers} users with chat history");

            if ($totalUsers > 0) {
                $progressBar = $this->output->createProgressBar($totalUsers);
                $progressBar->start();

                foreach ($usersWithChats as $user) {
                    $deletedCount += $this->cleanupUserChats($user, $days, $maxMessages, $dryRun);
                    $progressBar->advance();
                }

                $progressBar->finish();
                $this->newLine();
            }
        }

        // Summary
        $this->newLine();
        if ($dryRun) {
            $this->info("Dry run completed! Would delete {$deletedCount} messages from {$totalUsers} user(s)");
        } else {
            $this->info("Cleanup completed! Deleted {$deletedCount} messages from {$totalUsers} user(s)");
        }

        return Command::SUCCESS;
    }

    private function cleanupUserChats(User $user, int $days, int $maxMessages, bool $dryRun): int
    {
        $deletedCount = 0;

        // Delete old messages by date
        $oldMessages = $user->aiChatHistories()
            ->where('created_at', '<', Carbon::now()->subDays($days))
            ->get();

        if ($oldMessages->count() > 0) {
            if ($dryRun) {
                $deletedCount += $oldMessages->count();
                $this->line("Would delete {$oldMessages->count()} old messages for user {$user->name}");
            } else {
                $user->aiChatHistories()
                    ->where('created_at', '<', Carbon::now()->subDays($days))
                    ->delete();
                $deletedCount += $oldMessages->count();
            }
        }

        // Delete excess messages (keep only latest N messages)
        $totalMessages = $user->aiChatHistories()->count();

        if ($totalMessages > $maxMessages) {
            $excessCount = $totalMessages - $maxMessages;

            if ($dryRun) {
                $deletedCount += $excessCount;
                $this->line("Would delete {$excessCount} excess messages for user {$user->name}");
            } else {
                $oldestMessages = $user->aiChatHistories()
                    ->orderBy('created_at', 'asc')
                    ->limit($excessCount)
                    ->pluck('id');

                AiChatHistory::whereIn('id', $oldestMessages)->delete();
                $deletedCount += $excessCount;
            }
        }

        return $deletedCount;
    }
}
