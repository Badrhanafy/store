<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
public function index()
{
    return Product::with('impressions')
                 ->withAvg('impressions', 'rating') // هادي تحسب متوسط rating
                 ->get();
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
        'category' => 'required|string',
        'sizes' => 'required|string', // Changed from array to string
        'colors' => 'required|string', // Changed from array to string
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Handle Image Upload
    if ($request->hasFile('image')) {
        $imageName = time().'.'.$request->image->extension();  
        $request->image->move(public_path('images'), $imageName);
        $validated['image'] = 'images/' . $imageName;
    }

    // Decode the JSON strings back to arrays
    $validated['sizes'] = json_decode($validated['sizes'], true);
    $validated['colors'] = json_decode($validated['colors'], true);

    // Create product
    $product = Product::create($validated);

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product
    ], 201);
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
    public function GetImpressions($id){
        $product = Product::find($id);
        $impressions= $product->Impressions()->get();
        return $impressions;
    }

    ///////////////// delete image

  public function deleteImage(Request $request, $productId, $imageId)
{
    try {
        // Validate the product exists
        $product = Product::findOrFail($productId);
        
        // Find the image (assuming you have a ProductImage model)
        $image = ProductImage::where('product_id', $productId)
                            ->where('id', $imageId)
                            ->firstOrFail();
        
        // Get the full image path
        $imagePath = public_path($image->image_path);
        
        // Delete the file from storage
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        // Delete the record from database
        $image->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Image or product not found'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting image: ' . $e->getMessage()
        ], 500);
    }
}
/////////////////////////////////////  New Arrivals 
public function newArrivals(Request $request)
{
    $products = Product::newArrivals()
        ->select(['id', 'title', 'description', 'price', 'qte', 'category', 'sizes', 'colors', 'image', 'created_at'])
        ->get();

    $mappedProducts = $products->map(function ($product) {
        return [
            'id' => $product->id,
            'title' => $product->title,
            'description' => $product->description,
            'price' => $product->price,
            'quantity' => $product->qte,
            'category' => $product->category,
            'sizes' => $product->sizes,
            'colors' => $product->colors,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
            'created_at' => $product->created_at,
            'is_new' => true
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $mappedProducts,
        'message' => $products->isEmpty() 
            ? 'No new arrivals found' 
            : 'New arrivals retrieved successfully'
    ]);
}
}
