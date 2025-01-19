<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout()
    {
        $requestId = uniqid();
        Log::info('Payment process started', [
            'request_id' => $requestId,
            'timestamp' => now()->toIso8601String()
        ]);

        Log::info('Starting checkout process');

        $merchantId = config('services.senangpay.merchant_id');
        Log::info('Merchant ID from config:', ['merchant_id' => $merchantId]);

        $orderId = 'ORD' . time();

        $paymentData = [
            'detail' => 'Product purchase',
            'amount' => '20.00',
            'order_id' => $orderId,
            'name' => 'Aaron Aziz',
            'email' => 'aaronaziz@example.com',
            'phone' => '0123456789',
        ];

        Log::info('Payment data prepared:', [
            'request_id' => $requestId,
            'payment_data' => $paymentData
        ]);

        return view('payment.redirect', compact('paymentData'));
    }
}
