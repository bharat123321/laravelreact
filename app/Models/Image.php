<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['id','description','topic', 'image','video','category','file','user_id','visible'];

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

 public function likes()
    {
        return $this->hasMany(Like::class);
    }
    // Define the inverse relationship with the 'bookmarks' table
    public function bookmarkedByUsers()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'post_id', 'user_id')
                    ->withTimestamps();
    }
}