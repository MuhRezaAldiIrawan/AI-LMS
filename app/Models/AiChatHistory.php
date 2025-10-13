<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AiChatHistory extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'message_type',
        'message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeUserMessages($query)
    {
        return $query->where('message_type', 'user');
    }

    public function scopeAiMessages($query)
    {
        return $query->where('message_type', 'ai');
    }

    // Helper methods
    public static function generateSessionId(): string
    {
        return uniqid('chat_', true);
    }

    public static function cleanupOldChats($userId, $keepDays = 30, $maxMessages = 500)
    {
        $user = User::find($userId);
        if (!$user) return;

        // Delete chats older than specified days
        static::forUser($userId)
            ->where('created_at', '<', Carbon::now()->subDays($keepDays))
            ->delete();

        // Keep only the latest messages if exceeding limit
        $totalMessages = static::forUser($userId)->count();

        if ($totalMessages > $maxMessages) {
            $messagesToDelete = $totalMessages - $maxMessages;
            $oldestMessages = static::forUser($userId)
                ->orderBy('created_at', 'asc')
                ->limit($messagesToDelete)
                ->get();

            static::whereIn('id', $oldestMessages->pluck('id'))->delete();
        }
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    public function getIsFromUserAttribute()
    {
        return $this->message_type === 'user';
    }

    public function getIsFromAiAttribute()
    {
        return $this->message_type === 'ai';
    }
}
