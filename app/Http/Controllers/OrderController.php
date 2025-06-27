<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\OrderCancelledNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification; // Correct facade
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
/////////////////// logged in user's hstory 

public function getUserOrders(Request $request)
    {
        // Validate the request
        $request->validate([
            'phone' => 'required|string'
        ]);

        // Get the phone number from the request
        $phone = $request->query('phone');

        // Fetch orders with their related data
        $orders = Order::with(['order_items.product', 'payment'])
            ->where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return the orders as JSON
        return response()->json($orders);
    }
 public function cancel(Request $request, Order $order)
{
    // Check if the order belongs to the user (or admin)
    if ($order->phone !== $request->phone /* && !$request->user()->is_admin */) {
        return response()->json([
            'success' => false,
            'message' => 'You are not authorized to cancel this order.',
        ], 403);
    }

    // Check if the order can be cancelled (only pending/processing)
    if (in_array($order->status, ['completed', 'processing'])) {
        return response()->json([
            'success' => false,
            'message' => 'Order cannot be cancelled at this stage.',
        ], 400);
    }

    // Update order status
    $order->update([
        'status' => 'cancelled',
        'cancelled_at' => now(),
        'cancelled_by' => $request->phone,
    ]);

    // Restore product stock
    foreach ($order->items as $item) {
        $item->product()->increment('qte', $item->quantity);
    }

     // Notify admins
    $admins = User::where('role', "admin")->get();
    if ($admins->isNotEmpty()) {
        Notification::send($admins, new OrderCancelledNotification($order));
    }

    return response()->json([
        'success' => true,
        'message' => 'Order cancelled successfully.',
        'order' => $order->fresh(),
    ]);
}
}

