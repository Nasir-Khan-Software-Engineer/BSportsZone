@extends('public.master')
@section('seo')
    <meta name="description" content="Premium quality jersey designed for comfort, performance, and true fans." />
    <meta name="keywords" content="sports jersey, premium jersey, comfort jersey, performance jersey, true fans jersey" />
    <title>Get Your Premium Jersey From Bangladesh Sports Zone.</title>
@endsection

@section('content')
<div class="bsports-zone">
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

@section('scripts')
<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderPlaced = urlParams.get('order_placed') === 'true';
    
    if (orderPlaced) {
        if (typeof Website !== 'undefined' && Website.AddToCart) {
            Website.AddToCart.clearWebsiteCart();
            Website.AddToCart.setWebsiteCartCount();
        }
        
        urlParams.delete('order_placed');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '') + window.location.hash;
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>
@endsection
