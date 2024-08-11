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
        return response()->json(["resultdata"=>$results]);
    }
    public function searchdetail($id)
    {
       $results = Image::where('id', $id)->get();
        return response()->json(["searchdetail"=>$results]);
    }
    public function Fetchsearchdata($data)
    {
        $result = Image::where('topic', 'LIKE', '%' . $data . '%')
                       ->orWhere('category', 'LIKE', '%' . $data . '%')  
                       ->get();

        \Log::info($result); // Log the results for debugging

        return response()->json($result); // Return the result as JSON
    }
    public function FetchSearcheddata($data)
    {
        $result = Image::where('topic', 'LIKE', '%' . $data . '%')
                       ->orWhere('category', 'LIKE', '%' . $data . '%')  
                       ->get();

        \Log::info($result);  
        return response()->json($result);  
    }

}
