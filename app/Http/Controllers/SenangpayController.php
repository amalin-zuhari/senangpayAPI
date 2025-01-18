<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SenangpayController extends Controller
{
    public function initiatePayment(Request $request)
    {
        Log::info('Starting payment initiation', ['request_data' => $request->all()]);

        $merchantId = config('services.senangpay.merchant_id');
        $secretKey = config('services.senangpay.secret_key');
        $apiUrl = config('services.senangpay.api_url');

        Log::info('Configuration loaded', [
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

        Log::info('Generating hash', ['hash_string' => $hashString]);

        $data['hash'] = hash_hmac('sha256', $hashString, $secretKey);

        Log::info('Payment data prepared', ['data' => $data]);

        $paymentUrl = $apiUrl . $merchantId;

        Log::info('Redirecting to payment page', ['url' => $paymentUrl]);

        return redirect()->away($paymentUrl . '?' . http_build_query($data));
    }

    public function callback(Request $request)
    {
        Log::info('Payment callback received', $request->all());

        $secretKey = config('services.senangpay.secret_key');

        $hashString = $secretKey .
            $request->status_id .
            $request->order_id .
            $request->transaction_id .
            $request->msg;

        Log::info('Verifying callback hash', [
            'hash_string' => $hashString,
            'received_hash' => $request->hash
        ]);

        $calculatedHash = hash_hmac('sha256', $hashString, $secretKey);

        if ($calculatedHash !== $request->hash) {
            Log::error('Invalid callback hash', [
                'calculated' => $calculatedHash,
                'received' => $request->hash
            ]);
            return response('OK');
        }

        if ($request->status_id === '1') {
            Log::info('Payment successful', [
                'order_id' => $request->order_id,
                'transaction_id' => $request->transaction_id
            ]);
        } else {
            Log::error('Payment failed', [
                'order_id' => $request->order_id,
                'message' => $request->msg
            ]);
        }

        return response('OK');
    }

    public function return(Request $request)
    {
        Log::info('Payment return received', $request->all());

        $secretKey = config('services.senangpay.secret_key');

        $hashString = $secretKey .
            $request->status_id .
            $request->order_id .
            $request->transaction_id .
            $request->msg;

        Log::info('Verifying return hash', [
            'hash_string' => $hashString,
            'received_hash' => $request->hash
        ]);

        $calculatedHash = hash_hmac('sha256', $hashString, $secretKey);

        if ($calculatedHash !== $request->hash) {
            Log::error('Invalid return hash', [
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
