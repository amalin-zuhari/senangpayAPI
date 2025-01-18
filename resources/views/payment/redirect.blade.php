<!DOCTYPE html>
<html>

<head>
    <title>Redirecting to SenangPay</title>
</head>

<body>
    <h1>Proceed to Payment</h1>
    <p>Please click the button below to make your payment.</p>

    <form action="{{ route('payment.initiate') }}" method="POST">
        @csrf
        <input type="hidden" name="detail" value="{{ $paymentData['detail'] }}">
        <input type="hidden" name="amount" value="{{ $paymentData['amount'] }}">
        <input type="hidden" name="order_id" value="{{ $paymentData['order_id'] }}">
        <input type="hidden" name="name" value="{{ $paymentData['name'] }}">
        <input type="hidden" name="email" value="{{ $paymentData['email'] }}">
        <input type="hidden" name="phone" value="{{ $paymentData['phone'] }}">

        <button type="submit">Pay Now</button>
    </form>

</body>

</html>
