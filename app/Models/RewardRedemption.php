<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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