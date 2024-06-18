<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;  

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->query('term');
        
        // Perform the search
        $results = Image::where('topic', 'LIKE', '%' . $term . '%')->get();
        \Log::info("heeeeeeeeee");
        return response()->json(["resultdata"=>$results]);
    }
}
