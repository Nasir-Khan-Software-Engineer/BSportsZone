<!-- service Add, Edit Modal -->
<div class="modal fade" id="showSaleModal" tabindex="-1" role="dialog" aria-labelledby="showSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="showSaleModalLabel">Details<span id="showSaleID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="showSaleTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="showSaleBasicInfoTab" data-bs-toggle="tab" data-bs-target="#showSaleBasicInfoPane" type="button" role="tab" aria-controls="showSaleBasicInfoPane" aria-selected="true">Basic
                            Info</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showSaleServiceTab" data-bs-toggle="tab" data-bs-target="#showSaleServicePane" type="button" role="tab" aria-controls="showSaleServicePane" aria-selected="false">Service</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showSalePaymentTab" data-bs-toggle="tab" data-bs-target="#showSalePaymentPane" type="button" role="tab" aria-controls="showSalePaymentPane" aria-selected="false">Payment</button>
                    </li>
                </ul>

                <div class="tab-content" id="showSaleTabContent">
                    <div class="tab-pane fade show active" id="showSaleBasicInfoPane" role="tabpanel" aria-labelledby="showSaleBasicInfoTab" tabindex="0">
                        <div class="row mt-2">
                            <div class="col-12 col-lg-6">
                                <div class="row">
                                    <dt class="col-sm-4">POS ID</dt>
                                    <dd class="col-sm-8" id="POSID"></dd>

                                    <dt class="col-sm-4">Invoice No.</dt>
                                    <dd class="col-sm-8" id="invoice_code"></dd>

                                    <dt class="col-sm-4">Created By</dt>
                                    <dd class="col-sm-8" id="created_by"></dd>

                                    <dt class="col-sm-4">Updated By</dt>
                                    <dd class="col-sm-8" id="updated_by"></dd>

                                    <dt class="col-sm-4">Created Date</dt>
                                    <dd class="col-sm-8" id="formattedCreatedDate"></dd>

                                    <dt class="col-sm-4">Updated Date</dt>
                                    <dd class="col-sm-8" id="formattedUpdatedDate"></dd>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <dl class="row ">
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

                    <div class="tab-pane fade hide" id="showSaleServicePane" role="tabpanel" aria-labelledby="showSaleServiceTab" tabindex="1">
                        <table class="table table-bordered mt-2" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20%;">Service Code</th>
                                    <th>Service Name</th>
                                    <th style="width: 15%;" class="text-center">Staff</th>
                                    <th style="width: 15%;" class="text-center">QTY</th>
                                    <th style="width: 15%;" class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be appended here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade hide" id="showSalePaymentPane" role="tabpanel" aria-labelledby="showSalePaymentTab" tabindex="1">
                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th style="width:10%;">P. Method</th>
                                    <th style="width:10%;" class="text-center">P. Via</th>
                                    <th style="width:10%;" class="text-right">Paid Amt.</th>
                                    <th style="width:15%;" class="text-right">Transaction ID</th>
                                    <th style="width:20%;">Note</th>
                                    <th style="width:15%;" class="text-center">Received By</th>
                                    <th style="width:20%;" class="text-center">Payment At</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <!-- Rows will be appended here via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="printSalesBtn" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</div>