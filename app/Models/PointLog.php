<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $points_earned
 * @property string $reason
 * @property string $related_type
 * @property int $related_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $related
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog wherePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointLog whereUserId($value)
 * @mixin \Eloquent
 */
class PointLog extends Model
{
    protected $fillable = ['user_id', 'points_earned', 'reason', 'related_type', 'related_id'];
    public function related() { 
        return $this->morphTo(); 
    }
}
