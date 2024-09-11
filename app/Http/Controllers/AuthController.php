<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function handleAuthCallback(Request $request)
    {
        try {
            $idToken = $request->input('token');
            
            // Verify ID token using Google's tokeninfo endpoint
            $client = new Client();
            $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query' => ['id_token' => $idToken]
            ]);
    
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Invalid ID token');
            }
    
            $googleUser = json_decode($response->getBody(), true);
           
            if (!isset($googleUser['email'])) {
                throw new \Exception('Invalid token response structure');
            }

            if ($googleUser['aud'] !== config('services.google.client_id')) {
                throw new \Exception('Invalid token audience');
            }

            // Get avatar (profile picture) from Google and save it locally
            $avatarUrl = $googleUser['picture'] ?? '';
            $avatarFileName = '';

            if ($avatarUrl) {
                // Create a unique filename for the avatar
                $avatarFileName = uniqid() . '.jpg';
                $avatarPath = public_path('avatar/' . $avatarFileName);

                // Download and save the image to the public/avatar directory
                $client->get($avatarUrl, ['sink' => $avatarPath]);
            }

            // Create or update the user in the database
            $user = User::updateOrCreate(
                ['email' => $googleUser['email']],
                [
                    'firstname' => $googleUser['given_name'] ?? '',
                    'lastname' => $googleUser['family_name'] ?? '',
                    'avatar' => $avatarFileName,  // Save the filename, not the URL
                    'password' => bcrypt('dummy123'),  // Generate a dummy password
                    'gender' => "nothing",  // Placeholder, adjust as needed
                    'address' => "nothing",  // Placeholder, adjust as needed
                    'country' => "nothing",  // Placeholder, adjust as needed
                ]
            );

            // Generate JWT token for the user
            $jwtToken = JWTAuth::fromUser($user);
    
            return response()->json([
                'user' => $user,
                'access_token' => $jwtToken,  // Return generated JWT token
                'token_type' => 'Bearer',
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Google authentication failed. Please try again.'], 500);
        }
    }
}
