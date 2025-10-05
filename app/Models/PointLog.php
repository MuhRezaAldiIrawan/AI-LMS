<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    protected $fillable = ['user_id', 'points_earned', 'reason', 'related_type', 'related_id'];
    public function related() { 
        return $this->morphTo(); 
    }
}
