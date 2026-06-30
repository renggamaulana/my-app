<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        // Count of orders that are not completed (pending check or shipping)
        $newOrdersCount = Order::whereIn('status', ['Perlu di Cek', 'Perlu dikirim', 'pending'])->count();
        
        // Sum of revenue (paid orders or total orders)
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_price');
        
        // Count of users with 'user' role
        $usersCount = User::where('role', 'user')->count();

        return response()->json([
            'new_orders' => $newOrdersCount,
            'revenue' => $totalRevenue,
            'users' => $usersCount
        ]);
    }

    public function customers()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        
        // Map address from their latest order or default address
        $mappedUsers = $users->map(function ($u, $index) {
            $latestOrder = Order::where('user_id', $u->id)->latest()->first();
            $address = $latestOrder 
                ? ($latestOrder->address . ', ' . $latestOrder->city . ' ' . $latestOrder->postal_code) 
                : 'Jl. Cilandak Barat No. 45, Jakarta Barat 171231';
            
            return [
                'no' => $index + 1,
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'address' => $address,
            ];
        });

        return response()->json($mappedUsers);
    }

    public function orders()
    {
        $orders = Order::with('items.product.category')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'payment_status' => 'nullable|string',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        $order->status = $request->status;
        if ($request->has('payment_status')) {
            $order->payment_status = $request->payment_status;
        }
        $order->save();

        return response()->json($order->load('items.product.category'));
    }

    public function destroyOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Pesanan berhasil dihapus.']);
    }
}
