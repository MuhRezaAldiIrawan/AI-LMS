<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property int $points_cost
 * @property int $stock
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward wherePointsCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reward whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'points_cost',
        'stock',
        'image',
        'is_active'
    ];

}
