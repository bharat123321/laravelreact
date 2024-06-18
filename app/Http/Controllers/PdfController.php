<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image; // Replace with your actual model
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{  
    public function download($id)
    {
        // Fetch the item from the database
        $item = Image::findOrFail($id); // Replace Image with your model
        Log::info('Fetched item:', [$item]);
        
        // Determine if we have a file or an image
        $filePath = null;
        $fileLocation = '';
        $extension = '';
        
        if (!empty($item->file)) {
            $filePath = $item->file;
            $fileLocation = 'files';
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        } elseif (!empty($item->image)) {
            $filePath = $item->image;
            $fileLocation = 'images';
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        }
        
        Log::info('File path:', [$filePath]);
        Log::info('File extension:', [$extension]);
        
        if (empty($filePath)) {
            abort(404, 'No file or image found');
        }
        
        // If the file is an image, generate HTML and convert it to PDF
        if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            $imageNames = explode(',', $filePath); // Split comma-separated file paths
            Log::info('Image names:', $imageNames);
            $html = $this->generateHtml($imageNames);
            Log::info('Generated HTML:', [$html]);
            return $this->htmlToPdf($html);
        } else {
            // Serve the PDF file directly
            $contentTypes = [
                'pdf' => 'application/pdf',
                // Add more as needed
            ];
            $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
            
            return response()->download(
                public_path("{$fileLocation}/{$filePath}"),
                basename($filePath),
                ['Content-Type' => $contentType]
            );
        }
    }

    private function generateHtml($imageNames)
    {
        $html = '<html><head><title>PDF Document</title></head><body>';

        if (!empty($imageNames)) {
            foreach ($imageNames as $imageName) {
                // Ensure the imageName is trimmed and valid
                $imageName = trim($imageName);
                $imageUrl = asset('images/' . $imageName);
                
                Log::info('Image URL:', [$imageUrl]);
                
                // Concatenate the HTML string with the image tag using asset() function
                $html .= '<div><img src="' . $imageUrl . '" alt="Image" style="width: 100%;"/></div><br/>';
            }
        } else {
            // Handle the case where no image names are provided
            $html .= '<div>No images found</div>';
        }

        $html .= '</body></html>';

        return $html;
    }

    private function htmlToPdf($htmlContent)
    {
        // Initialize Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Allow fetching remote content
        $dompdf = new Dompdf($options);
        
        // Load HTML content
        $dompdf->loadHtml($htmlContent);
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render the HTML as PDF
        $dompdf->render();
        
        // Output the generated PDF to browser
        return $dompdf->stream('document.pdf', ['Attachment' => false]);
    }

    public function getFile($filename)
    {
        $path = public_path('/files/' . $filename);
        Log::info('File path:', [$path]);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        Log::info('File path exists:', [$path]);
        
        $response = Response::file($path);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
