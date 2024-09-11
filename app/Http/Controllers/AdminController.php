<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\User;
class AdminController extends Controller
{
    public function Fetchpostdata(){
 $fetchData = Image::join('users', 'images.user_id', '=', 'users.id')
        ->where('images.is_approved', 0)
        ->select('images.*', 'users.firstname','users.lastname','users.avatar')
        ->orderByDesc('images.created_at')
        ->get()
        ->map(function ($image) {
            $image->formatted_date = $image->formattedCreatedDate();
            return $image;
        });
    
    return response()->json(['data' => $fetchData]);

    }

public function VerifiedAccept($id)
{
     
    $post = Image::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post not found'], 404);
    }

    $post->is_approved = 1;
    $post->save();

    
    return response()->json(['message' => 'Post approved successfully', 'post' => $post], 200);
}
public function DeleteVeifiedPost($id)
{
    $post = Image::find($id);
    if(!$post)
    {
        return response()->json(['message'=>'Post notfound'],404);
    }
    $post->delete();
    return response()->json(['message'=>"Post Deleted successfully"]);
}

}
