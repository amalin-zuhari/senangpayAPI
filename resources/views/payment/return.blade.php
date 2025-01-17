<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container text-center py-5">
        <h1 class="{{ session('payment_status') === 'completed' ? 'text-success' : 'text-danger' }}">
            {{ session('payment_status') === 'completed' ? 'Thank You!' : 'Payment Failed' }}
        </h1>
        <p class="lead">
            {{ session('payment_status') === 'completed' 
                ? 'Your payment has been processed successfully.' 
                : 'We were unable to process your payment. Please try again.' }}
        </p>
        @if(session('order_id') && session('transaction_id'))
            <p>Order ID: {{ session('order_id') }}</p>
            <p>Transaction ID: {{ session('transaction_id') }}</p>
        @endif
        @if ($errors->any())
            <p class="text-danger">{{ $errors->first('message') }}</p>
        @endif
        <a href="/" class="btn btn-primary">Go Back to Home</a>
    </div>
</body>
</html>
