<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Mpesa\STKPush;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\MpesaSTK;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Http\Helpers\Cart;
use App\Mail\NewOrderEmail;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MpesaSTKPUSHController extends Controller
{
    public $result_code = 1;
    public $result_desc = 'An error occured';

    // Initiate  Stk Push Request
    public function STKPush(Request $request)
    {
        $user = $request->user();

        $customer = $user->customer;

        $phone_number = $customer->phone;

        $account_number = getenv('MPESA_INITIATOR_NAME');

        if (!$customer->shippingAddress || !$customer->billingAddress) {
            return redirect()->route('profile')->with('error', 'Please provide your address details to proceed');
        }

        list($products, $cartItems) = Cart::getCartItemsAndProducts();

        $orderItems = [];
        $totalPrice = 0;

        DB::beginTransaction();

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            if ($product->quantity !== null && $product->quantity < $quantity) {
                $message = match ($product->quantity) {
                    0 => 'The product "'.$product->title.'" is out of stock',
                    1 => 'There is only one item left for product "'.$product->title.'"',
                    default => 'There are only ' . $product->quantity . ' items left for product "'.$product->title.'"'
                };
                return redirect()->back()->with('error', $message);
            }

        }

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;
            $orderItems[] = [
                'product_id' => $product->id,
                'unit_price' => $product->price,
                'quantity' => $quantity
            ];
        }

        // dd($totalPrice);

        $response = Mpesa::stkpush($phone_number, $totalPrice, $account_number);

        /** @var \Illuminate\Http\Client\Response $response */
        $result = $response->json();

        // dd($result);

        if (!is_null($result)) {
            MpesaSTK::create([
                'merchant_request_id' =>  $result['MerchantRequestID'],
                'checkout_request_id' =>  $result['CheckoutRequestID'],
                'status' => 'pending'
            ]);
        }


        try {
            // Create Order
            $orderData = [
                'total_price' => $totalPrice,
                'status' => OrderStatus::Unpaid,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            $order = Order::create($orderData);

            // Create OrderItems
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            // Create Payment
            $paymentData = [
                'order_id' => $order->id,
                'status' => PaymentStatus::Pending,
                'amount' => $totalPrice,
                'type' => 'cc',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'session_id' => $result['CheckoutRequestID']
            ];

            Payment::create($paymentData);
        } catch (Exception $e) {
            DB::rollBack();
            Log::critical( __METHOD__ . ' method does not work. '. $e->getMessage());
            throw $e;
        }

        DB::commit();

        // Delete Cart Items After Checkout
        CartItem::where('user_id', $user->id)->delete();

        return $result;
    }

    // This function is used to review the response from Safaricom once a transaction is complete
    public function STKConfirm(Request $request)
    {
        $stk_push_confirm = (new STKPush())->confirm($request);

        try {
            $checkout_request_id = $request->get('CheckoutRequestID');

            if (!$checkout_request_id) {
                return view('checkout.failure', ['message' => 'Invalid CheckoutRequestID']);
            }

            $payment = Payment::query()
                ->where(['session_id' => $checkout_request_id])
                ->whereIn('status', [PaymentStatus::Paid, PaymentStatus::Pending])
                ->first();

            if (!$payment) {
                throw new NotFoundHttpException();
            }

            if ($payment->status === PaymentStatus::Pending) {
                $this->updateOrderAndSession($payment);
            }

            if ($stk_push_confirm) {
                $this->result_code = 0;
                $this->result_desc = 'Success';
            }

            return response()->json([
                'ResultCode' => $this->result_code,
                'ResultDesc' => $this->result_desc
            ]);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return view('checkout.failure', ['message' => $e->getMessage()]);
        }
    }

    // Used to query the status of an STK Push Transaction
    public function query(Request $request)
    {
        $checkoutRequestId = $request->input('CheckoutRequestID');

        $response = Mpesa::stkquery($checkoutRequestId);
        $result = json_decode((string)$response);

        return $result;
    }

    private function updateOrderAndSession(Payment $payment)
    {
        DB::beginTransaction();
        try {
            $payment->status = PaymentStatus::Paid;
            $payment->update();

            $order = $payment->order;

            $order->status = OrderStatus::Paid;
            $order->update();
        } catch (Exception $e) {
            DB::rollBack();
            Log::critical( __METHOD__ . ' method does not work. '. $e->getMessage());
            throw $e;
        }

        DB::commit();

        try {
            $adminUsers = User::where('is_admin', 1)->get();

            foreach ([...$adminUsers, $order->user] as $user) {
                Mail::to($user)->send(new NewOrderEmail($order, (bool)$user->is_admin));
            }
        } catch (Exception $e) {
            Log::critical('Email sending does not work. '. $e->getMessage());
        }
    }
}
