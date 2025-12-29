WinPos.Profile = (function (Urls){
    var updateInfo = function (formData){
        WinPos.Common.postAjaxCall(Urls.updateInfo, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                window.location.reload();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updatePassword = function (formData){
        WinPos.Common.postAjaxCall(Urls.updatePassword, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // we need to reload the page here
                window.location.reload();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    return {
        updateInfo: updateInfo,
        updatePassword: updatePassword
    }
})(profileUrls);
