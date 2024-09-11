<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
class WelcomeController extends Controller
{
    public function fetchpublicdata() {
    $fetchData = Image::join('users', 'images.user_id', '=', 'users.id')
        ->where('images.visible', 0)
        ->select('images.*', 'users.firstname', 'users.avatar')
        ->orderByDesc('images.created_at')
        ->get()
        ->map(function ($image) {
            $image->formatted_date = $image->formattedCreatedDate();
            return $image;
        });

    \Log::info($fetchData);

    return response()->json(['data' => $fetchData]);
}

public function FetchCollectionbook(){
     $fetchData = Image::join('users', 'images.user_id', '=', 'users.id')
        ->where(['images.visible'=>'false','images.is_approved'=>1])
        ->select('images.*', 'users.firstname', 'users.avatar')
        ->orderByDesc('images.created_at')
        ->get()
        ->map(function ($image) {
            $image->formatted_date = $image->formattedCreatedDate();
            return $image;
        });
    return response()->json(['data' => $fetchData]);
}
 public function viewbook($id)
{
    
    $fetchdata = Image::join('users', 'images.user_id', '=', 'users.id')->where('images.id', $id)->get();

    return response()->json([
        'data' => $fetchdata
    ]);
}

}
