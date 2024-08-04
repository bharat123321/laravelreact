<?php      
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class AuthController extends Controller
{
   
    public function handleAuthCallback(Request $request)
    {
        try {
            $idToken = $request->input('token');
            Log::info('Received ID Token: ' . $idToken);
    
            // Verify ID token using Google's tokeninfo endpoint
            $client = new Client();
            $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query' => ['id_token' => $idToken]
            ]);
    
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Invalid ID token');
            }
    
            $googleUser = json_decode($response->getBody(), true);
            Log::info('Google Token Info Retrieved: ', $googleUser);
    
            // Find or create the user in the local database
            $user = User::updateOrCreate(
                ['email' => $googleUser['email']],
                [
                    'firstname' => $googleUser['given_name'] ?? '',
                    'lastname' => $googleUser['family_name'] ?? '',
                    'avatar' => $googleUser['picture'] ?? '',
                    'password' => bcrypt('dummy123'), // Store a valid password hash
                ]
            );
    
            Log::info('User Retrieved or Created: ', $user->toArray());
    
            // Generate API token for the user
            $apiToken = $user->createToken('authToken')->plainTextToken;
            Log::info($apiToken);
            return response()->json([
                'user' => $user,
                'access_token' => $apiToken,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return response()->json(['error' => 'Google authentication failed. Please try again.'], 500);
        }
    }
    
}
