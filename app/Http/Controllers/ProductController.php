<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    public function index() {
        return Product::all();
    }
    
    public function addImages(Request $request, $productId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
    
                // نحط الصورة فـ public/product_images
                $image->move(public_path('product_images'), $imageName);
    
                // نسجل المسار فـ database
                ProductImage::create([
                    'product_id' => $productId,
                    'image_path' => 'product_images/' . $imageName, // باش تعرضها بسهولة
                ]);
            }
    
            return response()->json(['message' => 'Images added and saved successfully!']);
        }
    
        return response()->json(['message' => 'No images uploaded'], 400);
    }
    
    
public function getImages($id)
{
    $product = Product::with('images')->findOrFail($id);

    return response()->json([
        
        'images' => $product->images
    ]);
}   


    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'qte' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $validated['image'] = 'images/' . $imageName; // Save relative path
        }
    
        // Create product
        $product = Product::create($validated);
    
        return response()->json($product, 201);
    }

    public function show(Product $product) {
        return $product;
    }

    public function update(Request $request, Product $product) {
        $product->update($request->all());
        return $product;
    }

    public function destroy(Product $product) {
        $product->delete();
        return response()->noContent();
    }
}
