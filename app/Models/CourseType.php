<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
}