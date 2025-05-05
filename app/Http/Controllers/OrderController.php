<?php

namespace App\Http\Controllers;
use App\Models\Order\OrderItem;
class OrderController extends Controller
{
    public function index() {
        return Order::with(['orderItems.product', 'payment'])->get();
    }

    public function store(Request $request) {
        $order = Order::create($request->only(['user_id', 'name', 'phone', 'address', 'status', 'total_price']));

        foreach ($request->order_items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return $order->load('orderItems');
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

