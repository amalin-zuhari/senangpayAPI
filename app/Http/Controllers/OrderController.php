<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(Request $request)
{
    $merchantId = config('services.senangpay.merchant_id');
    // dd($merchantId); // This will show the value or null

    $paymentData = [
        'detail' => 'Product purchase',
        'amount' => '100.00',
        'order_id' => 'ORD' . time(),
        'name' => 'Customer Name',
        'email' => 'customer@example.com',
        'phone' => '0123456789'
    ];

    // Generate hash
    $secretKey = config('services.senangpay.secret_key');
    $hashString = hash_hmac('sha256', 
        $secretKey . 
        urldecode($paymentData['detail']) . 
        urldecode($paymentData['amount']) . 
        urldecode($paymentData['order_id']), 
        $secretKey
    );

    $paymentData['hash'] = $hashString; // Add hash to payment data


    // This will directly render your redirect view with the payment data
    return view('payment.redirect', compact('paymentData'));
}
}
