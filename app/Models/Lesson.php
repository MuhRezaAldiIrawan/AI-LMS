<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id', 'title', 'summary', 'order', 'content_type',
        'video_url', 'content_text', 'attachment_path', 'duration_in_minutes',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
    public function completedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_user')->withTimestamps();
    }
}