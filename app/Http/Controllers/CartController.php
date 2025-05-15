<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $cart = Cart::with('item')
            ->where('user_id', Auth::id())
            ->get();

        $total = $cart->sum(function ($item) {
            return $item->quantity * $item->item->price;
        });

        return response()->json([
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Lock the item row to prevent concurrent modifications
                $item = Item::lockForUpdate()->findOrFail($request->item_id);

                // Check if item is already in cart
                $cartItem = Cart::where('user_id', Auth::id())
                    ->where('item_id', $request->item_id)
                    ->first();

                if ($cartItem) {
                    // Check if new total quantity exceeds stock
                    if (($cartItem->quantity + $request->quantity) > $item->stock) {
                        return response()->json([
                            'message' => 'Not enough stock available'
                        ], 422);
                    }

                    $cartItem->quantity += $request->quantity;
                    $cartItem->save();
                } else {
                    // Check if quantity exceeds stock
                    if ($request->quantity > $item->stock) {
                        return response()->json([
                            'message' => 'Not enough stock available'
                        ], 422);
                    }

                    Cart::create([
                        'user_id' => Auth::id(),
                        'item_id' => $request->item_id,
                        'quantity' => $request->quantity
                    ]);
                }

                return response()->json([
                    'message' => 'Item added to cart successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            return DB::transaction(function () use ($request, $cart) {
                // Lock the cart and item rows
                $cart = Cart::lockForUpdate()
                    ->where('id', $cart->id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                $item = Item::lockForUpdate()->findOrFail($cart->item_id);

                if ($request->quantity > $item->stock) {
                    return response()->json([
                        'message' => 'Not enough stock available'
                    ], 422);
                }

                $cart->quantity = $request->quantity;
                $cart->save();

                return response()->json([
                    'message' => 'Cart updated successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Cart $cart)
    {
        try {
            return DB::transaction(function () use ($cart) {
                $cart = Cart::lockForUpdate()
                    ->where('id', $cart->id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                $cart->delete();

                return response()->json([
                    'message' => 'Item removed from cart successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clear()
    {
        try {
            return DB::transaction(function () {
                Cart::where('user_id', Auth::id())->delete();

                return response()->json([
                    'message' => 'Cart cleared successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 