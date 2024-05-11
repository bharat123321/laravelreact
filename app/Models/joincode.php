<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class joincode extends Model
{
    use HasFactory;

    protected $fillable =['user_id','classcode','join_id'];
}
