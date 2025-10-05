<?php
// File: app/Models/Module.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'order'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }
}