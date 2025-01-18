<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout()
    {
        Log::info('Starting checkout process');

        $merchantId = config('services.senangpay.merchant_id');
        Log::info('Merchant ID from config:', ['merchant_id' => $merchantId]);

        $paymentData = [
            'detail' => 'Product purchase',
            'amount' => '20.00',
            'order_id' => 'ORD' . time(),
            'name' => 'Aaron Aziz',
            'email' => 'aaronaziz@example.com',
            'phone' => '0123456789',
        ];

        Log::info('Payment data prepared:', ['payment_data' => $paymentData]);

        return view('payment.redirect', compact('paymentData'));
    }
}
