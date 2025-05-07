<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {
        return Order::with(['orderItems.product', 'payment'])->get();
    }

    public function store(Request $request)
{
    $order = Order::create([
        'user_id' => null, // بلا حساب
        'customer_name' =>  $request->customer_name||'Guest User',
        'phone' => $request->phone,
        'address' => $request->address,
        'status' => 'pending',
        'total_price' => 0, // غادي نحسبوه من بعد
    ]);

    $product = Product::findOrFail($request->product_id);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => $request->quantity,
        'price' => $product->price,
        'taille' => $request->taille,
    ]);

    // نحسب المجموع
    $order->total_price = $product->price * $request->quantity;
    $order->save();

    // Payment record
    Payment::create([
        'order_id' => $order->id,
        'payment_method' => $request->payment_method,
        'payment_status' => 'pending',
    ]);

    return response()->json(['message' => 'Order created successfully!']);
}
public function storeCartOrder(Request $request)
{
    $order = Order::create([
        'user_id' => null, 
        'customer_name' => $request->customer_name || 'Guest User',
        'phone' => $request->phone,
        'address' => $request->address,
        'status' => 'pending',
        'total_price' => 0,
    ]);

    $totalPrice = 0;

    foreach ($request->products as $item) {
        $product = Product::findOrFail($item['id']);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'price' => $product->price,
            'taille' => $item['taille'] ?? null,
        ]);

        $totalPrice += $product->price * $item['quantity'];
    }

    $order->total_price = $totalPrice;
    $order->save();

    Payment::create([
        'order_id' => $order->id,
        'payment_method' => $request->payment_method,
        'payment_status' => 'pending',
    ]);

    return response()->json(['message' => 'Order from cart created successfully!']);
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

}

