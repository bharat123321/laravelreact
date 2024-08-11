<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\comment;
class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['Comments']);
    }
    public function index($post_id)
    {
        $comments = Comment::join('users', 'comments.user_id', '=', 'users.id')
            ->where('post_id', $post_id)
            ->select('comments.*', 'users.firstname','users.lastname', 'users.avatar')
            ->orderBy('id','desc')
            ->get();
    
        // Log the raw data for debugging
        \Log::info($comments);
    
        $formattedComments = $comments->map(function($comment) {
            $comment->formatted_date = $comment->formattedCreatedDate();
            return $comment;
        });
    
        return response()->json($formattedComments);
    }
    
    public function Comments(Request $request)
    {
        \Log::info($request->all());
        $validator = Validator::make($request->all(), [
           "comments"=>"required"
        ]);
        $comments = $request->comments;
        $user_id = Auth::user()->id;
        $post_id = $request->post_id;
       $comment = new Comment();
       $comment->user_id = $user_id;
       $comment->post_id = $post_id;
       $comment->comment = $request->comments;
       $comment->save();
        return response()->json(["success"=>"uploaded sucessfully"]);

    }
    public function updateComment(Request $request, $id)
    {
        \Log::info($request->all());
    
        // Validate the request
        $request->validate([
            'content' => 'required|string',
        ]);
    
        $commentContent = $request->input('content');
    
        // Find the comment and update it
        $comment = Comment::find($id);
        if ($comment) {
            $comment->comment = $commentContent;
            $comment->save(); // This will update the `updated_at` timestamp
            return response()->json(["success" => "Comment updated"]);
        } else {
            return response()->json(["error" => "Comment not found"], 404);
        }
    }

    public function DeleteComment(Request $request, $id)    
    {
        \Log::info($request->all());
        \Log::info($id);
        Comment::where("id", $id)->delete();
    }
    
}
