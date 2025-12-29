@extends('layouts.main-layout')

@section('style')
@vite(['resources/css/setup/account-setup-style.css'])
@endsection

@section('content')

@include('setup.accountSetup.accountInfo')

@endsection

@section('script')
@vite(['resources/js/setup/account-setup-script.js'])

<script>
let AccountSetupUrls = {
    'updateAccountInfo': "{{ route('setup.account.update') }}",
    'updatePOSInfo': "{{ route('setup.posinfo.update') }}",
    'updateLoyaltySettings': "{{ route('setup.loyalty.update') }}",
    'updateSmsTemplate': "{{ route('setup.sms-template.update') }}",
    'updateSmsConfig': "{{ route('setup.sms-config.update') }}"
}

$(document).ready(function() {
    // Initialize tooltips for loyalty settings
    WinPos.AccountSetup.initializeTooltips();
    
    $('#accountInfoForm').submit(async function(event) {
        event.preventDefault();

        let accountInfo = WinPos.Common.getFormData('#accountInfoForm');

        // Convert file to Base64 before sending
        if (accountInfo.logo instanceof File) {
            accountInfo.logo = await fileToBase64(accountInfo.logo);
        }
        
        WinPos.AccountSetup.update(accountInfo);
    });


    $('#POSInfoForm').submit(function(event) {
        event.preventDefault();

        let POSInfo = WinPos.Common.getFormData('#POSInfoForm');
        WinPos.AccountSetup.updatePOSInfo(POSInfo);
    });

    $('#LoyaltySettingsForm').submit(function(event) {
        event.preventDefault();

        let loyaltyInfo = WinPos.Common.getFormData('#LoyaltySettingsForm');
        WinPos.AccountSetup.updateLoyaltySettings(loyaltyInfo);
    });

    $('#SmsConfigForm').submit(function(event) {
        event.preventDefault();
        let configData = WinPos.Common.getFormData('#SmsConfigForm');
        // Convert checkbox to boolean
        configData.is_active = $('#sms_is_active').is(':checked');
        WinPos.AccountSetup.updateSmsConfig(configData);
    });

    $('#SmsTemplateForm').submit(function(event) {
        event.preventDefault();
        let templateData = WinPos.Common.getFormData('#SmsTemplateForm');
        WinPos.AccountSetup.updateSmsTemplate(templateData);
    });

    // Toggle API key visibility
    $('#toggleApiKey').click(function() {
        var input = $('#sms_api_key');
        var icon = $('#apiKeyIcon');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Character counter and validation for SMS template
    $('#sms_template').on('input', function() {
        let template = $(this).val();
        let charCount = template.length;
        $('#template_char_count').text(charCount);

        // Check for placeholder
        let hasPlaceholder = /\[##\s*.*?\s*##\]/.test(template);
        if (!hasPlaceholder) {
            $('#placeholder_warning').show();
            $('#sms_template').addClass('is-invalid');
        } else {
            $('#placeholder_warning').hide();
            $('#sms_template').removeClass('is-invalid');
        }

        // Estimate final length (replace placeholder with 70 chars)
        let estimatedLength = template.replace(/\[##\s*.*?\s*##\]/, 'X'.repeat(70)).length;
        if (estimatedLength > 160) {
            $('#template_length_warning').show();
        } else {
            $('#template_length_warning').hide();
        }
    });

    // Initial validation on page load
    $('#sms_template').trigger('input');

    $("#logo").change(function() {
        WinPos.Common.previewImage('#logoPreview', this);
    });
});

function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}
</script>

@endsection