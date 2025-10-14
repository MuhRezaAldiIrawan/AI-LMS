<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $quiz_id
 * @property int $score
 * @property int $passed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuizAttemptAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\Quiz $quiz
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt wherePassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizAttempt whereUserId($value)
 * @mixin \Eloquent
 */
class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'passed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }
}