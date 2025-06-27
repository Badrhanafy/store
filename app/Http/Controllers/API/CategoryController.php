<?php
// app/Http/Controllers/API/CategoryController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {
        //$categories = Category::active()->ordered()->get();
        $categories = Product::select('category')->distinct()->pluck('category');

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'link' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|string',
            'link' => 'sometimes|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}