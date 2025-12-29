<!-- Shop Show Modal -->
<div class="modal fade" id="showShopModal" tabindex="-1" role="dialog" aria-labelledby="showShopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="showShopModalLabel">Shop Details<span id="showShopID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="showShopTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="showShopBasicInfoTab" data-toggle="tab" data-target="#showShopBasicInfoPane" 
                            type="button" role="tab" aria-controls="showShopBasicInfoPane" aria-selected="true">Basic Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showShopAboutTab" data-toggle="tab" data-target="#showShopAboutPane" type="button" 
                            role="tab" aria-controls="showShopAboutPane" aria-selected="false">About</button>
                    </li>
                </ul>

                <div class="tab-content" id="showShopTabContent">
                    <div class="tab-pane fade show active" id="showShopBasicInfoPane" role="tabpanel" aria-labelledby="showShopBasicInfoTab">
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-row">
                                    <div class="col-md-6 mb-2">
                                        <div><b>Name</b></div>
                                        <div id="showName">This is the shop's name</div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div><b>Email</b></div>
                                        <div id="showEmail">This is the shop's email</div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div><b>Primary Phone</b></div>
                                        <div id="showPrimaryPhone">This is the shop's primary phone</div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div><b>Secondary Phone</b></div>
                                        <div id="showSecondaryPhone">This is the shop's secondary phone</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <div><b>Division</b></div>
                                        <div id="showDivision">This is the shop's division</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <div><b>District</b></div>
                                        <div id="showDistrict">This is the shop's district</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <div><b>Thana</b></div>
                                        <div id="showThana">This is the shop's thana</div>
                                    </div>

                                    <div class="col-12 mb-2">
                                        <div><b>Address</b></div>
                                        <div id="showAddress">This is the shop's address</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="showShopAboutPane" role="tabpanel" aria-labelledby="showShopAboutTab">
                        <div class="row mt-2">
                            <div class="col-12 mb-2">
                                <div><b>About</b></div>
                                <div id="showAbout">This is the shop's about information</div>
                            </div>
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
