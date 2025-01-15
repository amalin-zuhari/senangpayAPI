<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SenangpayController extends Controller
{
    
    private $merchantId;
    private $secretKey;
    private $apiUrl;

    public function __construct()
    {
        $this->merchantId = config('services.senangpay.merchant_id');
        $this->secretKey = config('services.senangpay.secret_key');
        $this->apiUrl = config('services.senangpay.api_url');
    }

    public function initiatePayment(Request $request)
    {

        $validated = $request->validate([
            'detail' => 'required|string',
            'amount' => 'required|numeric',
            'order_id' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);


        //Generate hash
        $hashString = hash_hmac('sha256', 
            $this->secretKey . 
            urldecode($validated['detail']) . 
            urldecode($validated['amount']) . 
            urldecode($validated['order_id']), 
            $this->secretKey
        );

        // Prepare data for the payment form
        $paymentData = [
            'detail' => $validated['detail'],
            'amount' => $validated['amount'],
            'order_id' => $validated['order_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'hash' => $hashString,
            'merchant_id' => $this->merchantId
        ];

        // Return view with payment form
        return view('payment.redirect', compact('paymentData'));
    }

    public function callback(Request $request)
    {
        Log::info('Payment callback received', $request->all());

        // Verify hash
        $calculatedHash = hash_hmac('sha256',
            $this->secretKey .
            urldecode($request->status_id) .
            urldecode($request->order_id) .
            urldecode($request->transaction_id) .
            urldecode($request->msg),
            $this->secretKey
        );

        if ($calculatedHash !== urldecode($request->hash)) {
            Log::error('Invalid callback signature');
            return response()->json(['status' => 'error', 'message' => 'Hash verification failed'], 400);
        }

        // Process the payment result
        $status_id = urldecode($request->status_id);
        $msg = urldecode($request->msg);
        $order_id = urldecode($request->order_id);
        $transaction_id = urldecode($request->transaction_id);

        if ($status_id === '1') {
            // Payment successful - Update your order status here
            // Add your order processing logic
            
            return view('payment.success', [
                'message' => "Payment successful for order $order_id",
                'transaction_id' => $transaction_id
            ]);
        } else {
            // Payment failed
            return view('payment.failed', [
                'message' => "Payment failed: $msg",
                'order_id' => $order_id
            ]);
        }
    }
}
