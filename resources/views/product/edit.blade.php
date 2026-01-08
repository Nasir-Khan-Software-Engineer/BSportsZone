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
                                <th class="text-center align-middle" style="width: 12%;">Tagline</th>
                                <th class="text-center align-middle" style="width: 18%;">Description</th>
                                <th class="text-center align-middle" style="width: 10%;">Selling Price</th>
                                <th class="text-center align-middle" style="width: 10%;">Stock</th>
                                <th class="text-center align-middle" style="width: 12%;">Available Stock in Warehouse</th>
                                <th class="text-center align-middle" style="width: 8%;">Status</th>
                                <th class="text-center align-middle" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($product->variations) && $product->variations->count() > 0)
                                @foreach($product->variations as $variation)
                                @php
                                    $isEditable = $variation->status == 'active' && !($variation->has_sales_items ?? false);
                                @endphp
                                <tr data-variation-id="{{ $variation->id }}" class="{{ $variation->status == 'inactive' ? 'table-secondary' : '' }}" data-has-sales="{{ $variation->has_sales_items ? 'true' : 'false' }}" data-is-editable="{{ $isEditable ? 'true' : 'false' }}">
                                    <td>
                                        <input type="text" class="form-control form-control-sm variation-tagline" value="{{ $variation->tagline }}" data-variation-id="{{ $variation->id }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm variation-description" value="{{ $variation->description ?? '' }}" data-variation-id="{{ $variation->id }}" readonly>
                                    </td>
                                    <td>
                                        @if($variation->status == 'active')
                                        <div class="d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger price-decrease-btn" data-variation-id="{{ $variation->id }}" title="Decrease Price">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" readonly step="0.01" class="form-control form-control-sm variation-selling-price" value="{{ $variation->selling_price }}" data-variation-id="{{ $variation->id }}" style="flex: 1; min-width: 80px;">
                                            <button type="button" class="btn btn-sm btn-outline-success price-increase-btn" data-variation-id="{{ $variation->id }}" title="Increase Price">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                        @else
                                        <input type="number" readonly step="0.01" class="form-control form-control-sm variation-selling-price" value="{{ $variation->selling_price }}" data-variation-id="{{ $variation->id }}">
                                        @endif
                                    </td>
                                    <td>
                                        @if($variation->status == 'active')
                                        <div class="d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger stock-decrease-btn" data-variation-id="{{ $variation->id }}" title="Decrease Stock">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" readonly class="form-control form-control-sm variation-stock" value="{{ $variation->stock }}" data-variation-id="{{ $variation->id }}" style="flex: 1; min-width: 60px;">
                                            <button type="button" class="btn btn-sm btn-outline-success stock-increase-btn" data-variation-id="{{ $variation->id }}" title="Increase Stock">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                        @else
                                        <input type="number" readonly class="form-control form-control-sm variation-stock" value="{{ $variation->stock }}" data-variation-id="{{ $variation->id }}">
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info">{{ $variation->available_stock_in_warehouse ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm variation-status" data-variation-id="{{ $variation->id }}" data-original-status="{{ $variation->status }}" data-original-tagline="{{ $variation->tagline }}">
                                            <option value="active" {{ $variation->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $variation->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        @if($variation->status == 'active')
                                        <button type="button" class="btn btn-sm btn-primary edit-variation" data-variation-id="{{ $variation->id }}" title="Edit Variation"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <button type="button" class="btn btn-sm btn-success save-variation" data-variation-id="{{ $variation->id }}" style="display: none;"><i class="fa-solid fa-save"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger delete-variation" data-variation-id="{{ $variation->id }}"><i class="fa-solid fa-trash"></i></button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-info show-variation" data-variation-id="{{ $variation->id }}" title="Show Variation"><i class="fa-solid fa-eye"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr id="noVariationsRow">
                                    <td colspan="7" class="text-center">No variations found. Click "Add New Variation" to create one.</td>
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
                    <div class="form-group">
                        <label for="variationStatus">Status</label>
                        <select class="form-control rounded" name="status" id="variationStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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
                <button type="button" id="createFreshVariantBtn" class="btn btn-success rounded btn-sm" style="display: none;"><i class="fa-solid fa-plus"></i> Create Fresh Variant</button>
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
    'createFreshVariant': "{{ route('product.variation.create-fresh-variant',['variation' => 'variationID']) }}",
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

    // Edit variation
    $(document).on('click', '.edit-variation', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let isEditable = row.data('is-editable') === true || row.data('is-editable') === 'true';
        let hasSales = row.data('has-sales') === true || row.data('has-sales') === 'true';
        
        // Check if variation is inactive
        if(row.hasClass('table-secondary')){
            toastr.error('Cannot edit inactive variant.');
            return;
        }
        
        // Check if variation can be edited
        if(!isEditable){
            if(hasSales){
                toastr.error('Cannot edit variation. This variation has sales items associated with it.');
            } else {
                toastr.error('Cannot edit this variation.');
            }
            return;
        }
        
        // Make inputs editable - remove readonly attribute and property
        row.find('.variation-tagline').removeAttr('readonly').prop('readonly', false);
        row.find('.variation-description').removeAttr('readonly').prop('readonly', false);
        
        // Add visual indication that fields are editable
        row.find('.variation-tagline, .variation-description').addClass('editing');
        
        // Status dropdown is always enabled (can always change status)
        // No need to enable/disable it here
        
        // Show save button, hide edit button
        row.find('.edit-variation').hide();
        row.find('.save-variation').show();
        
        // Focus on tagline input
        setTimeout(function(){
            row.find('.variation-tagline').focus().select();
        }, 100);
    });

    // Save variation from table
    $(document).on('click', '.save-variation', function() {
        let variationId = $(this).data('variation-id');
        WinPos.Product.updateVariationFromTable(variationId);
    });

    // Save status change automatically
    $(document).on('change', '.variation-status', function() {
        let variationId = $(this).data('variation-id');
        let newStatus = $(this).val();
        let originalStatus = $(this).data('original-status');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let hasSales = row.data('has-sales') === true || row.data('has-sales') === 'true';
        
        // If status hasn't changed, do nothing
        if(newStatus === originalStatus){
            return;
        }
        
        // If variation has sales items, only save status (status-only update)
        if(hasSales){
            WinPos.Product.updateVariationStatusOnly(variationId, newStatus);
        } else {
            // If variation doesn't have sales, check if we're in edit mode
            // If save button is visible, don't auto-save (let user click save)
            // If save button is hidden, auto-save the status change
            if(row.find('.save-variation').is(':visible')){
                // In edit mode, don't auto-save - let user click save button
                return;
            } else {
                // Not in edit mode, auto-save status change
                WinPos.Product.updateVariationStatusOnly(variationId, newStatus);
            }
        }
    });

    // Delete variation
    $(document).on('click', '.delete-variation', function() {
        if (confirm("Are you sure you want to delete this variation?")) {
            let variationId = $(this).data('variation-id');
            WinPos.Product.deleteVariation(variationId);
        }
    });

    // Show variation (for inactive variants - placeholder for future use)
    $(document).on('click', '.show-variation', function() {
        let variationId = $(this).data('variation-id');
        // Placeholder for future implementation
        toastr.info('Show variation feature will be implemented in the future.');
    });

    // Stock increase/decrease buttons
    $(document).on('click', '.stock-increase-btn, .stock-decrease-btn', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        // Check if variation is inactive
        if(row.hasClass('table-secondary')){
            toastr.error('Cannot update stock for inactive variant.');
            return;
        }
        WinPos.Product.openStockUpdateModal(variationId);
    });

    // Price increase/decrease buttons
    $(document).on('click', '.price-increase-btn, .price-decrease-btn', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        // Check if variation is inactive
        if(row.hasClass('table-secondary')){
            toastr.error('Cannot update price for inactive variant.');
            return;
        }
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

    // Create fresh variant
    $(document).on('click', '#createFreshVariantBtn', function() {
        let variationId = $(this).data('variation-id');
        if(confirm('Are you sure you want to create a fresh variant? This will mark the current variant as inactive and transfer all stock to the new variant.')){
            WinPos.Product.createFreshVariant(variationId);
        }
    });
});
</script>
@endsection

