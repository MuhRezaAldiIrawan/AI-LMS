<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property int $total_points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint whereTotalPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPoint whereUserId($value)
 * @mixin \Eloquent
 */
class UserPoint extends Model
{
    protected $fillable = ['user_id', 'total_points'];
}