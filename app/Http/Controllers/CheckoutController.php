<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\CartItem;
use App\Enums\OrderStatus;
use App\Http\Helpers\Cart;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        list($products, $cartItems) = Cart::getCartItemsAndProducts();

        $lineItems = [];
        $totalPrice = 0;

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                        'images' => ['https://picsum.photos/400']
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => $quantity,
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

        $orderData = [
            'total_price' => $totalPrice,
            'status' => OrderStatus::Unpaid,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];

        $order = Order::create($orderData);

        $paymentData = [
            'order_id' => $order->id,
            'status' => PaymentStatus::Pending,
            'amount' => $totalPrice,
            'type' => 'cc',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'session_id' => $checkout_session->id
        ];

        Payment::create($paymentData);

        // dd($checkout_session->id);

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        $sessionId = $request->get('session_id');

        $user = $request->user();

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if (!$session) {
                return view('checkout.failure', ['message' => 'Invalid Session ID']);
            }

            $payment = Payment::query()->where(['session_id' => $session->id, 'status' => PaymentStatus::Pending])->first();

            if (!$payment) {
                return view('checkout.failure', ['message' => 'Payment Does Not Exist']);
            }

            $payment->status = PaymentStatus::Paid;
            $payment->update();

            $order = $payment->order;

            $order->status = OrderStatus::Paid;
            $order->update();

            CartItem::where('user_id', $user->id)->delete();

            $customer = $stripe->customers->retrieve($session->customer);

            return view('checkout.success', compact('customer'));
        } catch(\Exception $e) {
            return view('checkout.failure', ['message' => $e->getMessage()]);
        }
    }

    public function failure(Request $request)
    {
        return view('checkout.failure', ['message' => '']);
    }

    public function checkoutOrder(Request $request, Order $order)
    {
        dd($order);
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
