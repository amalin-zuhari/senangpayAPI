{{-- resources/views/payment/redirect.blade.php --}}
<html>
<head>
    <title>Redirecting to SenangPay</title>
</head>
<body onload="document.order.submit()">
    <form name="order" method="post" action="https://sandbox.senangpay.my/payment/{{ $paymentData['merchant_id'] }}">
        {{-- @csrf --}}
        <input type="hidden" name="detail" value="{{ $paymentData['detail'] }}">
        <input type="hidden" name="amount" value="{{ $paymentData['amount'] }}">
        <input type="hidden" name="order_id" value="{{ $paymentData['order_id'] }}">
        <input type="hidden" name="name" value="{{ $paymentData['name'] }}">
        <input type="hidden" name="email" value="{{ $paymentData['email'] }}">
        <input type="hidden" name="phone" value="{{ $paymentData['phone'] }}">
        <input type="hidden" name="hash" value="{{ $paymentData['hash'] }}">
    </form>
</body>
</html>