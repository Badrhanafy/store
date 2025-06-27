<?php
// app/Http/Controllers/API/SlideController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index()
    {
        $slides = Slide::active()->ordered()->get();
        return response()->json($slides);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'cta' => 'required|string|max:50',
            'image' => 'required|string',
            'link' => 'required|string',
            'bg_color' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $slide = Slide::create($validated);
        return response()->json($slide, 201);
    }

    public function show(Slide $slide)
    {
        return response()->json($slide);
    }

    public function update(Request $request, Slide $slide)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'sometimes|string|max:255',
            'cta' => 'sometimes|string|max:50',
            'image' => 'sometimes|string',
            'link' => 'sometimes|string',
            'bg_color' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $slide->update($validated);
        return response()->json($slide);
    }

    public function destroy(Slide $slide)
    {
        $slide->delete();
        return response()->json(null, 204);
    }
}
