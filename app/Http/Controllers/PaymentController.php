<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        Log::info('Payment initiation started.', ['request_data' => $request->all()]);

        // Retrieve SenangPay configuration
        $merchantId = config('services.senangpay.merchant_id');
        $secretKey = config('services.senangpay.secret_key');
        $apiUrl = config('services.senangpay.api_url');

        Log::info('SenangPay configuration loaded.', [
            'merchant_id' => $merchantId,
            'api_url' => $apiUrl
        ]);

        // Validate incoming request
        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'detail' => 'required|string'
        ]);

        Log::info('Request validated successfully.', ['validated_data' => $validatedData]);

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

        Log::info('Hash generated for payment data.', ['hash' => $data['hash']]);

        // Construct payment URL
        $paymentUrl = "{$apiUrl}{$merchantId}";

        Log::info('Payment URL constructed.', ['payment_url' => $paymentUrl]);

        // Return payment URL and data
        return response()->json([
            'payment_url' => $paymentUrl,
            'payment_data' => $data
        ]);
    }

    public function callback(Request $request)
    {
        Log::info('SenangPay callback received.', ['callback_data' => $request->all()]);

        // Retrieve SenangPay configuration
        $secretKey = config('services.senangpay.secret_key');

        // Verify hash from the callback
        $hashString = $secretKey .
            $request->status_id .
            $request->order_id .
            $request->transaction_id .
            $request->msg;

        $expectedHash = hash_hmac('sha256', $hashString, $secretKey);

        Log::info('Hash verification process.', [
            'calculated_hash' => $expectedHash,
            'received_hash' => $request->hash
        ]);

        if ($expectedHash !== $request->hash) {
            Log::error('Invalid hash received from SenangPay.');
            return response()->json(['status' => 'error', 'message' => 'Invalid hash'], 400);
        }

        // Determine payment status
        $status = $request->status_id == '1' ? 'completed' : 'failed';

        Log::info("Payment status determined: {$status}", [
            'order_id' => $request->order_id,
            'transaction_id' => $request->transaction_id
        ]);

        // Implement your business logic here (e.g., update database)
        Log::info('Business logic executed for callback.');

        return response()->json([
            'status' => 'success',
            'payment_status' => $status,
            'order_id' => $request->order_id,
            'transaction_id' => $request->transaction_id
        ]);
    }
}
