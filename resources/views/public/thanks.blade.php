@extends('public.master')

@section('content')
<div class="untree_co-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="p-5 border bg-white">
                    <h2 class="h3 mb-4 text-black">Thank You for Your Order!</h2>
                    <p class="mb-4">Your order has been placed successfully.</p>
                    <p class="mb-4"><strong>Order Tracking Number:</strong> <span class="text-primary">{{ $order }}</span></p>
                    <p class="mb-4">We will process your order and contact you soon.</p>
                    <a href="{{ route('index') }}" class="btn btn-primary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
