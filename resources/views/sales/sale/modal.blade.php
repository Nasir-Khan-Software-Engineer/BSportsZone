<!-- Sale Details Modal -->
<div class="modal fade" id="showSaleModal" tabindex="-1" role="dialog" aria-labelledby="showSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">

            <!-- Modal Header -->
            <div class="modal-header rounded">
                <h5 class="modal-title" id="showSaleModalLabel">
                    Sale Details <span id="showSaleID"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="showSaleTab" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="showSaleBasicInfoTab"
                            data-bs-toggle="tab"
                            data-bs-target="#showSaleBasicInfoPane"
                            type="button" role="tab">
                            Basic Info
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showSaleServiceTab"
                            data-bs-toggle="tab"
                            data-bs-target="#showSaleServicePane"
                            type="button" role="tab">
                            Service
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showSaleProductTab"
                            data-bs-toggle="tab"
                            data-bs-target="#showSaleProductPane"
                            type="button" role="tab">
                            Product
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showSalePaymentTab"
                            data-bs-toggle="tab"
                            data-bs-target="#showSalePaymentPane"
                            type="button" role="tab">
                            Payment
                        </button>
                    </li>

                </ul>

                <!-- Tab Contents -->
                <div class="tab-content" id="showSaleTabContent">

                    <!-- BASIC INFO TAB -->
                    <div class="tab-pane fade show active" id="showSaleBasicInfoPane" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <dl class="row">
                                    <dt class="col-sm-4">POS ID</dt>
                                    <dd class="col-sm-8" id="POSID"></dd>

                                    <dt class="col-sm-4">Invoice No</dt>
                                    <dd class="col-sm-8" id="invoice_code"></dd>

                                    <dt class="col-sm-4">Created By</dt>
                                    <dd class="col-sm-8" id="created_by"></dd>

                                    <dt class="col-sm-4">Updated By</dt>
                                    <dd class="col-sm-8" id="updated_by"></dd>

                                    <dt class="col-sm-4">Created At</dt>
                                    <dd class="col-sm-8" id="formattedCreatedDate"></dd>

                                    <dt class="col-sm-4">Updated At</dt>
                                    <dd class="col-sm-8" id="formattedUpdatedDate"></dd>
                                </dl>
                            </div>

                            <div class="col-lg-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Total Amount</dt>
                                    <dd class="col-sm-8" id="total_amount"></dd>

                                    <dt class="col-sm-4">Discount</dt>
                                    <dd class="col-sm-8" id="discount_amount"></dd>

                                    <dt class="col-sm-4">Adjustment</dt>
                                    <dd class="col-sm-8" id="adjustment_amount"></dd>

                                    <dt class="col-sm-4">Paid Amount</dt>
                                    <dd class="col-sm-8" id="total_payable_amount"></dd>

                                    <dt class="col-sm-4">Note</dt>
                                    <dd class="col-sm-8" id="sale_note"></dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- SERVICE TAB -->
                    <div class="tab-pane fade" id="showSaleServicePane" role="tabpanel">
                        <table class="table table-bordered mt-3" id="serviceItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:20%">Service Code</th>
                                    <th>Service Name</th>
                                    <th style="width:15%" class="text-center">Staff</th>
                                    <th style="width:10%" class="text-center">QTY</th>
                                    <th style="width:15%" class="text-end">Price</th>
                                    <th style="width:15%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Service items via JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- PRODUCT TAB -->
                    <div class="tab-pane fade" id="showSaleProductPane" role="tabpanel">
                        <table class="table table-bordered mt-3" id="productItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:20%">Product Code</th>
                                    <th>Product Name</th>
                                    <th style="width:15%" class="text-center">QTY</th>
                                    <th style="width:15%" class="text-end">Price</th>
                                    <th style="width:15%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Product items via JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- PAYMENT TAB -->
                    <div class="tab-pane fade" id="showSalePaymentPane" role="tabpanel">
                        <table class="table table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Method</th>
                                    <th class="text-center">Via</th>
                                    <th class="text-end">Paid</th>
                                    <th>Transaction ID</th>
                                    <th>Note</th>
                                    <th class="text-center">Received By</th>
                                    <th class="text-center">Paid At</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <!-- Payment rows via JS -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="printSalesBtn">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>

        </div>
    </div>
</div>
