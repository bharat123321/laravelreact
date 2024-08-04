<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like; 
use App\Models\Bookmark;
use App\Models\Image; 
use Illuminate\Support\Facades\Auth;

class ViewBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['bookmark', 'like']);
    }
     public function viewbook($id)
{
    // Fetch the data about the book
    $fetchdata = Image::join('users', 'images.user_id', '=', 'users.id')
        ->where('images.id', $id)
        ->get();
    
    // Count the number of likes for the book
    $likescount = like::where('post_id', $id)->count();
    
    // Check if the current user has liked the book
    $likespost = like::where('user_id', Auth::user()->id)
        ->where('post_id', $id)
        ->exists(); // Use exists() to check if there are any likes
    
    // Check if the current user has bookmarked the book
    $bookmarkd = bookmark::where('user_id', Auth::user()->id)
        ->where('post_id', $id)
        ->exists(); // Use exists() to check if there are any bookmarks

    // Determine the success status based on whether likes and bookmarks exist
    $success = $bookmarkd;
    $likesuccess = $likespost;

    // Log fetched data for debugging
    \Log::info($fetchdata);
    
    // Return the response as JSON
    return response()->json([
        'data' => $fetchdata,
        'likescount' => $likescount,
        'likepost' => $likesuccess,
        'bookmark' => $success
    ]);
}

    public function bookmark(Request $request)
    {
        $postId = $request->input('id');
        $user = Auth::user();
        \Log::info($user);
        \Log::info($postId);
       if ($user) {
            // Check if the bookmark already exists
            $existingbookmark = Bookmark::where('user_id', $user->id)->where('post_id', $postId)->first();

            if ($existingbookmark) {
                // Remove bookmark if it already exists
                $existingbookmark->delete();
                return response()->json(['success' => true, 'message' => 'Saved removed']);
            } else {
                // Add new like
                $like = new Bookmark();
                $like->user_id = $user->id;
                $like->post_id = $postId;
                $like->save();
                $countpost = Bookmark::where('post_id',$postId)->count();

                return response()->json(['success' => true, 'message' => 'Saved','bookmarkcount'=>$countpost]);
            }
        }
        return response()->json(['success' => false, 'message' => 'User not authenticated']);
    }

    public function like(Request $request)
    {
        $postId = $request->input('id');
        $user = Auth::user();
        \Log::info($user);
        if ($user) {
            // Check if the like already exists
            $existingLike = Like::where('user_id', $user->id)->where('post_id', $postId)->first();

            if ($existingLike) {
                // Remove like if it already exists
                $existingLike->delete();
                return response()->json(['success' => true, 'message' => 'Like removed']);
            } else {
                // Add new like
                $like = new Like();
                $like->user_id = $user->id;
                $like->post_id = $postId;
                $like->save();

                return response()->json(['success' => true, 'message' => 'Liked']);
            }
        }
        return response()->json(['success' => false, 'message' => 'User not authenticated']);
    }
}
