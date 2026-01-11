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
            <!-- Product Edit Form with Collapsible Tab -->
            <div class="card border mb-3">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#productInfoCollapse" aria-expanded="false" aria-controls="productInfoCollapse">
                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                        <span>Product Information</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </h5>
                </div>
                <div class="collapse" id="productInfoCollapse">
                    <div class="card-body">
                        <form id="productEditForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="productId" name="product_id" value="{{ $product->id ?? '' }}">
                            <div class="row">
                                <!-- Left Side: Code, Unit, Brand -->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="editProductCode">Code*</label>
                                        <input required type="text" class="form-control rounded" name="code" id="editProductCode" value="{{ $product->code ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="editProductUnit">Unit</label>
                                        <select class="form-control rounded" name="unit_id" id="editProductUnit">
                                            <option value="">Select Unit</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ ($product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="editProductBrand">Brand</label>
                                        <select class="form-control rounded" name="brand_id" id="editProductBrand">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ ($product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Right Side: Name, Category -->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="editProductName">Name*</label>
                                        <input required type="text" class="form-control rounded" name="name" id="editProductName" value="{{ $product->name ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="editProductCategory">Category*</label>
                                        <select style="height: 120px;" multiple class="form-control rounded" name="category" id="editProductCategory" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ in_array($category->id, $product->categories->pluck('id')->toArray() ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Description: Full Width -->
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
                                <th class="text-center align-middle" style="width: 8%;">Available Stock in Warehouse</th>
                                <th class="text-center align-middle" style="width: 10%;">Discount</th>
                                <th class="text-center align-middle" style="width: 8%;">Status</th>
                                <th class="text-center align-middle" style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($product->variations) && $product->variations->count() > 0)
                                @foreach($product->variations as $variation)
                                @php
                                    $isEditable = $variation->status == 'active' && !($variation->has_sales_items ?? false);
                                    $isClosed = $variation->status == 'closed';
                                    $isInactive = $variation->status == 'inactive';
                                @endphp
                                <tr data-variation-id="{{ $variation->id }}" class="{{ ($isInactive || $isClosed) ? 'table-secondary' : '' }}" data-has-sales="{{ $variation->has_sales_items ? 'true' : 'false' }}" data-status="{{ $variation->status }}" data-full-description="{{ $variation->description ?? '' }}" data-discount-type="{{ $variation->discount_type ?? '' }}" data-discount-value="{{ $variation->discount_value ?? '' }}">
                                    <td>
                                        {{ $variation->tagline }}
                                    </td>
                                    <td>
                                        @php
                                            $description = $variation->description ?? '';
                                            $displayDescription = strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                        @endphp
                                        {{ $displayDescription ?: '-' }}
                                    </td>
                                    <td>
                                        @if($variation->status != 'closed')
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
                                        @if($variation->status != 'closed')
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
                                    <td class="text-center align-middle">
                                        @if($variation->discount_type && $variation->discount_value)
                                            @if($variation->discount_type == 'percentage')
                                                {{ number_format($variation->discount_value, 2) }}%
                                            @else
                                                {{ number_format($variation->discount_value, 2) }}tk
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($variation->status == 'closed')
                                            <span class="badge badge-secondary">Closed</span>
                                        @elseif($variation->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($variation->status == 'closed')
                                        <span class="badge badge-secondary">Closed</span>
                                        @else
                                        @php
                                            $canDelete = $variation->status == 'active' && $variation->stock == 0 && !($variation->has_sales_items ?? false);
                                        @endphp
                                        <button type="button" class="btn btn-sm btn-primary edit-variation" data-variation-id="{{ $variation->id }}" title="Edit Variation"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger delete-variation" data-variation-id="{{ $variation->id }}" data-can-delete="{{ $canDelete ? 'true' : 'false' }}" {{ !$canDelete ? 'disabled' : '' }} title="{{ !$canDelete ? 'Cannot delete: ' . ($variation->status != 'active' ? 'Variation is not active' : ($variation->stock > 0 ? 'Variation has stock' : 'Variation has sales')) : 'Delete Variation' }}"><i class="fa-solid fa-trash"></i></button>
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
                        <label for="variationDiscountType">Discount Type</label>
                        <select class="form-control rounded" name="discount_type" id="variationDiscountType">
                            <option value="">No Discount</option>
                            <option value="fixed">Fixed</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="variationDiscountValue">Discount Value</label>
                        <input type="number" step="0.01" min="0" class="form-control rounded" name="discount_value" id="variationDiscountValue" placeholder="0.00">
                        <small class="form-text text-muted" id="addDiscountValueHelp">Enter discount value based on selected type</small>
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
    <div class="modal-dialog modal-xl" role="document">
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
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal" id="closeStockUpdateModal"><i class="fa-solid fa-xmark"></i> Close</button>
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

<!-- Edit Variation Modal -->
<div class="modal fade" id="editVariationModal" tabindex="-1" role="dialog" aria-labelledby="editVariationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="editVariationModalLabel">Edit Variation</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editVariationForm">
                    <input type="hidden" id="editVariationId" name="variation_id">
                    <div class="form-group">
                        <label for="editVariationTagline">Tagline*</label>
                        <input required type="text" class="form-control rounded" name="tagline" id="editVariationTagline" placeholder="e.g., M Size, L Size, XL Size">
                    </div>
                    <div class="form-group">
                        <label for="editVariationDescription">Description</label>
                        <textarea class="form-control rounded" name="description" id="editVariationDescription" rows="3" placeholder="Variation description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editVariationDiscountType">Discount Type</label>
                        <select class="form-control rounded" name="discount_type" id="editVariationDiscountType">
                            <option value="">No Discount</option>
                            <option value="fixed">Fixed</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editVariationDiscountValue">Discount Value</label>
                        <input type="number" step="0.01" min="0" class="form-control rounded" name="discount_value" id="editVariationDiscountValue" placeholder="0.00">
                        <small class="form-text text-muted" id="discountValueHelp">Enter discount value based on selected type</small>
                    </div>
                    <div class="form-group">
                        <label for="editVariationStatus">Status</label>
                        <select class="form-control rounded" name="status" id="editVariationStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="updateVariationBtn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update Variation</button>
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
    'moveStockToPurchase': "{{ route('product.variation.move-stock',['variation' => 'variationID']) }}",
    'getPriceUpdateInfo': "{{ route('product.variation.price-update-info',['variation' => 'variationID']) }}",
    'createFreshVariant': "{{ route('product.variation.create-fresh-variant',['variation' => 'variationID']) }}",
};

let productId = {{ $product->id ?? 'null' }};

$(document).ready(function() {
    // Handle collapsible Product Information tab icon rotation
    $('#productInfoCollapse').on('show.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $('#productInfoCollapse').on('hide.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    
    // Update product
    $("#updateProduct").on('click', function() {
        WinPos.Product.updateProduct(productId);
    });

    // Add new variation
    $("#addNewVariation").on('click', function() {
        $("#addVariationForm")[0].reset();
        $("#variationProductId").val(productId);
        // Reset discount fields
        $('#variationDiscountType').val('');
        $('#variationDiscountValue').val('');
        WinPos.Product.updateAddDiscountFields();
        WinPos.Common.showBootstrapModal("addVariationModal");
    });
    
    // Handle discount type change in add modal
    $(document).on('change', '#variationDiscountType', function() {
        WinPos.Product.updateAddDiscountFields();
    });

    // Save variation
    $("#saveVariation").on('click', function() {
        WinPos.Product.saveVariation();
    });

    // Edit variation - open modal
    $(document).on('click', '.edit-variation', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let status = row.data('status');
        
        // Check if variation is closed
        if(status === 'closed'){
            toastr.error('Cannot edit closed variation.');
            return;
        }
        
        // Load variation data and open modal
        WinPos.Product.openEditVariationModal(variationId);
    });

    // Update variation from modal
    $(document).on('click', '#updateVariationBtn', function() {
        WinPos.Product.updateVariationFromModal();
    });
    
    // Handle discount type change
    $(document).on('change', '#editVariationDiscountType', function() {
        WinPos.Product.updateDiscountFields();
    });


    // Delete variation - only handle clicks on enabled buttons
    $(document).on('click', '.delete-variation:not(:disabled)', function() {
        let variationId = $(this).data('variation-id');
        WinPos.Product.deleteVariation(variationId);
    });

    // Show variation (for inactive variants - placeholder for future use)
    $(document).on('click', '.show-variation', function() {
        let variationId = $(this).data('variation-id');
        // Placeholder for future implementation
        toastr.info('Show variation feature will be implemented in the future.');
    });

    // Stock increase/decrease buttons
    $(document).on('click', '.stock-increase-btn', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let status = row.data('status');
        // Check if variation is closed
        if(status === 'closed'){
            toastr.error('Cannot update stock for closed variant.');
            return;
        }
        WinPos.Product.openStockUpdateModal(variationId, 'add');
    });

    $(document).on('click', '.stock-decrease-btn', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let status = row.data('status');
        // Check if variation is closed
        if(status === 'closed'){
            toastr.error('Cannot update stock for closed variant.');
            return;
        }
        WinPos.Product.openStockUpdateModal(variationId, 'move');
    });

    // Price increase/decrease buttons
    $(document).on('click', '.price-increase-btn, .price-decrease-btn', function() {
        let variationId = $(this).data('variation-id');
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let status = row.data('status');
        // Check if variation is closed
        if(status === 'closed'){
            toastr.error('Cannot update price for closed variant.');
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

