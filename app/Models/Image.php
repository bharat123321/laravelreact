<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['description','topic', 'image','video','file','user_id','visible'];

    protected $casts = [
        'image_paths' => 'array',
    ];
     public function formattedCreatedDate() {
       if ($this->created_at->diffInDays() > 30) {
            return   $this->created_at->toFormattedDateString();
        } else {
            return  $this->created_at->diffForHumans();
        }
}
}