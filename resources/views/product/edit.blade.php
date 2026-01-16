@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Edit Product</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn {{ $product->is_published ? 'btn-success' : 'btn-secondary' }} rounded btn-sm toggle-published" data-product-id="{{ $product->id }}" data-published="{{ $product->is_published ? '1' : '0' }}">
                    <i class="fa-solid {{ $product->is_published ? 'fa-check-circle' : 'fa-times-circle' }}"></i> 
                    {{ $product->is_published ? 'Published' : 'Unpublished' }}
                </button>
                <button type="button" class="btn {{ ($product->is_home ?? false) ? 'btn-info' : 'btn-secondary' }} rounded btn-sm toggle-home" data-product-id="{{ $product->id }}" data-is-home="{{ ($product->is_home ?? false) ? '1' : '0' }}">
                    <i class="fa-solid fa-home"></i> 
                    {{ ($product->is_home ?? false) ? 'For Home' : 'Mark for Home' }}
                </button>
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
                                        <label for="editProductSlug">Slug* <small>(Auto-generated from name, max 100 characters)</small></label>
                                        <input required type="text" class="form-control rounded" name="slug" id="editProductSlug" value="{{ $product->slug ?? '' }}" maxlength="100">
                                        <small class="form-text text-muted">This will be used in the product URL. You can edit it if needed.</small>
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

                                <!-- Default Price and Discount: Full Width -->
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-lg-4 form-group">
                                            <label for="editProductDefaultPrice">Default Price</label>
                                            <input type="number" step="0.01" min="0" class="form-control rounded" name="price" id="editProductDefaultPrice" value="{{ $product->price ?? 0 }}" placeholder="0.00">
                                        </div>

                                        <div class="col-12 col-lg-4 form-group">
                                            <label for="editProductDefaultDiscountType">Default Discount Type</label>
                                            <select class="form-control rounded" name="discount_type" id="editProductDefaultDiscountType">
                                                <option value="">No Discount</option>
                                                <option value="fixed" {{ ($product->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="percentage" {{ ($product->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-lg-4 form-group">
                                            <label for="editProductDefaultDiscount">Default Discount</label>
                                            <input type="number" step="0.01" min="0" class="form-control rounded" name="discount_value" id="editProductDefaultDiscount" value="{{ $product->discount_value ?? '' }}" placeholder="0.00">
                                            <small class="form-text text-muted" id="defaultDiscountValueHelp">Enter discount value based on selected type</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description: Full Width -->
                                <div class="col-12 form-group">
                                    <label for="editProductDescription">Description</label>
                                    <textarea name="description" id="editProductDescription" class="form-control rounded summernote" cols="30" rows="5" placeholder="Product Description">{!! $product->description ?? '' !!}</textarea>
                                </div>

                                <div class="col-12">
                                    <button type="button" id="updateProduct" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update Product</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Images Section -->
            <div class="card border mb-3">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#productImagesCollapse" aria-expanded="false" aria-controls="productImagesCollapse">
                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                        <span>Images</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </h5>
                </div>
                <div class="collapse" id="productImagesCollapse">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Product Images</h6>
                            <button type="button" id="addNewProductImage" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> Add New Image</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="productImagesTable">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%;">ID</th>
                                        <th class="text-center align-middle" style="width: 25%;">Image Name</th>
                                        <th class="text-center align-middle" style="width: 15%;">Preview</th>
                                        <th class="text-center align-middle" style="width: 10%;">Default</th>
                                        <th class="text-center align-middle" style="width: 15%;">Created At</th>
                                        <th class="text-center align-middle" style="width: 20%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Images will be loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Section -->
            <div class="card border mb-3">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#productSeoCollapse" aria-expanded="false" aria-controls="productSeoCollapse">
                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                        <span>SEO</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </h5>
                </div>
                <div class="collapse" id="productSeoCollapse">
                    <div class="card-body">
                        <form id="productSeoForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="seoProductId" name="product_id" value="{{ $product->id ?? '' }}">
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label for="productSeoKeyword">SEO Keywords</label>
                                    <textarea name="seo_keyword" id="productSeoKeyword" class="form-control rounded" rows="3" placeholder="Enter SEO keywords separated by commas">{{ $product->seo_keyword ?? '' }}</textarea>
                                    <small class="form-text text-muted">Enter keywords separated by commas (e.g., product, item, category)</small>
                                </div>
                                <div class="col-12 form-group">
                                    <label for="productSeoDescription">SEO Description</label>
                                    <textarea maxlength="160" name="seo_description" id="productSeoDescription" class="form-control rounded" rows="4" placeholder="Enter SEO description">{{ $product->seo_description ?? '' }}</textarea>
                                    <small class="form-text text-muted">Enter a brief description for search engines (recommended: 150-160 characters)</small>
                                </div>
                                <div class="col-12">
                                    <button type="button" id="updateProductSeo" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update SEO</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            <div class="card border mb-3">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#relatedProductsCollapse" aria-expanded="false" aria-controls="relatedProductsCollapse">
                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                        <span>Related Products</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </h5>
                </div>
                <div class="collapse" id="relatedProductsCollapse">
                    <div class="card-body">
                        <!-- Add Related Product Form -->
                        <div class="row mb-3">
                            <div class="col-12 col-md-8">
                                <label for="relatedProductSelect">Select Product</label>
                                <select class="form-control rounded" id="relatedProductSelect" name="related_product_id">
                                    <option value="">Select a product...</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <button type="button" id="addRelatedProductBtn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm w-100"><i class="fa-solid fa-plus"></i> Add</button>
                            </div>
                        </div>

                        <!-- Related Products Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="relatedProductsTable">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%;">ID</th>
                                        <th class="text-center align-middle" style="width: 20%;">Code</th>
                                        <th class="text-center align-middle" style="width: 60%;">Name</th>
                                        <th class="text-center align-middle" style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Related products will be loaded dynamically -->
                                    <tr id="noRelatedProductsRow">
                                        <td colspan="4" class="text-center">No related products found. Select a product and click "Add" to add one.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
                                <th class="text-center align-middle" style="width: 8%;">Warehouse</th>
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

<!-- Add Product Image Modal -->
<div class="modal fade" id="addProductImageModal" tabindex="-1" role="dialog" aria-labelledby="addProductImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductImageModalLabel">Add New Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#addProductImageModal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProductImageForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productImageName">Select Image <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="productImageName" name="image_name" list="productImagesList" placeholder="Type to search and select image" autocomplete="off" required>
                        <small class="form-text text-muted">Select an image from the Product images</small>
                    </div>
                    <div class="form-group">
                        <label>Preview</label>
                        <div id="productImagePreview" style="width: 100%; height: 300px; border: 2px dashed #ddd; background-size: contain; background-position: center; background-repeat: no-repeat; background-color: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted">Image preview will appear here</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#addProductImageModal').modal('hide');">Close</button>
                    <button type="submit" class="btn thm-btn-bg thm-btn-text-color">Add Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Product Image Modal -->
<div class="modal fade" id="showProductImageModal" tabindex="-1" role="dialog" aria-labelledby="showProductImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showProductImageModalLabel">Image Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#showProductImageModal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="showProductImagePreview" style="width: 100%; height: 300px; border: 2px solid #ddd; background-size: contain; background-position: center; background-repeat: no-repeat; background-color: #f9f9f9;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID:</th>
                                <td id="showProductImageId">-</td>
                            </tr>
                            <tr>
                                <th>Image Name:</th>
                                <td id="showProductImageName">-</td>
                            </tr>
                            <tr>
                                <th>Size:</th>
                                <td id="showProductImageSize">-</td>
                            </tr>
                            <tr>
                                <th>Is Default:</th>
                                <td id="showProductImageDefault">-</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td id="showProductImageCreatedAt">-</td>
                            </tr>
                            <tr>
                                <th>Created By:</th>
                                <td id="showProductImageCreatedBy">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#showProductImageModal').modal('hide');">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Datalist for product images -->
<datalist id="productImagesList">
    <!-- Options will be populated dynamically -->
</datalist>

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
    'getProductImagesList': "{{ route('product.images.list') }}",
    'getProductImages': "{{ route('product.images.get',['product' => $product->id ?? 'productID']) }}",
    'storeProductImage': "{{ route('product.images.store',['product' => $product->id ?? 'productID']) }}",
    'showProductImage': "{{ route('product.images.show',['product' => $product->id ?? 'productID', 'image' => 'imageID']) }}",
    'markProductImageDefault': "{{ route('product.images.mark-default',['product' => $product->id ?? 'productID', 'image' => 'imageID']) }}",
    'deleteProductImage': "{{ route('product.images.destroy',['product' => $product->id ?? 'productID', 'image' => 'imageID']) }}",
    'updateProductSeo': "{{ route('product.update-seo',['product' => $product->id ?? 'productID']) }}",
    'toggleProductPublished': "{{ route('product.toggle-published',['product' => $product->id ?? 'productID']) }}",
    'toggleProductHome': "{{ route('product.toggle-home',['product' => $product->id ?? 'productID']) }}",
    'getRelatedProducts': "{{ route('product.related-products.get',['product' => $product->id ?? 'productID']) }}",
    'getAvailableProducts': "{{ route('product.related-products.available',['product' => $product->id ?? 'productID']) }}",
    'addRelatedProduct': "{{ route('product.related-products.add',['product' => $product->id ?? 'productID']) }}",
    'removeRelatedProduct': "{{ route('product.related-products.remove',['product' => $product->id ?? 'productID', 'relatedProduct' => 'relatedProductID']) }}",
};

let productId = {{ $product->id ?? 'null' }};

$(document).ready(function() {

    // Initialize slug generation
    WinPos.Product.initSlugGeneration();
    
    // Handle collapsible Product Information tab icon rotation
    $('#productInfoCollapse').on('show.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $('#productInfoCollapse').on('hide.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Handle default discount type change
    $(document).on('change', '#editProductDefaultDiscountType', function() {
        WinPos.Product.updateDefaultDiscountFields();
    });

    // Initialize default discount fields on page load
    WinPos.Product.updateDefaultDiscountFields();

    // Handle collapsible Product Images tab icon rotation
    $('#productImagesCollapse').on('show.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        // Load product images when section is expanded
        WinPos.Product.loadProductImages(productId);
    });
    
    $('#productImagesCollapse').on('hide.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Handle collapsible SEO tab icon rotation
    $('#productSeoCollapse').on('show.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $('#productSeoCollapse').on('hide.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Handle collapsible Related Products tab icon rotation
    $('#relatedProductsCollapse').on('show.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        // Load related products and available products when section is expanded
        WinPos.Product.loadRelatedProducts(productId);
        WinPos.Product.loadAvailableProductsForSelect(productId);
    });
    
    $('#relatedProductsCollapse').on('hide.bs.collapse', function () {
        $(this).closest('.card').find('.card-header i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    
    // Update product
    $("#updateProduct").on('click', function() {
        // Validate discount before submitting
        var discountType = $('#editProductDefaultDiscountType').val();
        var discountValue = $('#editProductDefaultDiscount').val();
        var defaultPrice = parseFloat($('#editProductDefaultPrice').val() || 0);
        
        if (discountType && discountValue) {
            if (discountType === 'percentage' && parseFloat(discountValue) > 100) {
                toastr.error('Percentage discount cannot exceed 100%.');
                return;
            }
            if (discountType === 'fixed' && parseFloat(discountValue) > defaultPrice) {
                toastr.error('Fixed discount cannot exceed default price.');
                return;
            }
        }
        
        WinPos.Product.updateProduct(productId);
    });

    // Update discount fields when price changes
    $(document).on('input change', '#editProductDefaultPrice', function() {
        WinPos.Product.updateDefaultDiscountFields();
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

    // Product Images functionality
    $(document).on('click', '#addNewProductImage', function() {
        WinPos.Product.openAddProductImageModal(productId);
    });

    // Handle image name input change for preview
    $(document).on('input change', '#productImageName', function() {
        var imageName = $(this).val();
        WinPos.Product.previewProductImage(imageName);
    });

    // Save product image
    $(document).on('submit', '#addProductImageForm', function(event) {
        event.preventDefault();
        WinPos.Product.saveProductImage();
    });

    // Show product image
    $(document).on('click', '.show-product-image', function() {
        let imageId = $(this).data('image-id');
        WinPos.Product.showProductImage(productId, imageId);
    });

    // Mark as default
    $(document).on('click', '.mark-default-product-image', function() {
        let imageId = $(this).data('image-id');
        WinPos.Product.markProductImageAsDefault(productId, imageId);
    });

    // Delete product image
    $(document).on('click', '.delete-product-image', function() {
        let imageId = $(this).data('image-id');
        WinPos.Product.deleteProductImage(productId, imageId);
    });

    // Toggle published status
    $(document).on('click', '.toggle-published', function() {
        let productId = $(this).data('product-id');
        WinPos.Product.togglePublished(productId, $(this));
    });

    // Toggle home status
    $(document).on('click', '.toggle-home', function() {
        let productId = $(this).data('product-id');
        WinPos.Product.toggleHome(productId, $(this));
    });

    // Update SEO
    $(document).on('click', '#updateProductSeo', function() {
        WinPos.Product.updateSeo(productId);
    });

    // Related Products functionality
    $(document).on('click', '#addRelatedProductBtn', function() {
        let relatedProductId = $('#relatedProductSelect').val();
        if (!relatedProductId) {
            toastr.error('Please select a product.');
            return;
        }
        WinPos.Product.addRelatedProduct(productId, relatedProductId);
    });

    $(document).on('click', '.remove-related-product', function() {
        let relatedProductId = $(this).data('related-product-id');
        if (confirm('Are you sure you want to remove this related product?')) {
            WinPos.Product.removeRelatedProduct(productId, relatedProductId);
        }
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

