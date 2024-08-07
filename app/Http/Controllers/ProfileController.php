<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image; 
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->only(['Fetchuserfile']);
    }
    public function Fetchuserfile()
    {
        $fetchdata = Image::where('user_id',Auth::user()->id)->get();
        \Log::info($fetchdata);
        return response()->json(['data'=>$fetchdata]);
    }
}
