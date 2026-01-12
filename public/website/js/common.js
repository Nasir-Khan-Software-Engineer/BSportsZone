Website.Common = (function(data){

    var updateCopyRightYear = function () {
        $('#copyright-year').text(data.currentYear);
    };

    return {
        updateCopyRightYear: updateCopyRightYear
    };
})(websiteData);