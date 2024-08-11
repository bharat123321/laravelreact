<?php
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->stateless()->redirect();
});

Route::get('/auth/google/callback', function (Request $request) {
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Find or create the user in your database
    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            // Add any other user data you need to store
        ]
    );

    // Generate an API token for the user
    $token = $user->createToken('authToken')->plainTextToken;

    // Respond with the token
    return response()->json([
        'user' => $user,
        'accessToken' => $token,
    ]);
});
