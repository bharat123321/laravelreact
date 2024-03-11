<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'image','video','file','user_id'];

    protected $casts = [
        'image_paths' => 'array',
    ];
}
