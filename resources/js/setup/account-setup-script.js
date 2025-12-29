WinPos.AccountSetup = (function(Urls){
    var validateAccountInfo = function(formData){
        // validation

        if (formData.companyName.length < 3 || formData.companyName.length > 200) {
            $("#companyName").focus();
            return toastr.error('Company name must be between 3 and 200 characters long');
        }

        if (!formData.primaryEmail || !WinPos.Common.isValidEmail(formData.primaryEmail) || formData.primaryEmail.length < 3 || formData.primaryEmail.length > 255) {
            $("#primaryEmail").focus();
            return toastr.error('Primary email is invalid or empty');
        }

        if (formData.secondaryEmail && (!WinPos.Common.isValidEmail(formData.secondaryEmail) || formData.secondaryEmail.length > 255)) {
            $("#secondaryEmail").focus();
            return toastr.error('Secondary email is invalid or exceeds the maximum length');
        }

        if (!formData.primaryPhone || !WinPos.Common.isValidPhoneNumber(formData.primaryPhone) || formData.primaryPhone.length !== 11) {
            $("#primaryPhone").focus();
            return toastr.error('Primary phone number is invalid or empty');
        }

        if (formData.secondaryPhone && (!WinPos.Common.isValidPhoneNumber(formData.secondaryPhone) || formData.secondaryPhone.length !== 11)) {
            $("#secondaryPhone").focus();
            return toastr.error('Secondary phone number is invalid');
        }
        
        if (!formData.address || formData.address.length < 3 || formData.address.length > 300) {
            $("#address").focus();
            return toastr.error('Address must be between 3 and 300 characters long');
        }

        updateAccountInfo(formData);
    }
    
    var updateAccountInfo = function(data){
        $.ajax({
            url: Urls.updateAccountInfo,
            method: 'POST',
            contentType: "application/json",
            data: JSON.stringify(data),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === 'success'){
                    toastr.success(response.message);
                    // we need to refresh the page here after 2 seconds
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
                }else{
                    WinPos.Common.showValidationErrors(response.errors);
                }                  
            },
            error: function() {
                toastr.error("Something went wrong, please try later.");
            }
        });
    }

    var updatePOSInfo = function(data){
        $.ajax({
            url: Urls.updatePOSInfo,
            method: 'POST',
            contentType: "application/json",
            data: JSON.stringify(data),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if(response.status === 'success'){
                    toastr.success(response.message);
                    // we need to refresh the page here after 2 seconds
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
                }else{
                    WinPos.Common.showValidationErrors(response.errors);
                }                  
            },
            error: function() {
                toastr.error("Something went wrong, please try later.");
            }
        });
    }

    var previewLogo = function(input){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview').css('background-image', 'url('+e.target.result +')');
                $('#logoPreview').hide();
                $('#logoPreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    var updateLoyaltySettings = function(data){
        $.ajax({
            url: Urls.updateLoyaltySettings,
            method: 'POST',
            contentType: "application/json",
            data: JSON.stringify(data),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if(response.status === 'success'){
                    toastr.success(response.message);
                    // we need to refresh the page here after 2 seconds
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                }else{
                    WinPos.Common.showValidationErrors(response.errors);
                }                  
            },
            error: function() {
                toastr.error("Something went wrong, please try later.");
            }
        });
    }

    var initializeTooltips = function(){
        // Initialize Bootstrap tooltips
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover',
            placement: 'top',
            html: true
        });
    }

    var updateSmsTemplate = function(data){
        // Validate placeholder exists
        var placeholderPattern = /\[##\s*.*?\s*##\]/;
        if (!placeholderPattern.test(data.template)) {
            $('#sms_template').addClass('is-invalid');
            $('#template_error').text('The SMS template must contain the system-generated placeholder [## ... ##]. Please do not remove or alter this placeholder.');
            return toastr.error('The SMS template must contain the system-generated placeholder [## ... ##]');
        }

        // Validate length
        var estimatedLength = data.template.replace(/\[##\s*.*?\s*##\]/, 'X'.repeat(80)).length;
        if (estimatedLength > 160) {
            return toastr.error('The SMS template is too long. Estimated final length: ' + estimatedLength + ' characters. Maximum allowed: 160 characters.');
        }

        WinPos.Common.postAjaxCall(Urls.updateSmsTemplate, JSON.stringify(data), function(response) {
            if(response.status === 'success'){
                toastr.success(response.message);
                $('#sms_template').removeClass('is-invalid');
                $('#template_error').text('');
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
                if (response.errors && response.errors.template) {
                    $('#sms_template').addClass('is-invalid');
                    $('#template_error').text(response.errors.template[0]);
                }
            }
        });
    }

    var updateSmsConfig = function(data){
        // Validate required fields
        if (!data.base_url || !data.base_url.trim()) {
            $('#sms_base_url').focus();
            return toastr.error('Base URL is required');
        }

        if (!data.username || !data.username.trim()) {
            $('#sms_username').focus();
            return toastr.error('Username is required');
        }

        if (!data.api_key || !data.api_key.trim()) {
            $('#sms_api_key').focus();
            return toastr.error('API Key is required');
        }

        if (!data.sender_id || !data.sender_id.trim()) {
            $('#sms_sender_id').focus();
            return toastr.error('Sender ID is required');
        }

        // Validate URL format
        try {
            new URL(data.base_url);
        } catch (e) {
            $('#sms_base_url').focus();
            return toastr.error('Base URL must be a valid URL');
        }

        WinPos.Common.postAjaxCall(Urls.updateSmsConfig, JSON.stringify(data), function(response) {
            if(response.status === 'success'){
                toastr.success(response.message);
                // Refresh page after 1 second to reload session config
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    return {
        update: updateAccountInfo,
        previewLogo: previewLogo,
        updatePOSInfo: updatePOSInfo,
        updateLoyaltySettings: updateLoyaltySettings,
        initializeTooltips: initializeTooltips,
        updateSmsTemplate: updateSmsTemplate,
        updateSmsConfig: updateSmsConfig
    }
})(AccountSetupUrls); 