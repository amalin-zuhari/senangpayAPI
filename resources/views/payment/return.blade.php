<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($status_id == '1')
                    <div class="alert alert-success">
                        <h4>Payment Successful!</h4>
                        <p>Order ID: {{ $order_id }}</p>
                        <p>Transaction ID: {{ $transaction_id }}</p>
                        <p>Message: {{ str_replace('_', ' ', $msg) }}</p>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <h4>Payment Failed!</h4>
                        <p>Order ID: {{ $order_id }}</p>
                        <p>Message: {{ str_replace('_', ' ', $msg) }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
