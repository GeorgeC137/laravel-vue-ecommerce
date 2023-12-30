<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Cart;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        list($products, $cartItems) = Cart::getCartItemsAndProducts();

        $lineItems = [];

        foreach ($products as $product) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                        'images' => ['https://picsum.photos/300']
                    ],
                    'unit_amount_decimal' => $product->price * 100,
                ],
                'quantity' => $cartItems[$product->id]['quantity'],
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true),
            'cancel_url' => route('checkout.failure', [], true),
        ]);

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        dd($request->all());
    }

    public function failure(Request $request)
    {
        dd($request->all());
    }
}
