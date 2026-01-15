@extends('public.master')


@section('content')
<div class="untree_co-section product-section before-footer-section single-product-page">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="product-image">
                    <img src="{{ asset('images/1/Product/') }}/argentina.png" alt="Image" class="img-fluid">
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="product-info" id="product-info">
                    <h2 class="heading">{{$product->name}}</h2>
                    <div class="price">
                        @if($product->price !== $priceAfterDiscount && $product->discount_type && $product->discount_value)
                        <span class="original-price" style="text-decoration: line-through; color: #999; margin-right: 8px;">
                            Tk.{{ number_format($product->price, 2) }}
                        </span>
                        @endif
                        <strong class="price-display">Tk.{{ number_format($priceAfterDiscount, 2) }}</strong>
                    </div>
                    <p class="m-0">{{$product->seo_description}}</p>

                    <div class="variation">
                        <div><span>Select Yours</span></div>
                        @foreach($product->variations as $variation)
                            <button class="variation-btn {{ $variation->is_default ? 'active' : '' }}"
                                data-variation-id="{{ $variation->id }}"
                                data-variation-tagline="{{ $variation->tagline }}"
                                data-variation-selling-price="{{ $variation->selling_price }}"
                                data-variation-price-after-discount="{{ $variation->price_after_discount }}"
                                data-variation-discount-type="{{ $variation->discount_type }}"
                                data-variation-discount-value="{{ $variation->discount_value }}">
                                {{ $variation->tagline }}
                                <i class="fa-solid fa-check"></i>
                            </button>
                        @endforeach
                    </div>

                    <div >
                        <div><span>Select Quantity</span></div>
                        <div class="input-group mb-3" style="max-width: 150px;">
                            <button class="qty-minus-btn qty-btn" type="button">&minus;</button>
                            <input id="quantityInput" type="text" class="form-control text-center quantity" value="1" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="qty-plus-btn qty-btn" type="button">&plus;</button>
                        </div>
                    </div>

                    <p>
                        <button 
                        data-product-id="{{$product->id}}"
                        data-product-name="{{$product->name}}"
                        data-product-image="{{$product->image}}"
                        
                        id="addToCartBtn" class="add-to-cart-btn">Add to Cart</button>

                        <a href="index.html" class="buy-now-btn mt-1">Cash on Delivery</a>
                    </p>
                </div>

            </div>
        </div>

        <!-- // details Section -->
        <div class="row">
            <div class="col-12 mt-1 col-lg-8">
                <div class="product-details">
                    <h2 class="heading">Product Details</h2>
                    <div class="row">
                        <div class="col-12 ">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-4">
                <div class="product-faq">
                    <h2 class="heading">Product FAQ</h2>

                    <div class="faq-container">

                        <div class="faq-item">
                            <button class="faq-question">
                                Are these jerseys original or replicas?
                                <span class="icon">+</span>
                            </button>
                            <div class="faq-answer">
                                Our jerseys are premium quality replicas designed to match the look and feel of original team kits, using high-grade fabric and accurate detailing.
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question">
                                What sizes are available?
                                <span class="icon">+</span>
                            </button>
                            <div class="faq-answer">
                                We offer sizes from S to XXL. A detailed size chart is available on every product page to help you find the perfect fit.
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question">
                                Can I customize my jersey with a name and number?
                                <span class="icon">+</span>
                            </button>
                            <div class="faq-answer">
                                Yes! You can add your favorite player’s name and number or your own custom name before adding the jersey to your cart.
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question">
                                How long does delivery take?
                                <span class="icon">+</span>
                            </button>
                            <div class="faq-answer">
                                Orders are usually delivered within 3–7 business days depending on your location and customization.
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question">
                                What is your return policy?
                                <span class="icon">+</span>
                            </button>
                            <div class="faq-answer">
                                We accept returns within 7 days for unused and unwashed jerseys. Customized jerseys are not eligible unless there is a defect.
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var websiteData = {};
    </script>

    <script src="{{ asset('website/js/add-to-cart.js') }}"></script>
    <!-- <script src="{{ asset('website/js/single-product.js') }}"></script> -->
    <script src="{{ asset('website/js/single-product-script.js') }}"></script>
@endsection