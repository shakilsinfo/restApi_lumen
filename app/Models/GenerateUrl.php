<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenerateUrl extends Model
{
    protected $table = 'generated_url';
    protected $fillable = ['user_id','url_code','register_url','parent_user_id'];
}
