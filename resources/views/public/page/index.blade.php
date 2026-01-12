@extends('public.master')

@section('content')
<!-- Start Hero Section -->
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Modern Interior <span clsas="d-block">Design Studio</span></h1>
                    <p class="mb-4">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.</p>
                    <p><a href="" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="hero-img-wrap">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<!-- Start Product Section -->
<div class="product-section">
    <div class="container">
        <div class="row">

            <!-- Start Column 1 -->
            <div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
                <h2 class="mb-4 section-title">Premium Quality Jerseys Made for Performance</h2>
                <p class="mb-4">
                    Each jersey is crafted with high-quality fabric to ensure maximum comfort, durability, and a modern athletic look — perfect for both the field and everyday wear.
                </p>
                <p><a href="shop.html" class="btn">Shop Now</a></p>
            </div>
            <!-- End Column 1 -->


            <div class="col-12 col-md-4 col-lg-3 mb-5">
                <div class="product-item">
                    <a href="#">
                        <img src="{{ asset('website/images/MeherArt-Product.png') }}" class="img-fluid product-thumbnail">
                        <h3 class="product-title">Argentina 2026 Jersey Home</h3>
                        <strong class="product-price">$50.00</strong>
                    </a>
                    <br>
                    <button type="button" class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 mb-5">
                <div class="product-item">
                    <a href="#">
                        <img src="{{ asset('website/images/MeherArt-Product.png') }}" class="img-fluid product-thumbnail">
                        <h3 class="product-title">Argentina 2026 Jersey Home</h3>
                        <strong class="product-price">$50.00</strong>
                    </a>
                    <br>
                    <button type="button" class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 mb-5">
                <div class="product-item">
                    <a href="#">
                        <img src="{{ asset('website/images/MeherArt-Product.png') }}" class="img-fluid product-thumbnail">
                        <h3 class="product-title">Argentina 2026 Jersey Home</h3>
                        <strong class="product-price">$50.00</strong>
                    </a>
                    <br>
                    <button type="button" class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Product Section -->

<!-- Start Why Choose Us Section -->
<div class="why-choose-section">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-6">
                <h2 class="section-title">Why Choose Us</h2>
                <p>Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.</p>

                <div class="row my-5">
                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="images/truck.svg" alt="Image" class="imf-fluid">
                            </div>
                            <h3>Fast &amp; Free Shipping</h3>
                            <p>Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate.</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="images/bag.svg" alt="Image" class="imf-fluid">
                            </div>
                            <h3>Easy to Shop</h3>
                            <p>Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate.</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="images/support.svg" alt="Image" class="imf-fluid">
                            </div>
                            <h3>24/7 Support</h3>
                            <p>Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate.</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="feature">
                            <div class="icon">
                                <img src="images/return.svg" alt="Image" class="imf-fluid">
                            </div>
                            <h3>Hassle Free Returns</h3>
                            <p>Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-5">
                <div class="img-wrap">
                    <img src="images/full-pic-1.png" alt="Image" class="img-fluid">
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
                    <div class="grid grid-1"><img src="images/full-pic-1.png" alt="Untree.co"></div>
                    <div class="grid grid-2"><img src="images/full-pic-1.png" alt="Untree.co"></div>
                    <div class="grid grid-3"><img src="images/full-pic-1.png" alt="Untree.co"></div>
                </div>
            </div>
            <div class="col-lg-5 ps-lg-5">
                <h2 class="section-title mb-4">We Help You Make Modern Interior Design</h2>
                <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.
                    Pellentesque habitant morbi tristique senectus et netus et malesuada</p>

                <ul class="list-unstyled custom-list my-4">
                    <li>Donec vitae odio quis nisl dapibus malesuada</li>
                    <li>Donec vitae odio quis nisl dapibus malesuada</li>
                    <li>Donec vitae odio quis nisl dapibus malesuada</li>
                    <li>Donec vitae odio quis nisl dapibus malesuada</li>
                </ul>
                <p><a herf="#" class="btn">Explore</a></p>
            </div>
        </div>
    </div>
</div>
<!-- End We Help Section -->

<!-- Start Popular Product -->
<div class="popular-product">
    <div class="container">
        <div class="row">

            <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                <div class="product-item-sm d-flex">
                    <div class="thumbnail">
                        <img src="images/product-1.png" alt="Image" class="img-fluid">
                    </div>
                    <div class="pt-3">
                        <h3>Nordic Chair</h3>
                        <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio </p>
                        <p><a href="#">Read More</a></p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                <div class="product-item-sm d-flex">
                    <div class="thumbnail">
                        <img src="images/product-2.png" alt="Image" class="img-fluid">
                    </div>
                    <div class="pt-3">
                        <h3>Kruzo Aero Chair</h3>
                        <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio </p>
                        <p><a href="#">Read More</a></p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                <div class="product-item-sm d-flex">
                    <div class="thumbnail">
                        <img src="images/product-3.png" alt="Image" class="img-fluid">
                    </div>
                    <div class="pt-3">
                        <h3>Ergonomic Chair</h3>
                        <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio </p>
                        <p><a href="#">Read More</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Popular Product -->

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
                                        <img src="images/review/review (1).jpeg" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="images/review/review (2).jpeg" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="images/review/review (3).jpeg" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="images/review/review (4).jpeg" alt="">
                                    </div>
                                </li>

                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="images/review/review (5).jpeg" alt="">
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