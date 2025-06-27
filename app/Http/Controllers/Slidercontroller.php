<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bg_color' => 'string',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/slides');
            $validated['image'] = basename($imagePath);
        }

        $slide = Slide::create($validated);
        $slide->image_url = asset('storage/slides/' . $slide->image);

        return response()->json($slide, 201);
    }

    
}