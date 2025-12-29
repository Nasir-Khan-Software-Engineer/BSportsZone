(function(window, $){

    var MODAL_ID = 'subscriptionAlertModal';
    var STORAGE_KEY = 'subscription_modal_shown';

    function getTodayStr() {
        var now = new Date();
        return now.toISOString().slice(0,10); // 'YYYY-MM-DD'
    }

    function getDayOfMonth() {
        return new Date().getDate();
    }

    function getShowCount() {
        var data = localStorage.getItem(STORAGE_KEY);
        if(!data) return {date: '', count: 0};
        try {
            return JSON.parse(data);
        } catch(e) {
            return {date: '', count: 0};
        }
    }

    function setShowCount(date, count) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({date: date, count: count}));
    }

    function clearShowCount() {
        localStorage.removeItem(STORAGE_KEY);
    }

    function getMaxShowCount(day) {
        if(day === 1) return 3;
        if(day === 2) return 5;
        if(day === 3) return 7;
        if(day === 4) return 9;
        if(day === 5) return 11;
        if(day === 6) return 13;
        return Infinity;
    }

    function showSubscriptionModal(show) {
        if(show) {
            var today = getTodayStr();
            var info = getShowCount();
            var day = getDayOfMonth();
            var maxCount = getMaxShowCount(day);

            if(day >= 7) {
                // Always show, no limit
                $('#' + MODAL_ID).modal('show');
                return;
            }

            if(info.date !== today) {
                setShowCount(today, 1);
                $('#' + MODAL_ID).modal('show');
            } else if(info.count < maxCount) {
                setShowCount(today, info.count + 1);
                $('#' + MODAL_ID).modal('show');
            }
            // else: do not show, already shown max times today
        } else {
            $('#' + MODAL_ID).modal('hide');
            clearShowCount();
        }
    }

    window.showSubscriptionModal = showSubscriptionModal;

    $(document).on('click', '#' + MODAL_ID + ' [data-dismiss="modal"]', function() {
        $('#' + MODAL_ID).modal('hide');
    });
})(window, jQuery);
