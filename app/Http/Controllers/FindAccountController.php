<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SentCodetoEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
class FindAccountController extends Controller
{
    public function FindAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where("email", $email)->first();

        if ($user) {
            return response()->json(['user' => $user, 'status' => 200]);
        }

        return response()->json(['message' => 'User not found', 'status' => 404]);
    }


 public function SendCode(Request $request)
{
    $email = $request->email;
    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json(['status' => 404, 'message' => 'User not found']);
    }

    $existingCode = DB::table('password_reset_tokens')
        ->where('email', $email)
        ->where('expires_at', '>', now())
        ->first();

    if ($existingCode) {
        return response()->json(['status' => 200, 'message' => 'A code has already been sent to your email. Please wait a few minutes before requesting a new one.']);
    }

    // Generate a new 6-digit code
    $code = random_int(100000, 999999);

    // Store the code in the database with an expiration time of 10 minutes
    DB::table('password_reset_tokens')->insert([
        'email' => $email,
        'token' => $code,
        'created_at' => now(),
        'expires_at' => now()->addMinutes(10) // Code expires in 10 minutes
    ]);

    // Send the code via email using custome functionality 
    Mail::to($email)->send(new SentCodetoEmail($code));

    return response()->json(['status' => 200, 'message' => 'Code sent to your email']);
}


}
