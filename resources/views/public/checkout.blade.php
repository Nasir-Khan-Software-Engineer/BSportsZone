@extends('public.master')


@section('content')
<div class="untree_co-section">
    <div class="container pb-5">

        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <h2 class="h3 mb-2 text-black">Your Order</h2>
                <div class="p-3 p-lg-3 border bg-white">
                    <table class="table site-block-order-table" id="cartTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12  col-lg-8 offset-lg-2 mt-2">
                <h2 class="h3 mb-2 text-black">Billing Details</h2>
                <div class="p-2 p-lg-3 border bg-white">
                    <form action="{{ route('placeOrder') }}" method="POST" id="checkoutForm" class="checkoutForm">
                        @csrf
                        <input type="hidden" name="cartData" id="cartData">
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="customerName" class="text-black">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerName" name="customerName" placeholder="Your Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="customerPhone" class="text-black">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerPhone" name="customerPhone" placeholder="01637017926">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deliveryArea" class="text-black">Delivery Area <span class="text-danger">*</span></label>
                            <select id="deliveryArea" name="deliveryArea" class="form-control">
                                <option value="">Select an Area</option>
                                <option value="inside dhaka">Inside Dhaka (70 Tk)</option>
                                <option value="outside dhaka">Outside Dhaka (140 Tk)</option>
                            </select>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="customerAddress" class="text-black">Address <span class="text-danger">*</span></label>
                                <textarea name="customerAddress" id="customerAddress" cols="30" rows="3" class="form-control" placeholder="Street address"></textarea>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <button type="submit" class="w-100 place-order-btn" id="placeOrderbtn">Place Order <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- </form> -->
    </div>
</div>
@endsection

@section('scripts')
<script>
var websiteData = {};
</script>

<script src="{{ asset('website/js/add-to-cart.js') }}"></script>
<script src="{{ asset('website/js/single-product-script.js') }}"></script>
<script>
$(document).ready(function() {
    Website.AddToCart.renderWebsiteCartTable();

    let websiteCartObject = Website.AddToCart.getWebsiteCartObject();
    $('#cartData').val(JSON.stringify(websiteCartObject));



    $('#deliveryArea').on('change', function () {

        let area = $(this).val();
        let deliveryCharge = 0;

        if (area === 'inside dhaka') {
            deliveryCharge = 70;
        } else if (area === 'outside dhaka') {
            deliveryCharge = 140;
        }

        Website.AddToCart.updateDeliveryCharge(deliveryCharge);
    });

});


$(document).on('click', '.remove-cart-item', function () {

    let productId = $(this).data('product-id');
    let variationId = $(this).data('variation-id');
    let product = {
        id: productId,
        variation_id: variationId
    }
    Website.AddToCart.removeProductFromWebsiteCart(product);
    Website.AddToCart.setWebsiteCartCount();
    Website.AddToCart.renderWebsiteCartTable();

    Website.Common.showToastMessage('success', 'Product removed from cart!');
});




</script>
@endsection