<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SenangPayService
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

    public function createPayment($orderData)
    {
        try {
            // Generate hash using HMAC SHA256 as per sample code
            $hash = hash_hmac('sha256', 
                $this->secretKey . 
                urldecode($orderData['detail']) . 
                urldecode($orderData['amount']) . 
                urldecode($orderData['order_id']), 
                $this->secretKey
            );
            
            $paymentData = [
                'detail' => $orderData['detail'],
                'amount' => $orderData['amount'],
                'order_id' => $orderData['order_id'],
                'hash' => $hash,
                'name' => $orderData['name'],
                'email' => $orderData['email'],
                'phone' => $orderData['phone']
            ];

            return [
                'success' => true,
                'payment_url' => $this->apiUrl . $this->merchantId,
                'payment_data' => $paymentData
            ];
        } catch (\Exception $e) {
            Log::error('SenangPay payment creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment initiation failed'
            ];
        }
    }

    public function verifyCallback($callbackData)
    {
        // Generate hash for verification using HMAC SHA256
        $hash = hash_hmac('sha256',
            $this->secretKey .
            urldecode($callbackData['status_id']) .
            urldecode($callbackData['order_id']) .
            urldecode($callbackData['transaction_id']) .
            urldecode($callbackData['msg']),
            $this->secretKey
        );
        
        return $hash === urldecode($callbackData['hash']);
    }
}