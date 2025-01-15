@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Details</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('payment.initiate') }}">
                        @csrf
                        <div class="form-group">
                            <label>Detail</label>
                            <input type="text" name="detail" class="form-control" placeholder="Description of the transaction" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" name="amount" class="form-control" placeholder="Amount to pay, for example 12.20" required>
                        </div>
                        <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" name="order_id" class="form-control" value="{{ $order_id }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Name of the customer" required>
                        </div>
                        <div class="form-group">
                            <label>Customer Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email of the customer" required>
                        </div>
                        <div class="form-group">
                            <label>Customer Contact No</label>
                            <input type="text" name="phone" class="form-control" placeholder="Contact number of customer" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection