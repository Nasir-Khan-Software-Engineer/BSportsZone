@extends('public.master')

@section('seo')
    <meta name="description" content="Premium quality jersey designed for comfort, performance, and true fans." />
    <meta name="keywords" content="sports jersey, premium jersey, comfort jersey, performance jersey, true fans jersey" />
    <title>Get Your Premium Jersey From Bangladesh Sports Zone.</title>
@endsection

@section('content')

<!-- Start Product Section -->
<div class="container">

    <div class="row mb-2">
        <div class="col-12">
            <div class="home-banner">
                <a href="{{ route('shop') }}">
                    <img class="img-fluid" src="https://bsportszone.com/images/1/Banner/animated-banner_2026-01-18_17-14-48.gif" alt="bsportszone">
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Start Column 1 -->
        <div class="col-md-12 col-lg-3  mb-lg-0">
            <div class="main-title-section text-center">
                <h1 class="mb-4 main-title">Get Your Premium Jersey From Bangladesh Sports Zone.</h1>
                <p class="mb-4 sub-title">
                    Premium quality jersey designed for comfort, performance, and true fans.
                </p>
                <p><a href="{{ route('shop') }}" class="shop-now-btn">Shop Now</a></p>
            </div>
        </div>
        <!-- End Column 1 -->

        @foreach($homeProducts as $product)
        <div class="col-12 col-md-4 col-lg-3 mb-3">
            <div class="product-item px-2">
                <a title="{{ $product->name }}" href="{{ route('product.single', $product->slug) }}">
                    <img src="{{ asset('images/1/Product/') }}/{{$product->image}}" alt="{{ $product->name }}" class="img-fluid product-thumbnail">
                    <h3 class="product-title">{{ $product->short_name }}</h3>
                    <div class="product-price">
                        @if($product->discount_type && $product->discount_value)
                        <span class="original-price" style="text-decoration: line-through; color: #999; margin-right: 8px;">
                            Tk.{{ number_format($product->price, 2) }}
                        </span>
                        @endif
                        <strong>Tk.{{ number_format($product->price_after_discount, 2) }}</strong>
                    </div>
                </a>
                <button type="button" class="add-to-cart-btn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-image="{{ $product->image }}"
                    data-product-quantity="1" data-variation-id="{{ $product->default_variation_id }}" data-variation-price-after-discount="{{ $product->default_variation_price_after_discount }}"
                    data-variation-selling-price="{{ $product->default_variation_selling_price }}" data-variation-discount-type="{{ $product->default_variation_discount_type }}"
                    data-variation-discount-value="{{ $product->default_variation_discount_value }}" data-variation-tagline="{{ $product->default_variation_tagline }}">Add to Cart</button>
            </div>
        </div>
        @endforeach

    </div>
</div>
<!-- End Product Section -->

<!-- Start Why Choose Us Section -->
<div class="why-choose-section">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-6">
                <h2 class="section-title title-italic-bold">Why Choose Us</h2>
                <p class="font-roboto-regular">We are committed to delivering premium-quality sports jerseys with reliable service and customer satisfaction at the core. From product quality to post-purchase support, we make sure your shopping experience is smooth, secure, and enjoyable.</p>

                <div class="row my-5">
                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="{{ asset('website/images/truck.svg') }}" alt="Image" class="imf-fluid">
                            </div>
                            <h3>Fast & Reliable Shipping</h3>
                            <p>We process and ship orders quickly to ensure your jersey reaches you on time. Our secure packaging and trusted delivery partners guarantee safe and prompt delivery across the country.</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="{{ asset('website/images/bag.svg') }}" alt="Image" class="imf-fluid">
                            </div>
                            <h3>Easy & Secure Shopping</h3>
                            <p>Enjoy a simple, user-friendly shopping experience with secure payment options. Browse, select, and order your favorite jerseys in just a few clicks.</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="{{ asset('website/images/support.svg') }}" alt="Image" class="imf-fluid">
                            </div>
                            <h3>24/7 Customer Support</h3>
                            <p>Have a question or need help? Our support team is available around the clock to assist you with orders, sizing, or any concerns you may have.</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="{{ asset('website/images/return.svg') }}" alt="Image" class="imf-fluid">
                            </div>
                            <h3>Hassle Free Returns</h3>
                            <p>If the size doesn’t fit or you’re not satisfied, we offer an easy return process with clear guidelines—no stress, no complications.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-5">
                <div class="img-wrap">
                    <img src="https://bsportszone.com/images/1/Product/germany-jersey_2026-01-18_17-32-54.jpg" alt="Germany Jersey" class="img-fluid">
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Why Choose Us Section -->

<!-- Start We Help Section -->
<div class="we-help-section">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-7 mb-5 mb-lg-0">
                <div class="imgs-grid">
                    <div class="grid grid-1"><img src="https://bsportszone.com/images/1/Product/argentina_2026-01-18_16-56-20.png" alt="Argentina Jersey"></div>
                    <div class="grid grid-2"><img src="https://bsportszone.com/images/1/Product/home-product-2_2026-01-18_17-31-40.png" alt="Brazil Jersey"></div>
                    <div class="grid grid-3"><img src="https://bsportszone.com/images/1/Product/home-product-3_2026-01-18_17-31-20.png" alt="Germany Jersey"></div>
                </div>
            </div>
            <div class="col-lg-5 ps-lg-5">
                <h2 class="section-title mb-4 title-italic-bold">We Help You Wear the Game in Style</h2>
                <p class="font-roboto-regular">Premium-quality sports jerseys crafted for all-day comfort, on-field performance, and fans who demand both style and durability.</p>

                <ul class="list-unstyled custom-list my-4 font-roboto-regular">
                    <li>Modern, comfortable jerseys made for players and fans.</li>
                    <li>High-quality jerseys with modern design and lasting comfort.</li>
                    <li>Performance-driven jerseys with premium comfort and style.</li>
                    <li>Comfort, quality, and style—made for every fan.</li>
                </ul>
                <p><a href="{{ route('shop') }}" class="shop-now-btn">Shop Now</a></p>
            </div>
        </div>
    </div>
</div>
<!-- End We Help Section -->


<!-- Start Testimonial Slider -->
<div class="testimonial-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 mx-auto text-center">
                <h2 class="section-title">What Our Customers Say 💬</h2>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="review-slider-section">
                    <div id="reviewSplide" class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="{{ asset('website/images/review/review (1).jpeg') }}" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="{{ asset('website/images/review/review (2).jpeg') }}" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="{{ asset('website/images/review/review (3).jpeg') }}" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="{{ asset('website/images/review/review (4).jpeg') }}" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="{{ asset('website/images/review/review (5).jpeg') }}" alt="">
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Testimonial Slider -->
@endsection

@section('scripts')
<script>
new Splide('#reviewSplide', {
    type: 'loop',
    perPage: 6,
    gap: '1px',
    arrows: false,
    pagination: true,
    drag: true,
    grab: true,
    autoScroll: {
        speed: 0.5,
    },
    breakpoints: {
        768: {
            perPage: 3
        },
        480: {
            perPage: 3
        },
        320: {
            perPage: 2
        }
    }
}).mount(window.splide.Extensions);
</script>
@endsection