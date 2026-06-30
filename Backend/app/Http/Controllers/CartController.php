<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity;
        $notes = $request->notes;

        // Check if item already exists in cart with same product and same notes
        $existingCartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('notes', $notes)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->quantity += $quantity;
            $existingCartItem->save();
            return response()->json($existingCartItem->load('product.category'));
        }

        $cartItem = Cart::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'notes' => $notes,
        ]);

        return response()->json($cartItem->load('product.category'), 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $cartItem = Cart::where('user_id', $request->user()->id)->find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Item keranjang tidak ditemukan.'], 404);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'notes' => $request->notes,
        ]);

        return response()->json($cartItem->load('product.category'));
    }

    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)->find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Item keranjang tidak ditemukan.'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item berhasil dihapus dari keranjang.']);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Keranjang berhasil dikosongkan.']);
    }
}
