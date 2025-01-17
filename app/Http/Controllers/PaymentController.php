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

    // 
    
    public function callback(Request $request)
{
    Log::info('SenangPay callback received.', ['callback_data' => $request->all()]);

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
        return redirect()->route('payment.return')->withErrors([
            'message' => 'Invalid payment verification. Please contact support.'
        ]);
    }

    // Determine payment status
    $status = $request->status_id == '1' ? 'completed' : 'failed';

    Log::info("Payment status determined: {$status}", [
        'order_id' => $request->order_id,
        'transaction_id' => $request->transaction_id
    ]);

    // Redirect to return page with payment details
    return redirect()->route('payment.return')->with([
        'payment_status' => $status,
        'order_id' => $request->order_id,
        'transaction_id' => $request->transaction_id
    ]);
}

}
