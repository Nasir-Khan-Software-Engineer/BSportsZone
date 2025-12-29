<!-- Last Sales History Modal -->
<div class="modal fade" id="lastSalesHistoryModal" tabindex="-1" role="dialog" aria-labelledby="lastSalesHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="lastSalesHistoryModalLabel">Last Sales</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div id="lastSalesHistoryLoading" class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading...</p>
                </div>

                <!-- Content will be populated here -->
                <div id="lastSalesHistoryContent" class="d-none">
                    <!-- Sales Information -->
                    <div class="row mt-2 mb-4">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Date:</dt>
                                        <dd class="col-sm-7" id="lastSaleDate">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Invoice Number:</dt>
                                        <dd class="col-sm-7" id="lastSaleInvoice">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Total Amount:</dt>
                                        <dd class="col-sm-7" id="lastSaleTotal">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Discount Amount:</dt>
                                        <dd class="col-sm-7" id="lastSaleDiscount">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Total Paid:</dt>
                                        <dd class="col-sm-7" id="lastSalePaid">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Payment Method:</dt>
                                        <dd class="col-sm-7" id="lastSalePaymentMethod">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Adjustment:</dt>
                                        <dd class="col-sm-7" id="lastSaleAdjustment">-</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Sales By:</dt>
                                        <dd class="col-sm-7" id="lastSaleSalesBy">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Details Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 40%;">Service Name</th>
                                        <th style="width: 10%;" class="text-center">QTY</th>
                                        <th style="width: 20%;" class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="lastSaleItemsTableBody">
                                    <!-- Rows will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- No History Message -->
                    <div id="lastSalesHistoryEmpty" class="text-center py-4 d-none">
                        <i class="fa-solid fa-inbox fa-3x text-muted"></i>
                        <p class="mt-3 text-muted">No sales history found for this customer.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
