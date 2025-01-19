<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SenangpayController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $requestId = uniqid();
        Log::info('Starting payment initiation', [
            'request_id' => $requestId,
            'request_data' => $request->all(),
            'timestamp' => now()->toIso8601String()
        ]);

        $merchantId = config('services.senangpay.merchant_id');
        $secretKey = config('services.senangpay.secret_key');
        $apiUrl = config('services.senangpay.api_url');

        Log::info('Configuration loaded', [
            'request_id' => $requestId,
            'merchant_id' => $merchantId,
            'secret_key_exists' => !empty($secretKey),
            'api_url' => $apiUrl
        ]);

        $validatedData = $request->validate([
            'detail' => 'required|string|max:500',
            'amount' => 'required|numeric|min:1',
            'order_id' => 'required|string|max:100',
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        $data = [
            'detail' => $request->detail,
            'amount' => $request->amount,
            'order_id' => $request->order_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        $hashString = $secretKey .
            $data['detail'] .
            $data['amount'] .
            $data['order_id'];

        Log::info('Generating hash', [
            'request_id' => $requestId,
            'hash_string' => $hashString
        ]);

        $data['hash'] = hash_hmac('sha256', $hashString, $secretKey);

        Log::info('Payment data prepared', [
            'request_id' => $requestId,
            'data' => $data
        ]);

        $paymentUrl = $apiUrl . $merchantId;

        Log::info('Redirecting to payment page', [
            'request_id' => $requestId,
            'url' => $paymentUrl
        ]);

        return redirect()->away($paymentUrl . '?' . http_build_query($data));
    }

    public function callback(Request $request)
    {
        $requestId = uniqid();
        Log::info('Payment callback received', [
            'request_id' => $requestId,
            'callback_data' => $request->all(),
            'timestamp' => now()->toIso8601String()
        ]);

        $secretKey = config('services.senangpay.secret_key');

        $hashString = $secretKey .
            $request->status_id .
            $request->order_id .
            $request->transaction_id .
            $request->msg;

        Log::info('Verifying callback hash', [
            'request_id' => $requestId,
            'hash_string' => $hashString,
            'received_hash' => $request->hash
        ]);

        $calculatedHash = hash_hmac('sha256', $hashString, $secretKey);

        if ($calculatedHash !== $request->hash) {
            Log::error('Invalid callback hash', [
                'request_id' => $requestId,
                'calculated' => $calculatedHash,
                'received' => $request->hash
            ]);
            return response('OK');
        }

        try {
            if ($request->status_id === '1') {
                // Maybe in the future you can add order update logic here
                // Contoh: Query like Order::where('order_id', $request->order_id)->update(['status' => 'paid']); bla bla bla

                Log::info('Payment successful', [
                    'request_id' => $requestId,
                    'order_id' => $request->order_id,
                    'transaction_id' => $request->transaction_id
                ]);
            } else {
                Log::error('Payment failed', [
                    'request_id' => $requestId,
                    'order_id' => $request->order_id,
                    'message' => $request->msg
                ]);
            }

            return response('OK');
        } catch (\Exception $e) {
            Log::error('Error processing callback', [
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);
            return response('OK');
        }
    }

    public function return(Request $request)
    {
        $requestId = uniqid();
        Log::info('Payment return received', [
            'request_id' => $requestId,
            'return_data' => $request->all(),
            'timestamp' => now()->toIso8601String()
        ]);

        $secretKey = config('services.senangpay.secret_key');

        $hashString = $secretKey .
            $request->status_id .
            $request->order_id .
            $request->transaction_id .
            $request->msg;

        Log::info('Verifying return hash', [
            'request_id' => $requestId,
            'hash_string' => $hashString,
            'received_hash' => $request->hash
        ]);

        $calculatedHash = hash_hmac('sha256', $hashString, $secretKey);

        if ($calculatedHash !== $request->hash) {
            Log::error('Invalid return hash', [
                'request_id' => $requestId,
                'calculated' => $calculatedHash,
                'received' => $request->hash
            ]);
            return view('payment.return')->with('error', 'Invalid payment verification');
        }

        return view('payment.return', [
            'status_id' => $request->status_id,
            'order_id' => $request->order_id,
            'msg' => $request->msg,
            'transaction_id' => $request->transaction_id
        ]);
    }
}
