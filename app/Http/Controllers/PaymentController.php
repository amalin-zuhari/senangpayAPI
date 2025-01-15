<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        // Retrieve SenangPay configuration
        $merchantId = config('services.senangpay.merchant_id');
        $secretKey = config('services.senangpay.secret_key');
        $apiUrl = config('services.senangpay.api_url');

        dd($merchantId, $secretKey, $apiUrl);

        // Validate incoming request
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'detail' => 'required|string'
        ]);

        // Prepare payment data
        $data = [
            'detail' => $request->input('detail'),
            'amount' => $request->input('amount'),
            'order_id' => $request->input('order_id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ];

        // Generate hash for secure transmission
        $hashString = $secretKey .
            urldecode($data['detail']) .
            urldecode($data['amount']) .
            urldecode($data['order_id']);

        $data['hash'] = hash_hmac('sha256', $hashString, $secretKey);

        // Construct payment URL
        $paymentUrl = "{$apiUrl}{$merchantId}";

        // Return payment URL and data
        return response()->json([
            'payment_url' => $paymentUrl,
            'payment_data' => $data
        ]);
    }

    public function callback(Request $request)
    {
        // Retrieve SenangPay configuration
        $secretKey = config('services.senangpay.secret_key');

        // Log the callback data for debugging
        Log::info('SenangPay Callback received:', $request->all());

        // Verify hash from the callback
        $hashString = $secretKey .
            $request->status_id .
            $request->order_id .
            $request->transaction_id .
            $request->msg;

        $expectedHash = hash_hmac('sha256', $hashString, $secretKey);

        if ($expectedHash !== $request->hash) {
            Log::error('Invalid hash received from SenangPay');
            return response()->json(['status' => 'error', 'message' => 'Invalid hash'], 400);
        }

        // Determine payment status
        $status = $request->status_id == '1' ? 'completed' : 'failed';

        // Implement your business logic here (e.g., update database)
        Log::info("Payment status: {$status} for order_id: {$request->order_id}");

        return response()->json([
            'status' => 'success',
            'payment_status' => $status,
            'order_id' => $request->order_id,
            'transaction_id' => $request->transaction_id
        ]);
    }
}
