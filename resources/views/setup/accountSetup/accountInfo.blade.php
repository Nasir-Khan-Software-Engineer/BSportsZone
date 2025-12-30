<div class="row mt-2">
    <div class="col-md-6">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>POS Information</h3>
                    </div>
                    <div class="card-body">
                        <form id="POSInfoForm">
                            <div class="form-row">
                                <div class="form-group col-12 col-lg-6">
                                    <label for="serviceCodePrefix">Service Code Prefix<span class="text-danger required-star">*</span> <small>(Min: 3 & Max: 5
                                            characters)</small></label>
                                    <input required maxlength="5" minlength="3" value="{{ $accountInfo->productCodePrefix ?? '' }}" type="text" class="form-control rounded" name="serviceCodePrefix"
                                        id="serviceCodePrefix" placeholder="Service Code Prefix">
                                </div>

                                <div class="form-group col-12 col-lg-6">
                                    <label for="invoiceNumberPrefix">Invoice Number Prefix<span class="text-danger required-star">*</span> <small>(Min: 3 & Max: 5
                                            characters)</small></label>
                                    <input value="{{ $accountInfo->invoiceNumberPrefix ?? '' }}" required maxlength="5" minlength="3" type="text" class="form-control rounded"
                                        name="invoiceNumberPrefix" id="invoiceNumberPrefix" placeholder="Invoice Number Prefix">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="adjustment_min">Minimum Adjustment<span class="text-danger required-star">*</span></label>
                                    <input type="number" step="0.01" class="form-control rounded" id="adjustment_min" name="adjustment_min" value="{{ $accountInfo->posSettings->adjustment_min }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="adjustment_max">Maximum Adjustment<span class="text-danger required-star">*</span></label>
                                    <input type="number" step="0.01" class="form-control rounded" id="adjustment_max" name="adjustment_max" value="{{ $accountInfo->posSettings->adjustment_max }}">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if(isFeatureEnabled('ENABLED_LOYALTY'))
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Loyalty Settings</h3>
                        <a class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" href="{{ route('help.setup.loyalty') }}" target="_blank">
                            <i class="fa-solid fa-circle-question"></i> Help
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="LoyaltySettingsForm">
                            <div class="form-row">
                                <div class="form-group col-12 col-lg-6">
                                    <label for="minimum_purchase_amount">Minimum Purchase Amount (৳)<span class="text-danger required-star">*</span> <i class="fas fa-info-circle" data-toggle="tooltip"
                                            data-placement="top" title="The minimum amount a customer must spend to qualify for loyalty benefits"></i></label>
                                    <input required type="number" step="0.01" class="form-control rounded" name="minimum_purchase_amount" id="minimum_purchase_amount"
                                        value="{{ $accountInfo->loyaltySettings->minimum_purchase_amount ?? '' }}" placeholder="Minimum Purchase Amount">
                                </div>

                                <div class="form-group col-12 col-lg-6">
                                    <label for="minimum_purchase_amount_applies_for">Applys For <span class="text-danger required-star">*</span> <i class="fas fa-info-circle" data-toggle="tooltip"
                                            data-placement="top" title="The minimum amount a customer must spend to qualify for loyalty benefits applies for"></i></label>

                                    <select required class="form-control rounded" name="minimum_purchase_amount_applies_for" id="minimum_purchase_amount_applies_for">
                                        <option value="Single" {{ $accountInfo->loyaltySettings?->minimum_purchase_amount_applies_for === 'Single' ? 'selected' : '' }}>
                                            Single Transaction
                                        </option>
                                        <option value="All"
                                            {{ $accountInfo->loyaltySettings?->minimum_purchase_amount_applies_for === 'All' || $accountInfo->loyaltySettings?->minimum_purchase_amount_applies_for === null ? 'selected' : '' }}>
                                            All Transactions
                                        </option>
                                    </select>

                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12 col-lg-4">
                                    <label for="validity_period_months">Validity Period (Months)<span class="text-danger required-star">*</span> <i class="fas fa-info-circle" data-toggle="tooltip"
                                            data-placement="top" title="How long the loyalty benefits remain valid after earning them"></i></label>
                                    <input required type="number" class="form-control rounded" name="validity_period_months" id="validity_period_months"
                                        value="{{ $accountInfo->loyaltySettings->validity_period_months ?? '' }}" placeholder="Validity in Months">
                                </div>
                                <div class="form-group col-12 col-lg-4">
                                    <label for="max_visits">Maximum Visits Allowed<span class="text-danger required-star">*</span> <i class="fas fa-info-circle" data-toggle="tooltip"
                                            data-placement="top" title="Maximum number of visits a customer can make using loyalty benefits"></i></label>
                                    <input required type="number" class="form-control rounded" name="max_visits" id="max_visits" value="{{ $accountInfo->loyaltySettings->max_visits ?? '' }}"
                                        placeholder="Maximum Visits">
                                </div>
                                <!-- // max visits per day -->
                                <div class="form-group col-12 col-lg-4">
                                    <label for="max_visits_per_day">Maximum Visits Allowed Per Day<span class="text-danger required-star">*</span> <i class="fas fa-info-circle" data-toggle="tooltip"
                                            data-placement="top" title="Maximum number of visits a customer can make using loyalty benefits per day"></i></label></label>
                                    <input required type="number" class="form-control rounded" name="max_visits_per_day" id="max_visits_per_day"
                                        value="{{ $accountInfo->loyaltySettings->max_visits_per_day ?? 1 }}" placeholder="Maximum Visits">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="rules_text">Loyalty Rules / Description <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top"
                                        title="Detailed rules and conditions for the loyalty program that customers should know"></i></label>
                                <textarea class="form-control rounded" name="rules_text" id="rules_text" rows="4"
                                    placeholder="Write loyalty rules here...">{{ $accountInfo->loyaltySettings->rules_text ?? '' }}</textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">
                                    <i class="fa-solid fa-floppy-disk"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- SMS Configuration Section -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="mb-0">SMS Configuration</h3>
                    </div>
                    <div class="card-body">
                        <form id="SmsConfigForm">
                            <div class="form-group">
                                <label for="sms_base_url">Base URL <span class="text-danger required-star">*</span></label>
                                <input type="url" class="form-control rounded" name="base_url" id="sms_base_url" value="{{ $smsConfig->base_url ?? '' }}"
                                    placeholder="https://api.mimsms.com/api/SmsSending" required>
                                <small class="form-text text-muted">SMS API base URL</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="sms_username">Username <span class="text-danger required-star">*</span></label>
                                    <input type="text" class="form-control rounded" name="username" id="sms_username" value="{{ $smsConfig->username ?? '' }}"
                                        placeholder="SMS API Username" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="sms_api_key">API Key <span class="text-danger required-star">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control rounded" name="api_key" id="sms_api_key" value="{{ $smsConfig->api_key ?? '' }}"
                                            placeholder="SMS API Key" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                                <i class="fa-solid fa-eye" id="apiKeyIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="sms_sender_id">Sender ID <span class="text-danger required-star">*</span></label>
                                    <input type="text" class="form-control rounded" name="sender_id" id="sms_sender_id" value="{{ $smsConfig->sender_id ?? '' }}"
                                        placeholder="Sender Name/ID" maxlength="50" required>
                                    <small class="form-text text-muted">This will appear as the sender name</small>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="sms_campaign_id">Campaign ID</label>
                                    <input type="text" class="form-control rounded" name="campaign_id" id="sms_campaign_id" value="{{ $smsConfig->campaign_id ?? '' }}"
                                        placeholder="Campaign ID (optional)" maxlength="50">
                                    <small class="form-text text-muted">Leave empty or set to 'null' if not required</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="sms_is_active" value="1" {{ ($smsConfig->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_is_active">
                                        Active (Enable SMS sending)
                                    </label>
                                </div>
                                <small class="form-text text-muted">Uncheck to disable SMS sending temporarily</small>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">
                                    <i class="fa-solid fa-floppy-disk"></i> Update Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Company Information</h3>
                    </div>
                    <div class="card-body">
                        <form id="accountInfoForm">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="companyName">Company Name<span class="text-danger required-star">*</span></label>
                                            <input minlength="3" maxlength="200" required value="{{$accountInfo->companyName}}" required type="text" class="form-control rounded" name="companyName"
                                                id="companyName" placeholder="Company Name">
                                        </div>

                                        <div class="form-group col-12">
                                            <label for="primaryPhone">Primary Phone<span class="text-danger required-star">*</span></label>
                                            <input minlength="11" maxlength="11" required value="{{$accountInfo->primaryPhone}}" type="text" class="form-control rounded" name="primaryPhone"
                                                id="primaryPhone" placeholder="Primary Phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="logo">Logo (50x50)<span class="text-danger required-star">*</span></label>
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' id="logo" name="logo" accept=".png, .jpg, .jpeg" />
                                            <label for="logo"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            @php
                                            $posid = auth()->user()->posid;
                                            $logoPath = "/images/{$posid}/" . $accountInfo->logo; // relative to public/
                                            @endphp

                                            <div class="rounded" id="logoPreview" style="background-image: url('{{ asset($logoPath) }}');">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="primaryEmail">Primary Email<span class="text-danger required-star">*</span></label>
                                    <input value="{{$accountInfo->primaryEmail}}" required type="Email" class="form-control rounded" name="primaryEmail" id="primaryEmail" placeholder="Primary Email">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="secoundaryEmail">Secondary Email</label>
                                    <input value="{{$accountInfo->secoundaryEmail}}" type="Email" class="form-control rounded" name="secoundaryEmail" id="secoundaryEmail"
                                        placeholder="Secondary Email">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="secondaryPhone">Secondary Phone</label>
                                    <input minlength="11" maxlength="11" value="{{$accountInfo->secondaryPhone}}" type="text" class="form-control rounded" name="secondaryPhone" id="secondaryPhone"
                                        placeholder="Secondary Phone">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="address">Address<span class="text-danger required-star">*</span></label>
                                    <textarea required maxlength="500" minlength="3" class="form-control rounded" name="address" id="address" rows="2"
                                        placeholder="Address">{{$accountInfo->address}}</textarea>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- SMS Template Section -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">SMS Template</h3>
                    </div>
                    <div class="card-body">
                        <form id="SmsTemplateForm">
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info"></i>
                                <strong>Note:</strong> The text inside <code>[## ... ##]</code> is system-generated and will be automatically replaced with invoice details.
                                Please do not remove or alter this placeholder.
                            </div>

                            <div class="form-group">
                                <label for="sms_template">SMS Template <span class="text-danger required-star">*</span></label>
                                <textarea class="form-control rounded" name="template" id="sms_template" rows="6" placeholder="Enter your SMS template here..." maxlength="500"
                                    required>{{ $smsTemplate ?? '' }}</textarea>
                                <small class="form-text text-muted">
                                    <span id="template_char_count">0</span> / 500 characters
                                    <span class="text-danger ml-2" id="template_length_warning" style="display: none;">
                                        <i class="fa-solid fa-exclamation-triangle"></i> Template may exceed SMS limit (160 chars)
                                    </span>
                                </small>
                                <div class="invalid-feedback" id="template_error"></div>
                            </div>

                            <div class="form-group">
                                <div class="alert alert-warning" id="placeholder_warning" style="display: none;">
                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> The system-generated placeholder <code>[## ... ##]</code> is missing or has been altered.
                                    This is required for the SMS to work correctly.
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="saveSmsTemplateBtn">
                                    <i class="fa-solid fa-floppy-disk"></i> Update Template
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>