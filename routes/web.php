<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\SenangpayController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/return', function () {
    return view('payment.return');
})->name('payment.return');

Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/payment/initiate', [SenangpayController::class, 'initiatePayment'])->name('payment.initiate');
Route::post('/payment/callback', [SenangpayController::class, 'callback'])->name('payment.callback');