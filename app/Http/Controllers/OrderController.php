<?php 
 
namespace App\Http\Controllers; 
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
 
class OrderController extends Controller 
{ 
    public function checkout(Request $request) 
    { 
     
        Log::info('Starting checkout process');

        $merchantId = config('services.senangpay.merchant_id'); 
        Log::info('Merchant ID from config:', ['merchant_id' => $merchantId]);
        
        if (!$merchantId) {
            Log::error('Merchant ID is missing from configuration');
        }
 
        $paymentData = [ 
            'detail' => 'Product purchase', 
            'amount' => '20.00', 
            'order_id' => 'ORD' . time(), 
            'name' => 'Aaron Aziz', 
            'email' => 'aaronaziz@example.com', 
            'phone' => '0123456789', 
            'merchant_id' => $merchantId 
        ]; 
 
        Log::info('Payment data prepared:', ['payment_data' => $paymentData]);
 
        // Generate hash 
        $secretKey = config('services.senangpay.secret_key'); 
        Log::info('Secret key retrieved:', ['key_exists' => !empty($secretKey)]);

        $hashString = hash_hmac('sha256',  
            $secretKey .  
            urldecode($paymentData['detail']) .  
            urldecode($paymentData['amount']) .  
            urldecode($paymentData['order_id']),  
            $secretKey 
        ); 
 
        $paymentData['hash'] = $hashString;
        Log::info('Hash generated and added to payment data');
 
        Log::info('Rendering payment redirect view');
        // This will directly render your redirect view with the payment data 
        return view('payment.redirect', compact('paymentData')); 
    }
}