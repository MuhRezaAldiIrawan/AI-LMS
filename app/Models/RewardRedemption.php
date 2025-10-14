<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $reward_id
 * @property int $points_cost
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Reward $reward
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption wherePointsCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereRewardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RewardRedemption whereUserId($value)
 * @mixin \Eloquent
 */
class RewardRedemption extends Model
{
    use HasFactory;
    // [PERBAIKAN] Ubah 'points_spent' menjadi 'points_cost'
    protected $fillable = ['user_id', 'reward_id', 'points_cost', 'status', 'admin_notes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}