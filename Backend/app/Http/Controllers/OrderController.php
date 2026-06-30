<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.product.category')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|string|email|max:255',
            'address' => 'required|string',
            'alt_address' => 'nullable|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string|max:20',
            'payment_method' => 'required|string',
            'shipping_cost' => 'required|integer|min:0',
        ]);

        $userId = $request->user()->id;

        // Fetch cart items
        $cartItems = Cart::with('product')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong. Tidak dapat melakukan pesanan.'], 400);
        }

        // Calculate subtotal price
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->product->price * $item->quantity;
        }

        $totalPrice = $subtotal + $request->shipping_cost;

        // Generate unique 6-digit invoice number
        $invoiceNum = str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT);
        while (Order::where('invoice_number', $invoiceNum)->exists()) {
            $invoiceNum = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        }

        // Process checkout in a database transaction
        try {
            $order = DB::transaction(function () use ($userId, $request, $totalPrice, $cartItems, $invoiceNum) {
                // Create order
                $order = Order::create([
                    'user_id' => $userId,
                    'customer_name' => $request->first_name . ' ' . $request->last_name,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'alt_address' => $request->alt_address,
                    'province' => $request->province,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'invoice_number' => $invoiceNum,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'unpaid',
                    'shipping_cost' => $request->shipping_cost,
                    'total_price' => $totalPrice,
                    'status' => 'Perlu di Cek', // Default status matching screen
                ]);

                // Create order items & delete cart items
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'notes' => $item->notes,
                    ]);

                    $item->delete();
                }

                return $order;
            });

            return response()->json($order->load('items.product.category'), 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
