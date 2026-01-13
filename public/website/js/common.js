Website.Common = (function(data){

    var updateCopyRightYear = function () {
        $('#copyright-year').text(data.currentYear);
    };

    var showToastMessage = function (type, message) {
        let toast = `
            <div class="custom-toast custom-toast-${type}">
                <span>${message}</span>
            </div>`;

        $('#toast-container').append(toast);
        
        setTimeout(() => {
            $('#toast-container .custom-toast').first().remove();
        }, 3500);
    }

    return {
        updateCopyRightYear: updateCopyRightYear,
        showToastMessage: showToastMessage
    };
})(websiteData);