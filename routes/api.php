<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route for testing Sanctum authentication
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Default route (you might want to remove this if not needed)
Route::get("/", function () {
    return view("home");
});
use App\Http\Controllers\DataController;

Route::middleware('auth:sanctum')->get('/data', [DataController::class, 'getData']);

// Authentication routes
Route::post("/login", [HomeController::class, "login"])->name('login');
Route::post("/register", [HomeController::class, "register"]);

// Authenticated routes group
Route::group(['middleware'=>'api'], function () {
    Route::post('/upload', [HomeController::class, 'upload']);
    Route::post('/classcode',[HomeController::class,'classCode']);
    Route::post('/joincode',[IndexController::class,'Joincode']);
    Route::get('/fetchUser',[IndexController::class,'FetchUser']);
     Route::get('/fetchcreateddata',[IndexController::class,'FetchCreatedGroup']);
      Route::get('/fetchselectednots',[IndexController::class,'FetchSelectedNotes']);
    Route::get('/fetchdata',[IndexController::class,'Fetchdata']);
    Route::get('/fetchupload/{code}',[IndexController::class,"FetchUpload"]);
    Route::post("/home", [IndexController::class, "index"]);
    Route::get("/userverify", [IndexController::class, "UserVerify"]);
    Route::post("/profilepic",[IndexController::class,"ProfileUpdate"]);
    // Add more authenticated routes here if needed
});

 
  