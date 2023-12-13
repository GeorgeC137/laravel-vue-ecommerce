<?php

namespace App\Http\Helpers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class Cart
{
    // Display the number of items available in the cart
    public static function getCartItemsCount(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return CartItem::where('user_id', $user->id)->sum('quantity');
        } else {
            $cartItems = self::getCookieCartItems();

            return array_reduce(
                $cartItems,
                fn($carry, $item) => $carry + $item['quantity'], 0
            );
        }
    }

    // Returns an array of cart items user have. If user is authorized, cart items are from database otherwise calls getCookieCartItems()
    public static function getCartItems(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return CartItem::where('user_id', $user->id)->get()->map(
                fn($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity]
            );
        } else {
            return self::getCookieCartItems();
        }
    }

    // Returns the cart items user has in cookie
    public static function getCookieCartItems(Request $request)
    {
        return json_decode($request->cookie('cart_items', '[]'), true);
    }

    public static function getCountFromItems($cartItems)
    {
        return array_reduce(
            $cartItems,
            fn($carry, $item) => $carry + $item['quantity'], 0
        );
    }

    // Moves the cart items from cookie into database after user registration
    public static function moveCartItemsIntoDb(Request $request)
    {
        $cartItems = self::getCookieCartItems();
        $dbCartItems = CartItem::where('user_id', $request->user()->id)->get()->keyBy('product_id');
        $newCartItems = [];

        foreach ($cartItems as $cartItem) {
            if (isset($dbCartItems[$cartItem['product_id']])) {
                continue;
            }
            $newCartItems[] = [
                'user_id' => $request->user()->id,
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity']
            ];
        }

        if (!empty($newCartItems)) {
            CartItem::insert($newCartItems);
        }
    }
}
