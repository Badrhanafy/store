<?php
// app/Http/Controllers/API/ImageController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
  public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
                  . '-' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store directly in public/sliders
        $path = $file->move(public_path('sliders'), $filename);
        
        return response()->json([
            'url' => url("sliders/$filename"),  // Generates: http://yourdomain.com/sliders/filename.jpg
            'path' => "sliders/$filename"       // Returns: sliders/filename.jpg
        ]);
    }

    return response()->json(['error' => 'No image uploaded'], 400);
}
}