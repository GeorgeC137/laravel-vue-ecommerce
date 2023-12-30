<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Helpers\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    public function index()
    {
        list($products, $cartItems) = $this->getCartItemsAndProducts();

        $total = 0;

        foreach ($products as $product) {
            $total += $product->price * $cartItems[$product->id]['quantity'];
        }

        return view('cart.index', compact('cartItems', 'products', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $quantity = $request->post('quantity', 1);

        $user = $request->user();

        if ($user) {
            $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->quantity = +$quantity;
                $cartItem->update();
            } else {
                $data = [
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ];

                CartItem::create($data);
            }

            return response([
                'count' => Cart::getCartItemsCount()
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);
            $productFound = false;

            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] += $quantity;
                    $productFound = true;
                    break;
                }
            }

            if (!$productFound) {
                $cartItems[] = [
                    'user_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ];
            }

            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            return response([
                'count' => Cart::getCountFromItems($cartItems)
            ]);
        }
    }

    public function remove(Request $request, Product $product)
    {
        $user = $request->user();

        if ($user) {
            $cartItem = CartItem::query()->where(['user_id' => $user->id, 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->delete();
            }

            return response([
                'count' => Cart::getCartItemsCount()
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);

            foreach ($cartItems as $i => &$item) {
                if ($item['product_id'] === $product->id) {
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }

            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            return response([
                'count' => Cart::getCountFromItems($cartItems)
            ]);
        }
    }

    public function updateQuantity(Request $request, Product $product)
    {
        $quantity = $request->post('quantity');

        $user = $request->user();

        if ($user) {
            CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->update(['quantity' => $quantity]);

            return response([
                'count' => Cart::getCartItemsCount()
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);

            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }

            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            return response([
                'count' => Cart::getCountFromItems($cartItems)
            ]);
        }
    }

    public function checkout(Request $request)
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        list($products, $cartItems) = $this->getCartItemsAndProducts();

        $lineItems = [];

        foreach ($products as $product) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                        'images' => ['https://picsum.photos/400']
                    ],
                    'unit_amount_decimal' => $product->price * 100,
                ],
                'quantity' => $cartItems[$product->id]['quantity'],
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => 'http://localhost:4242/success',
            'cancel_url' => 'http://localhost:4242/cancel',
        ]);

        return redirect($checkout_session->url);
    }

    private function getCartItemsAndProducts()
    {
        $cartItems = Cart::getCartItems();

        $ids = Arr::pluck($cartItems, 'product_id');

        $products = Product::query()->whereIn('id', $ids)->get();

        $cartItems = Arr::keyBy($cartItems, 'product_id');

        return [
            $products,
            $cartItems
        ];
    }
}
