<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class classcode extends Model
{
    use HasFactory;

    protected $fillable=['classcode','user_id','subjectname'];


   
}
