<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->user()->role->slug === 'client') {
            $query->where('client_id', $request->user()->id);
        }

        return response()->json([
            'orders' => $query->with(['client', 'manager'])->paginate(20),
        ]);
    }

    public function show($id)
    {
        $order = Order::find($id)->load(['client', 'manager']);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->client_id !== $request->user()->id && $request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['order' => $order]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:blocks,land,merchandise,service',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'unit_price' => 'required|numeric',
            'total_price' => 'required|numeric',
            'delivery_address' => 'nullable|string',
            'requested_date' => 'nullable|date',
        ]);

        $validated['client_id'] = $request->user()->id;
        $validated['order_number'] = 'ORD-' . strtoupper(uniqid());
        $validated['status'] = 'pending';

        $order = Order::create($validated);

        return response()->json(['message' => 'Order created', 'order' => $order], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($request->user()->role->slug !== 'manager' && $request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,in-delivery,delivered,cancelled',
            'delivery_date' => 'nullable|date',
        ]);

        $order->update($validated);

        return response()->json(['message' => 'Order updated', 'order' => $order]);
    }
}
