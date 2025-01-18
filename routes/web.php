<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\SenangpayController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/payment/initiate', [SenangpayController::class, 'initiatePayment'])->name('payment.initiate');
Route::post('/payment/callback', [SenangpayController::class, 'callback'])->name('payment.callback');
Route::get('/payment/return', [SenangpayController::class, 'return'])->name('payment.return');
