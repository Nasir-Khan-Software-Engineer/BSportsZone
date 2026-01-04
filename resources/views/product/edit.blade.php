@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Edit Product</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('product.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <!-- Product Edit Form -->
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <form id="productEditForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="productId" name="product_id" value="{{ $product->id ?? '' }}">
                        <div class="row">
                            <div class="col-12 col-lg-4 form-group">
                                <label for="editProductCode">Code*</label>
                                <input required type="text" class="form-control rounded" name="code" id="editProductCode" value="{{ $product->code ?? '' }}">
                            </div>

                            <div class="col-12 col-lg-8 form-group">
                                <label for="editProductName">Name*</label>
                                <input required type="text" class="form-control rounded" name="name" id="editProductName" value="{{ $product->name ?? '' }}">
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="editProductUnit">Unit</label>
                                <select class="form-control rounded" name="unit_id" id="editProductUnit">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ ($product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="editProductBrand">Brand</label>
                                <select class="form-control rounded" name="brand_id" id="editProductBrand">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ ($product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="editProductSupplier">Supplier</label>
                                <select class="form-control rounded" name="supplier_id" id="editProductSupplier">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ ($product->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 form-group">
                                <label for="editProductCategory">Category*</label>
                                <select style="height: 120px;" multiple class="form-control rounded" name="category" id="editProductCategory" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ in_array($category->id, $product->categories->pluck('id')->toArray() ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 form-group">
                                <label for="editProductDescription">Description</label>
                                <textarea name="description" id="editProductDescription" class="form-control rounded" cols="30" rows="5" placeholder="Product Description">{{ $product->description ?? '' }}</textarea>
                            </div>

                            <div class="col-12">
                                <button type="button" id="updateProduct" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Variations Table -->
            <div class="card border">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Product Variations</h5>
                    <button type="button" id="addNewVariation" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> Add New Variation</button>
                </div>
                <div class="card-body p-1">
                    <table class="table table-bordered" id="variationsTable">
                        <thead>
                            <tr>
                                <th class="text-center align-middle" style="width: 15%;">Tagline</th>
                                <th class="text-center align-middle" style="width: 20%;">Description</th>
                                <th class="text-center align-middle" style="width: 10%;">Selling Price</th>
                                <th class="text-center align-middle" style="width: 10%;">Stock</th>
                                <th class="text-center align-middle" style="width: 10%;">Status</th>
                                <th class="text-center align-middle" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($product->variations) && $product->variations->count() > 0)
                                @foreach($product->variations as $variation)
                                <tr data-variation-id="{{ $variation->id }}">
                                    <td>
                                        <input type="text" class="form-control form-control-sm variation-tagline" value="{{ $variation->tagline }}" data-variation-id="{{ $variation->id }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm variation-description" value="{{ $variation->description ?? '' }}" data-variation-id="{{ $variation->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger price-decrease-btn" data-variation-id="{{ $variation->id }}" title="Decrease Price">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" readonly step="0.01" class="form-control form-control-sm variation-selling-price" value="{{ $variation->selling_price }}" data-variation-id="{{ $variation->id }}" style="flex: 1; min-width: 80px;">
                                            <button type="button" class="btn btn-sm btn-outline-success price-increase-btn" data-variation-id="{{ $variation->id }}" title="Increase Price">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger stock-decrease-btn" data-variation-id="{{ $variation->id }}" title="Decrease Stock">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" readonly class="form-control form-control-sm variation-stock" value="{{ $variation->stock }}" data-variation-id="{{ $variation->id }}" style="flex: 1; min-width: 60px;">
                                            <button type="button" class="btn btn-sm btn-outline-success stock-increase-btn" data-variation-id="{{ $variation->id }}" title="Increase Stock">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm variation-status" data-variation-id="{{ $variation->id }}">
                                            <option value="active" {{ $variation->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $variation->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-success save-variation" data-variation-id="{{ $variation->id }}"><i class="fa-solid fa-save"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger delete-variation" data-variation-id="{{ $variation->id }}"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr id="noVariationsRow">
                                    <td colspan="6" class="text-center">No variations found. Click "Add New Variation" to create one.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Variation Modal -->
<div class="modal fade" id="addVariationModal" tabindex="-1" role="dialog" aria-labelledby="addVariationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="addVariationModalLabel">Add New Variation</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addVariationForm">
                    <input type="hidden" id="variationProductId" name="product_id" value="{{ $product->id ?? '' }}">
                    <div class="form-group">
                        <label for="variationTagline">Tagline*</label>
                        <input required type="text" class="form-control rounded" name="tagline" id="variationTagline" placeholder="e.g., M Size, L Size, XL Size">
                    </div>
                    <div class="form-group">
                        <label for="variationDescription">Description</label>
                        <textarea class="form-control rounded" name="description" id="variationDescription" rows="3" placeholder="Variation description"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="variationSellingPrice">Selling Price*</label>
                            <input required type="number" step="0.01" class="form-control rounded" name="selling_price" id="variationSellingPrice" placeholder="0.00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="variationStock">Stock*</label>
                            <input required type="number" min="1" class="form-control rounded" name="stock" id="variationStock" value="1">
                        </div>
                        <div class="col-6 form-group">
                            <label for="variationStatus">Status</label>
                            <select class="form-control rounded" name="status" id="variationStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="saveVariation" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal fade" id="stockUpdateModal" tabindex="-1" role="dialog" aria-labelledby="stockUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="stockUpdateModalLabel">Update Stock - <span id="modalVariationTagline"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Variation Information Section -->
                <div class="card border mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Variation Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2"><strong>Variation Name:</strong> <span id="variationNameDisplay">-</span></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><strong>Current Selling Price:</strong> <span id="currentSellingPriceDisplay">-</span></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><strong>Current Stocks:</strong> <span id="currentStocksDisplay">-</span></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><strong>Already Sales QTY:</strong> <span id="alreadySalesQtyDisplay">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Update Section -->
                <div class="card border">
                    <div class="card-header">
                        <h6 class="mb-0">Stock Update</h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="purchaseItemsContainer">
                            <p class="text-muted text-center p-3">Loading purchase items...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Confirmation Modal -->
<div class="modal fade" id="stockUpdateConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="stockUpdateConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="stockUpdateConfirmationModalLabel">Confirm Stock Update</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> No, Close</button>
                <button type="button" id="confirmStockUpdate" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-check"></i> Yes, Update Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Price Update Modal -->
<div class="modal fade" id="priceUpdateModal" tabindex="-1" role="dialog" aria-labelledby="priceUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="priceUpdateModalLabel">Update Price - <span id="priceModalVariationTagline"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="priceUpdateContainer">
                    <p class="text-muted text-center">Loading price information...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="savePriceUpdate" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" style="display: none;"><i class="fa-solid fa-floppy-disk"></i> Update Price</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/product/product-script.js'])
<script>
let productUrls = {
    'updateProduct': "{{ route('product.update',['product' => $product->id ?? 'productID']) }}",
    'storeVariation': "{{ route('product.variation.store') }}",
    'updateVariation': "{{ route('product.variation.update',['variation' => 'variationID']) }}",
    'deleteVariation': "{{ route('product.variation.destroy',['variation' => 'variationID']) }}",
    'getPurchaseItems': "{{ route('product.variation.purchase-items',['variation' => 'variationID']) }}",
    'addStockFromPurchase': "{{ route('product.variation.add-stock',['variation' => 'variationID']) }}",
    'getPriceUpdateInfo': "{{ route('product.variation.price-update-info',['variation' => 'variationID']) }}",
};

let productId = {{ $product->id ?? 'null' }};

$(document).ready(function() {
    // Update product
    $("#updateProduct").on('click', function() {
        WinPos.Product.updateProduct(productId);
    });

    // Add new variation
    $("#addNewVariation").on('click', function() {
        $("#addVariationForm")[0].reset();
        $("#variationProductId").val(productId);
        WinPos.Common.showBootstrapModal("addVariationModal");
    });

    // Save variation
    $("#saveVariation").on('click', function() {
        WinPos.Product.saveVariation();
    });

    // Save variation from table
    $(document).on('click', '.save-variation', function() {
        let variationId = $(this).data('variation-id');
        WinPos.Product.updateVariationFromTable(variationId);
    });

    // Delete variation
    $(document).on('click', '.delete-variation', function() {
        if (confirm("Are you sure you want to delete this variation?")) {
            let variationId = $(this).data('variation-id');
            WinPos.Product.deleteVariation(variationId);
        }
    });

    // Stock increase/decrease buttons
    $(document).on('click', '.stock-increase-btn, .stock-decrease-btn', function() {
        let variationId = $(this).data('variation-id');
        WinPos.Product.openStockUpdateModal(variationId);
    });

    // Price increase/decrease buttons
    $(document).on('click', '.price-increase-btn, .price-decrease-btn', function() {
        let variationId = $(this).data('variation-id');
        WinPos.Product.openPriceUpdateModal(variationId);
    });

    // Save price update
    $(document).on('click', '#savePriceUpdate', function() {
        let variationId = $('#priceUpdateVariationId').val();
        let newPrice = parseFloat($('#newSellingPrice').val());
        
        if(!newPrice || newPrice < 0){
            toastr.error('Please enter a valid price.');
            return;
        }
        
        WinPos.Product.updateVariationPrice(variationId, newPrice);
    });
});
</script>
@endsection

