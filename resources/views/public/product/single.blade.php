@extends('public.master')

@section('seo')
    <meta name="description" content="{{ $product->seo_description }}" />
    <meta name="keywords" content="{{ $product->seo_keywords }}" />
    <title>Get Your Premium Jersey From Bangladesh Sports Zone.</title>
@endsection

@section('content')
<div class="bsports-zone product-section before-footer-section single-product-page">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="product-image">
                    <div id="singleProductImageSlider" class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                @foreach($product->images as $image)
                                <li class="splide__slide">
                                    <div class="review-card">
                                        <img src="{{ asset('images/1/Product/') }}/{{$image->image_name}}" alt="{{$product->name}}">
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="product-info" id="product-info">
                    <h1 class="product-title">{{$product->name}}</h1>
                    <div class="price">
                        @if($product->price !== $priceAfterDiscount && $product->discount_type && $product->discount_value)
                        <span class="original-price">
                            Tk.{{ number_format($product->price, 2) }}
                        </span>
                        @endif
                        <strong class="price-display">Tk.{{ number_format($priceAfterDiscount, 2) }}</strong>
                    </div>
                    <p class="product-short-description">{{$product->seo_description}}</p>

                    <div class="variation">
                        <div><i>Select Yours</i></div>
                        @foreach($product->variations as $variation)
                        <button class="variation-btn {{ $variation->is_default ? 'active' : '' }}" data-variation-id="{{ $variation->id }}" data-variation-tagline="{{ $variation->tagline }}"
                            data-variation-selling-price="{{ $variation->selling_price }}" data-variation-price-after-discount="{{ $variation->price_after_discount }}"
                            data-variation-discount-type="{{ $variation->discount_type }}" data-variation-discount-value="{{ $variation->discount_value }}">
                            {{ $variation->tagline }}
                            <i class="fa-solid fa-check"></i>
                        </button>
                        @endforeach
                    </div>

                    <div class="mt-2">
                        <div><i>Select Quantity</i></div>
                        <div class="input-group mb-3" style="max-width: 150px;">
                            <button class="qty-minus-btn qty-btn" type="button">&minus;</button>
                            <input id="quantityInput" type="text" class="form-control text-center quantity" value="1" placeholder="" aria-label="Example text with button addon"
                                aria-describedby="button-addon1">
                            <button class="qty-plus-btn qty-btn" type="button">&plus;</button>
                        </div>
                    </div>

                    <p>
                        <button data-product-id="{{$product->id}}" data-product-name="{{$product->name}}" data-product-image="{{$product->image}}" id="addToCartBtn" class="single-add-to-cart-btn">Add
                            to Cart</button>
                        <a href="index.html" class="buy-now-btn mt-1">Cash on Delivery</a>
                    </p>
                </div>

            </div>
        </div>

        <!-- // details Section -->
        <div class="row">
            <div class="col-12 mt-1 col-lg-9">
                <div class="product-details">
                    <div class="row">
                        <div class="col-12 product-description">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 mt-1">

                <!-- // review Section -->


                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="single-product-page-review-slider-section">
                            <p class="mb-0 text-center">Our Happy Customers Reviews</p>
                            <div id="singleProductPageReviewSplide" class="splide">
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

                <!-- // Related Products Section -->
                <div class="row">
                    @foreach($relatedProducts as $rproduct)
                    <div class="col-12 mb-3 related-product-section">
                        <div class="product-item px-2">
                            <a title="{{ $rproduct->name }}" href="{{ route('product.single', $rproduct->slug) }}">
                                <img src="{{ asset('images/1/Product/') }}/{{$rproduct->image}}" alt="{{ $rproduct->name }}" class="img-fluid product-thumbnail">
                                <h3 class="product-title">{{ $rproduct->short_name }}</h3>
                                <div class="product-price">
                                    @if($rproduct->discount_type && $rproduct->discount_value)
                                    <span class="original-price" style="text-decoration: line-through; color: #999; margin-right: 8px;">
                                        Tk.{{ number_format($rproduct->price, 2) }}
                                    </span>
                                    @endif
                                    <strong>Tk.{{ number_format($rproduct->price_after_discount, 2) }}</strong>
                                </div>
                            </a>
                            <button type="button" class="add-to-cart-btn" data-product-id="{{ $rproduct->id }}" data-product-name="{{ $rproduct->name }}" data-product-image="{{ $rproduct->image }}"
                                data-product-quantity="1" data-variation-id="{{ $rproduct->default_variation_id }}"
                                data-variation-price-after-discount="{{ $rproduct->default_variation_price_after_discount }}"
                                data-variation-selling-price="{{ $rproduct->default_variation_selling_price }}" data-variation-discount-type="{{ $rproduct->default_variation_discount_type }}"
                                data-variation-discount-value="{{ $rproduct->default_variation_discount_value }}" data-variation-tagline="{{ $rproduct->default_variation_tagline }}">Add to
                                Cart</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var websiteData = {};

document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const item = question.parentElement;
        item.classList.toggle('active');
    });
});

    new Splide('#singleProductPageReviewSplide', {
        type: 'loop',
        perPage: 2,
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
                perPage: 2
            },
            480: {
                perPage: 2
            },
            320: {
                perPage: 2
            }
        }
    }).mount(window.splide.Extensions);


    new Splide('#singleProductImageSlider', {
        type: 'slide',
        perPage: 1,
        gap: '1px',
        arrows: true,
        pagination: true,
        drag: true,
        grab: true,
        breakpoints: {
            768: { perPage: 1 },
            480: { perPage: 1 },
            320: { perPage: 1 }
        }
    }).mount();

</script>

<script src="{{ asset('website/js/single-product-script.js') }}"></script>
@endsection