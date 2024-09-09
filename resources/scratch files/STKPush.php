<?php

namespace App\Mpesa;

use App\Models\MpesaSTK;
use Illuminate\Http\Request;
use App\Jobs\SendMpesaNotificationJob;

// This Class is responsible for getting a response from Safaricom and Storing the Transaction Details to the Database
class STKPush
{
    public $failed = false;
    public $response = 'An Unkown Error Occured';

    public function confirm(Request $request)
    {
        $payload = json_decode($request->getContent());
        $user = $request->user();

        if (property_exists($payload, 'Body')) {
            $result_code = $payload->Body->stkCallback->ResultCode;
            $result_desc = $payload->Body->stkCallback->ResultDesc;

            $merchant_request_id = $payload->Body->stkCallback->MerchantRequestID;
            $checkout_request_id = $payload->Body->stkCallback->CheckoutRequestID;

            $stkPush = MpesaSTK::where('merchant_request_id', $merchant_request_id)
                ->where('checkout_request_id', $checkout_request_id)->first();

            if ($result_code == '0') {
                // Transaction Successful
                $amount = $payload->Body->stkCallback->CallbackMetadata->Item[0]->Value;
                $mpesa_receipt_number = $payload->Body->stkCallback->CallbackMetadata->Item[1]->Value;
                $transaction_date = $payload->Body->stkCallback->CallbackMetadata->Item[3]->Value;
                $phone_number = $payload->Body->stkCallback->CallbackMetadata->Item[4]->Value;
                $message = "Your Mpesa transaction of amount Ksh {$amount} was successful.";
                $transaction_status = 'success';

                $data = [
                    'result_desc' => $result_desc,
                    'result_code' => $result_code,
                    'merchant_request_id' => $merchant_request_id,
                    'checkout_request_id' => $checkout_request_id,
                    'amount' => $amount,
                    'mpesa_receipt_number' => $mpesa_receipt_number,
                    'transaction_date' => $transaction_date,
                    'phone_number' => $phone_number,
                    'status' => 'success', // Mark success
                ];
            } else {
                // Transaction Failed (ResultCode != 0)
                $data = [
                    'result_desc' => $result_desc,
                    'result_code' => $result_code,
                    'merchant_request_id' => $merchant_request_id,
                    'checkout_request_id' => $checkout_request_id,
                    'status' => 'failed', // Mark failure
                ];

                // Handle different failure scenarios based on ResultCode
                switch ($result_code) {
                    case '1032':
                        $message = 'Transaction was canceled by the user';
                        break;
                    case '1':
                        $message = 'User entered the wrong PIN';
                        break;
                    case '2001':
                        $message = 'Insufficient funds in the account';
                        break;
                    default:
                        $message = 'Transaction failed: ' . $result_desc;
                        break;
                }

                $transaction_status = 'failed';
            }

            if ($user) {
                SendMpesaNotificationJob::dispatch($user, $message, $transaction_status);
            }

            // Save or update the transaction details in the database
            if ($stkPush) {
                $stkPush->fill($data)->save();
            } else {
                MpesaSTK::create($data);
            }
        } else {
            $this->failed = true;
            $this->response = 'Invalid payload received';
        }

        return $this;
    }
}
