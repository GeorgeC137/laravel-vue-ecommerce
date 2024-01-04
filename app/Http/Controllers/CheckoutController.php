<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Cart;
use Illuminate\Http\Request;
use Exception;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                        'images' => ['https://picsum.photos/400']
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => $cartItems[$product->id]['quantity'],
            ];
        }

        // dd(route('checkout.success', [], true), route('checkout.failure', [], true));

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'customer_creation' => 'always',
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.failure', [], true),
        ]);

        // dd($checkout_session->id);

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        $sessionId = $request->get('session_id');

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if (!$session) {
                return view('checkout.failure', ['message' => 'Invalid Session ID']);
            }

            $customer = $stripe->customers->retrieve($session->customer);

            // dd($session, $customer);

            return view('checkout.success', compact($customer));
        } catch(Exception $e) {
            return view('checkout.failure');
        }
    }

    public function failure(Request $request)
    {
        dd($request->all());
    }

    // public function webhook()
    // {
    //     // This is your Stripe CLI webhook secret for testing your endpoint locally.
    //     $endpoint_secret = getenv('STRIPE_WEBHOOK_SECRET');

    //     $payload = @file_get_contents('php://input');
    //     $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    //     $event = null;

    //     try {
    //         $event = \Stripe\Webhook::constructEvent(
    //             $payload,
    //             $sig_header,
    //             $endpoint_secret
    //         );
    //     } catch (\UnexpectedValueException $e) {
    //         // Invalid payload
    //         return response(400);
    //     } catch (\Stripe\Exception\SignatureVerificationException $e) {
    //         // Invalid signature
    //         return response(400);
    //     }

    //     // Handle the event
    //     switch ($event->type) {
    //         case 'payment_intent.succeeded':
    //             $paymentIntent = $event->data->object;
    //             // ... handle other event types
    //         default:
    //             echo 'Received unknown event type ' . $event->type;
    //     }

    //     return response(200);
    // }
}
