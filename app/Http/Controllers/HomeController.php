<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
           
         $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    
 
}
      public function register(Request $request){
        $validate = Validator::make($request->all(),[
         'firstname' =>'required|string',
         'lastname' =>'required|string',
          'email' => 'required|email|max:255|unique:users',
          'password'=>'required',
          'gender'=>'required',
          'address' =>'required',
          'country'=>'required'
        ]);
          
        if($validate->fails()){

         return response()->json([
            'status'=>422,
            'errors'=>$validate->messages()],422);
        }
        else{
               
              
            $user = User::create([
            'firstname' =>$request->firstname,
            'lastname' =>$request->lastname,
            'email' =>$request->email,
            'password'=>$request->password,
            'gender'=>$request->gender,
            'address'=>$request->address,
            'country' =>$request->country
            ]);
             
            if($user){
              
                return response()->json([
                  'status'=>200,
                  'message' =>'Successfully created'
                ],200);
            }
            else{
                return response()->json([
                  'status'=>500,
                  'message' =>'Something went wrong',
                ],500);
            }
        }
    }
    public function check(Request $request){
               echo "check";
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
   protected function respondWithToken($token)
{
    $user = auth()->user(); // Retrieve the currently authenticated user

    // if ($user->first_time_login == 1) {
    //     $user->first_time_login = 0; // Update first_time_login to 0
    //     $user->save(); // Save the changes to the user model
    // }

    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60,
        'user' => $user,
        'first_time_login' => $user->first_time_login,
    ]);
}

public function upload(Request $request)
{
     if ($request->has('images')) {
    $validator = Validator::make($request->all(), [
         'images'=>'required',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'string|nullable', // Optional description field
    ]);

    if ($validator->fails()) {
         $errors = $validator->errors()->all();
        if (in_array('Data too long for column', $errors)) {
        return response()->json([
            'status' => 422,
            'message' => 'Image name is too long.',
            'errors' => $errors
        ], 422);
    }else{
        return response()->json(['status'=>422,'message'=>'Select Valid Image like(jpg,png,gif)','errors' => $validator->errors()], 422);
    }
    }
    else{
        $imageNames = [];
               $file =$request->file('images');

         if($request->file('images'))
         {
             \Log::info($request->all());
            foreach ($request->file('images') as $image) {
                // Store each image
             $imageName = $image->getClientOriginalName();
             // $imageName = time() . '.' . $image->getClientOriginalExtension(); 
                 $image->move("images",$imageName);
               
             $imageNames[] = $imageName;
       }
            $imageNamesString = implode(',', $imageNames);
            $description = $request->input('description');
            $visible = $request->input('visible');
           $image = new Image();
        $image->image = $imageNamesString;
        $image->visible= $visible;
        $image->description = $description;
        $image->user_id=Auth::user()->id;
        $image->save();
            return response()->json(['message' => 'Images uploaded successfully'], 200);
       } 

        return response()->json(['message' => 'No images found to upload'], 400);
    }
    }
    elseif ($request->has('files')) {
    // If files are present, validate them
    $validator = Validator::make($request->all(), [
        'files' =>'required',
       'files.*' => 'required|file|mimes:pdf,doc,docx|max:10248',
        'description' => 'string|nullable', // Optional description field
    ]);
   // If files are present, validate them
$validator = Validator::make($request->all(), [
    'files' => 'required',
    'files.*' => 'required|file|mimes:pdf,doc,docx|max:10248',
    'description' => 'string|nullable', // Optional description field
]);

if ($validator->fails()) {
    $errors = $validator->errors()->all();

    // Concatenate all error messages into a single string
    $errorString = implode(' ', $errors);

    // Check if the concatenated string contains "Data too long for column"
    if (strpos($errorString, 'Data too long for column') !== false) {
        return response()->json([
            'status' => 422,
            'message' => 'File name is too long.',
            'errors' => $errors
        ], 422);
    } else {
        return response()->json([
            'status' => 422,
            'message' => 'Files should only be supported pdf, doc, docx.',
            'errors' => $validator->errors()
        ], 422);
    }
}

    else{
            $fileNames = [];
            $file =$request->file('files');

         if($request->file('files'))
         {
             \Log::info($request->all());
            foreach ($request->file('files') as $file) {
                // Store each image
             $fileName = $file->getClientOriginalName();
             // $imageName = time() . '.' . $image->getClientOriginalExtension(); 
                 $file->move("files",$fileName);
               
             $fileNames[] = $fileName;
       }
            $fileNamesString = implode(',', $fileNames);
            $description = $request->input('description');
            $visible = $request->input('visible');
           $file = new Image();
        $file->file = $fileNamesString;
        $file->description = $description;
        $file->visible = $visible;
        $file->user_id = Auth::user()->id;
        $file->save();
            return response()->json(['message' => 'Files uploaded successfully'], 200);
       } 

        return response()->json(['message' => 'No Files found to upload'], 400);
    }
}
   elseif ($request->has('video')) {
    // If files are present, validate them
    $validator = Validator::make($request->all(), [
        'video' =>'required',
       'video.*' => 'required|file|mimes:mp4,mov,avi,wmv|max:1048576',
        'description' => 'string|nullable', // Optional description field
    ]);
    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => 'Video should be only supported mp4,mov,avi,wmv ',
            'errors' => $validator->errors()
        ], 422);
    }
    else{
            $videoNames = [];
            $video =$request->file('video');

         if($request->file('video'))
         {
             \Log::info($request->all());
            foreach ($request->file('video') as $video) {
                // Store each z
             $videoName = $file->getClientOriginalName();
             // $imageName = time() . '.' . $image->getClientOriginalExtension(); 
                 $video->move("video",$videoName);
               
             $videoNames[] = $videoName;
       }
            $videoNamesString = implode(',', $videoNames);
            $description = $request->input('description');
            $visible = $request->input('visible');
           $video = new Image();
        $video->video = $videoNamesString;
        $video->visible = $visible;
        $video->description = $description;
        $video->user_id = Auth::user()->id;
        $video->save();
            return response()->json(['message' => 'Video uploaded successfully'], 200);
       } 

        return response()->json(['message' => 'No Video found to upload'], 400);
    }
}




     else { 
         return response()->json(['message' => '  found to upload'], 400);      
    }
    
        }
         
}    

  

