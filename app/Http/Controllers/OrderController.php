<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OrderController extends Controller
{
    public function index() {
        return Order::with(['orderItems.product', 'payment'])->get();
    }

public function store(Request $request)
{
    $validatedData = $request->validate([
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'size' => 'required|string',
        'color' => 'nullable|string',
        'payment_method' => 'required|string|in:cash,online',
    ]);

    $product = Product::findOrFail($validatedData['product_id']);

    // Validate size and color against product options
    if (!in_array($validatedData['size'], $product->sizes)) {
        return response()->json([
            'message' => 'Invalid size selected for this product'
            
        ], 422);
    }

    if ($validatedData['color'] && !in_array($validatedData['color'], $product->colors)) {
        return response()->json([
            'message' => 'Invalid color selected for this product',
            'available_colors' => $product->colors
        ], 422);
    }

    // Calculate total price
    $totalPrice = $product->price * $validatedData['quantity'];

    $order = Order::create([
        'user_id' => auth()->id(), // Store user ID if authenticated
        'customer_name' => $validatedData['customer_name'],
        'phone' => $validatedData['phone'],
        'address' => $validatedData['address'],
        'status' => 'pending',
        'total_price' => $totalPrice,
    ]);
      $product->qte = $product->qte - $validatedData['quantity'];
      $product->save();
    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => $validatedData['quantity'],
        'price' => $product->price,
        'size' => $validatedData['size'],
        'color' => $validatedData['color'] ?? null,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'payment_method' => $validatedData['payment_method'],
        'payment_status' => $validatedData['payment_method'] === 'online' ? 'processing' : 'pending',
    ]);

    // Load relationships for the response
    $order->load(['orderItems.product', 'payment']);

    return response()->json([
        'message' => 'Order created successfully!',
        'order' => $order
    ], 201);
}


    public function show(Order $order) {
        return $order->load(['orderItems.product', 'payment']);
    }

    public function update(Request $request, Order $order) {
        $order->update($request->all());
        return $order;
    }

    public function destroy(Order $order) {
        $order->delete();
        return response()->noContent();
    }









    /////////////////// plusieur items dans une commande

    // كنشأ order جديد
public function PanierOrder(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'address' => 'required|string',
        'total_price' => 'required|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.size' => 'required|string',
        'items.*.color' => 'required|string',
    ]);

    try {
        // Start database transaction
        DB::beginTransaction();

        // Create the order
        $order = Order::create([
            'user_id' => $request->user_id ?? null, // Use null if user_id not provided
            'customer_name' => $validatedData['customer_name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'status' => 'pending',
            'total_price' => $validatedData['total_price'],
        ]);

        // Create order items
        foreach ($validatedData['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'size' => $item['size'],
                'color' => $item['color'], // Fixed from $item->size to $item['color']
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Optional: Update product stock if you have inventory management
            // Product::where('id', $item['product_id'])->decrement('qte', $item['quantity']);
        }

        // Commit transaction
        DB::commit();

        return response()->json([
            'message' => 'Order placed successfully',
            'order_id' => $order->id,
            'total' => $order->total_price
        ], 201);

    } catch (\Exception $e) {
        // Rollback transaction on error
        DB::rollBack();
        
        return response()->json([
            'message' => 'Failed to place order',
            'error' => $e->getMessage()
        ], 500);
    }
}

}

