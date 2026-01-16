@extends('public.master')

@section('content')
<div class="untree_co-section product-section before-footer-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Shop Page Header -->
                <div class="shop-header text-center py-3">
                    <h1 class="shop-title">Shop Premium Sports Jerseys</h1>
                    <p class="shop-description">
                        Discover high-quality, stylish jerseys designed for comfort, performance, and everyday wear. <br>
                        Find your perfect fit and represent your team with pride.
                    </p>
                </div>

            </div>
        </div>




        <div class="row">
            @forelse($products as $product)
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
                    <br>
                    <button type="button" class="add-to-cart-btn"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-image="{{ $product->image }}"
                    data-product-quantity="1"

                    data-variation-id="{{ $product->default_variation_id }}"
                    data-variation-price-after-discount="{{ $product->default_variation_price_after_discount }}"
                    data-variation-selling-price="{{ $product->default_variation_selling_price }}"
                    data-variation-discount-type="{{ $product->default_variation_discount_type }}"
                    data-variation-discount-value="{{ $product->default_variation_discount_value }}"
                    data-variation-tagline="{{ $product->default_variation_tagline }}"
                    
                    >Add to Cart</button>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <p class="text-muted">No products available at the moment.</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="row mt-2">
            <div class="col-12">
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        {{-- Previous Page Link --}}
                        @if($products->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                        @else
                        <li class="page-item">
                            @php
                            $prevPage = $currentPage - 1;
                            $prevUrl = $prevPage == 1 ? url('/shop') : url('/shop/' . $prevPage);
                            @endphp
                            <a class="page-link" href="{{ $prevUrl }}">Previous</a>
                        </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @for($page = 1; $page <= $lastPage; $page++) @if($page==$currentPage) <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                            </li>
                            @else
                            @php
                            // For page 1, use /shop, for others use /shop/{page}
                            $pageUrl = $page == 1 ? url('/shop') : url('/shop/' . $page);
                            @endphp
                            <li class="page-item">
                                <a class="page-link" href="{{ $pageUrl }}">{{ $page }}</a>
                            </li>
                            @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if($products->hasMorePages())
                            <li class="page-item">
                                @php
                                $nextPage = $currentPage + 1;
                                $nextUrl = url('/shop/' . $nextPage);
                                @endphp
                                <a class="page-link" href="{{ $nextUrl }}">Next</a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link">Next</span>
                            </li>
                            @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var websiteData = {};
    </script>
    <script src="{{ asset('website/js/add-to-cart.js') }}"></script>

@endSection