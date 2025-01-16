{{-- resources/views/payment/redirect.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to SenangPay</title>
</head>
<body>
    <h1>Proceed to Payment</h1>
    <p>Please click the button below to make your payment.</p>

    <form name="order" method="post" action="https://sandbox.senangpay.my/payment/{{ $paymentData['merchant_id'] }}">
        @csrf
        <input type="hidden" name="detail" value="{{ $paymentData['detail'] }}">
        <input type="hidden" name="amount" value="{{ $paymentData['amount'] }}">
        <input type="hidden" name="order_id" value="{{ $paymentData['order_id'] }}">
        <input type="hidden" name="name" value="{{ $paymentData['name'] }}">
        <input type="hidden" name="email" value="{{ $paymentData['email'] }}">
        <input type="hidden" name="phone" value="{{ $paymentData['phone'] }}">
        <input type="hidden" name="hash" value="{{ $paymentData['hash'] }}">

        <!-- Pay Now Button -->
        <button type="submit" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer;">
            Pay Now
        </button>
    </form>
</body>
</html>
