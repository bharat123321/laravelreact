<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','post_id','comment'];

    public function formattedCreatedDate() {
        if ($this->updated_at->diffInDays() > 30) {
             return   $this->updated_at->toFormattedDateString();
         } else {
             return  $this->updated_at->diffForHumans();
         }
 }
}
