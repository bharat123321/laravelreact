<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DictionaryController extends Controller
{
    public function lookupWord($word)
    {
        try {
            $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/$word");
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['message' => 'Word not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }
}
