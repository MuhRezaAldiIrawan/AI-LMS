<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class UserPoint extends Model
{
    protected $fillable = ['user_id', 'total_points'];
}