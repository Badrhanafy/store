<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Carousel extends Controller
{
    public function index()
    {
        $slides = Slide::where('is_active', true)
                      ->orderBy('order')
                      ->get()
                      ->map(function ($slide) {
                          $slide->image_url = asset('storage/slides/' . $slide->image);
                          return $slide;
                      });
        
        return response()->json($slides);
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'subtitle' => 'required|string|max:255',
        'cta_text' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Ensure image is required
        'bg_color' => 'string',
        'order' => 'integer',
        'is_active' => 'boolean'
    ]);

    // Handle file upload
    $imagePath = $request->file('image')->store('slides', 'public');
    $validated['image'] = $imagePath;

    $slide = Slide::create($validated);
    
    return response()->json([
        'slide' => $slide,
        'image_url' => asset("storage/{$imagePath}")
    ], 201);
}

    // Add other CRUD methods as needed
}