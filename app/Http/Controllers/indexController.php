<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\classcode;
use App\Models\joincode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Image;
use PDF;
use ZipArchive;
use Illuminate\Support\Facades\DB;
class indexController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        echo "index";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function classCode(Request $request){
          \Log::info($request->all());
         $codes =$request->input('userCode');
         $code = new classcode();
         $code->class_code = $codes;
         $code->user_id = Auth::user()->id;
         $code->save();
         return response()->json(['Success'=>"SuccessFully created"]);
    }
    public function Joincode(Request $request)
    {
        
        $code = $request->input('userCode');
      $getuserid = DB::table('users')
        ->join('classcodes', 'users.id', '=', 'classcodes.user_id')
        ->where('class_code', $code) ->pluck('users.id')->first();
          
        $checkcode = classcode::where('class_code',$code)->first();
        $checkAuthid = joincode::where('join_id',Auth::user()->id)->where('classscode',$code)->first();
     
        if($checkcode){
            if($getuserid == Auth::user()->id || $checkAuthid )
            {
                return response()->json(['error'=>'You have already Joined']);
            }
            else{ 
             $joincode = new joincode();
             $joincode->classscode = $code;
             $joincode->join_id = Auth::user()->id;
             $joincode->user_id = $getuserid;
             $joincode->save();

            return response()->json(["message" => "Class joined successfully", "class_code" => $checkcode]);
        }
    }
        else{
            $store = "error i got";
            return response()->json(["error" => "Invalid class code"]);
        }
        
    }
    public function FetchUser(){
        if(Auth::check())
        {
        $store = DB::table('users')->where('classcode_status',1)->first();
        $conn = DB::table('joincodes')->where('join_id',Auth::user()->id)->get();
        $subjectName = DB::table('classcodes')
                    ->select('classcodes.subjectname','classcodes.class_code')
                    ->join('joincodes', 'classcodes.class_code', '=', 'joincodes.classscode')
                    ->where('joincodes.join_id', '=', Auth::user()->id)
                    ->get();

        return response()->json(['check'=>$subjectName]);
     }
     return response()->json(['status'=>401]);
}

  
public function FetchCreatedGroup()
{
    if (!Auth::check()) {
        return response()->json(['status' => 401]);
    }

    // Fetch records using Eloquent
    $subjectName = classcode::join('users','classcodes.user_id','=','users.id')->where('user_id', Auth::user()->id)->get();

    // Map over the collection to format the date
    $subjectName = $subjectName->map(function ($classcode) {
        $classcode->formatted_date = $classcode->formattedCreatedDate();
        return $classcode;
    });

    return response()->json(['fetchdata' => $subjectName]);
}


 
  public function Fetchdata() {
    $fetchData = Image::join('users', 'images.user_id', '=', 'users.id')
        ->where('images.visible', 0)
        ->select('images.*', 'users.firstname', 'users.avatar')
        ->orderByDesc('images.created_at')
        ->get()
        ->map(function ($image) {
            $image->formatted_date = $image->formattedCreatedDate();
            return $image;
        });

    \Log::info($fetchData);

    return response()->json(['data' => $fetchData]);
}


 public function FetchUpload($code)
{
    if (Auth::check()) {
        
             // Fetch records using Eloquent
    $subjectName = classcode::join('users','classcodes.user_id','=','users.id')->where('classcodes.class_code', '=', $code)->get();

    // Map over the collection to format the date
    $subjectName = $subjectName->map(function ($classcode) {
        $classcode->formatted_date = $classcode->formattedCreatedDate();
        return $classcode;
    });

        return response()->json(['checks' => $subjectName]);
    }
    return response()->json(['status' => 401]);
}


   public function ProfileUpdate(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'images' => 'required|array',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json(['status' => 422, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }

    // Handle image upload
    if ($request->hasFile('images')) {
        $images = $request->file('images');
 

        foreach ($images as $image) {
            $imageName = $image->getClientOriginalName();
            $image->move("avatar", $imageName);
             
        }

        // Update user's avatar with the image name
        $user = User::findOrFail(Auth::user()->id);
        $user->avatar = $imageName;
        $user->save();

        return response()->json(['message' => 'Images uploaded successfully'], 200);
    }

    return response()->json(['status' => 400, 'message' => 'No images provided'], 400);
}


    public function UserVerify(){
        
        if(Auth::check())
        {
            return response()->json(['status'=>200]);
        }
        
            return response()->json(['status'=>401]);
        
    }
   public function DownloadImage($id)
{
    try {
        $images = Image::where('id', $id)->get();

        if ($images->isEmpty()) {
            return response()->json(['error' => 'Images not found'], 404);
        }
           
        $imageData = $images->map(function ($image) {
            return ['filename' => $image->image];
        });
            
        return response()->json(['images' => $imageData], 200);
    } catch (\Exception $e) {
        \Log::error("Error fetching images: " . $e->getMessage());
        return response()->json(['error' => 'Error fetching images'], 500);
    }
}



     
         public function convertImageToPdf($id)
    {
        try {
             $image = Image::find($id);
        \Log::info($image);
        if (!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }

            return response()->json(['pdf_url' => $image]);
        } catch (\Exception $e) {
           \Log::error("Error creating PDF: " . $e->getMessage());
            return response()->json(['error' => 'Error creating PDF'], 500);
        }
    }
     public function convertDocxToPdf(Request $request)
    {
        // Store the uploaded DOCX file
        $file = $request->file('file');
        $filePath = $file->storeAs('uploads', $file->getClientOriginalName());

        // Convert DOCX to PDF
        $transformDoc = new TransformDoc();
        $transformDoc->transformDocument(storage_path('app/' . $filePath), storage_path('app/output/' . $file->getClientOriginalName() . '.pdf'), 'pdf');

        // Return the converted PDF file
        return response()->file(storage_path('app/output/' . $file->getClientOriginalName() . '.pdf'));
    }

    public function convertXslToPdf(Request $request)
    {
        // Store the uploaded XSL file
        $file = $request->file('file');
        $filePath = $file->storeAs('uploads', $file->getClientOriginalName());

        // Load the XSL stylesheet
        $xslProcessor = new XSLTProcessor();
        $xslProcessor->importStyleSheet(DOMDocument::load(storage_path('app/' . $filePath)));

        // Transform XSL to PDF (implementation depends on your XSL transformation logic)

        // Return the converted PDF file
        return response()->file($pdfFilePath);
    }
}
