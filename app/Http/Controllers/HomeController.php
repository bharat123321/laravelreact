<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user'=>auth()->user()
        ]);
    }


  
}
