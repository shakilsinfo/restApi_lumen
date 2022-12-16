<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarnPoint extends Model
{
    protected $table = 'earn_points';
    protected $fillable = ['user_id','points'];

}
