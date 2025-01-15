@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Result</div>
                <div class="card-body">
                    <div class="alert alert-{{ $success ? 'success' : 'danger' }}">
                        {{ $message }}
                    </div>
                    <a href="{{ route('payment.form') }}" class="btn btn-primary">Make Another Payment</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection