<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ViewBookController;
use App\Http\Controllers\ProfileController;
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
//google Auth
Route::get('auth/google', [AuthController::class, 'redirectToAuth']);
Route::post('auth/google/callback', [AuthController::class, 'handleAuthCallback']);
// Authentication routes
Route::post("/login", [HomeController::class, "login"])->name('login');
Route::post("/register", [HomeController::class, "register"]);
Route::get("fetchbookcollection",[WelcomeController::class,"FetchCollectionbook"]);
Route::get('/fetchpublicdata',[WelcomeController::class,'fetchpublicdata']);
Route::get('/fetchbookid/{id}',[WelcomeController::class,'Viewbook']);
Route::get('/lookup/{word}', [DictionaryController::class, 'lookupWord']);
Route::get('/fetchsearchdata/{data}', [SearchController::class, 'Fetchsearchdata']);
// Authenticated routes group
Route::group(['middleware'=>'api'], function () {
    Route::post('/upload', [HomeController::class, 'upload']);
    Route::post('/classcode',[HomeController::class,'classCode']);
    Route::post('/joincode',[IndexController::class,'Joincode']);
    Route::get('/fetchUser',[IndexController::class,'FetchUser']);
    Route::get('/fetchalldata',[IndexController::class,'Fetchalldata']);
    Route::get('/fetchcreateddata',[IndexController::class,'FetchCreatedGroup']);
    Route::get('/fetchselectednots',[IndexController::class,'FetchSelectedNotes']);
    Route::get('/fetchdata',[IndexController::class,'Fetchdata']);
    Route::get('/fetchupload/{code}',[IndexController::class,"FetchUpload"]);
    Route::post("/home", [IndexController::class, "index"]);
    Route::get('/downloadimage/{id}',[IndexController::class, "DownloadImage"]);
    Route::post('/convertimgtopdf/{id}', [IndexController::class, 'convertImageToPdf']);
    Route::get("/userverify", [IndexController::class, "UserVerify"]);
    Route::post("/profilepic",[IndexController::class,"ProfileUpdate"]);
    Route::put('/images/{id}/privacy', [IndexController::class, 'updatePrivacy']);
    Route::delete('/images/{id}', [IndexController::class, 'destroy']);
    Route::get('/convertimgtopdf/{id}', [PdfController::class, 'preview']);
    Route::get('/downloadpdf/{id}', [PdfController::class, 'download']);
    Route::get('/files/{filename}', [PdfController::class, 'getFile']);
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/searchdetail/{id}',[SearchController::class,'searchdetail']);
    Route::post('/bookmark/',[ViewBookController::class,'Bookmark']);
    Route::post('/like/',[ViewBookController::class,'Like']);
    Route::get('/fetchbookdetail/{id}',[ViewBookController::class,'Viewbook']);
    Route::get('/fetchuserfile',[ProfileController::class,'Fetchuserfile']);
});

 
  