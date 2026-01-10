@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Purchase Details</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('stock.purchase.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
                <a href="{{ route('stock.purchase.edit', $purchase->id) }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
            </div>
        </div>
        <div class="card-body p-1">
            <!-- Purchase Information -->
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Purchase Date:</strong> {{ $purchase->formattedDate }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Invoice Number:</strong> {{ $purchase->invoice_number ?? '-' }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Purchase Name:</strong> {{ $purchase->name }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Status:</strong> 
                            <span class="badge badge-{{ $purchase->status == 'confirmed' ? 'success' : 'warning' }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Supplier:</strong> {{ $purchase->supplier->name ?? '-' }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Product:</strong> {{ $purchase->product->name ?? '-' }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Total Quantity:</strong> {{ $purchase->total_qty }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Total Cost:</strong> {{ number_format($purchase->total_cost_price, 2) }}
                        </div>
                        <div class="col-12 col-md-4 mb-1">
                            <strong>Cration:</strong> {{ $purchase->createdBy }} | {{ $purchase->formattedDate }} {{ $purchase->formattedTime }}
                        </div>
                        @if($purchase->description)
                        <div class="col-12 mb-3">
                            <strong>Description:</strong>
                            <p>{{ $purchase->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Purchase Items -->
            <div class="card border">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Items</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Tag Line</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Cost Price</th>
                                <th class="text-center">Purchased Qty</th>
                                <th class="text-center">Unallocated Qty</th>
                                <th class="text-center">Allocated Qty</th>
                                <th class="text-center">Sold Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->purchaseItems as $item)
                            <tr>
                                <td>{{ $item->variation->tagline ?? '-' }}</td>
                                <td class="text-center">
                                    @php
                                        $status = $item->status ?? 'reserved';
                                        $statusLabel = match($status) {
                                            'sellable' => 'Sellable',
                                            'reserved' => 'Reserved',
                                            default => ucfirst($status)
                                        };
                                        $statusBadge = match($status) {
                                            'sellable' => 'success',
                                            'reserved' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusBadge }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center">{{ number_format($item->cost_price, 2) }}</td>
                                <td class="text-center">{{ $item->purchased_qty }}</td>
                                <td class="text-center">{{ $item->unallocated_qty }}</td>
                                <td class="text-center">{{ $item->purchased_qty - $item->unallocated_qty }}</td>
                                <td class="text-center">{{ $item->sold_qty ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No items found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@endsection

