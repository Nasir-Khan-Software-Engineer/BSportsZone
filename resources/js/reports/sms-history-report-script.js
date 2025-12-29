WinPos.Report.smsHistory = (function(Urls) {
    var datatableConfig = function(){
        return {
            serverSide: true,
            ajax: {
                url: Urls.datatable,
                type: 'GET',
                data: function (d) {
                    return {
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        from_date: $('#fromDate').val(),
                        to_date: $('#toDate').val()
                    }
                },
                dataSrc: function (json) {
                    // Update totals in footer
                    if (json.totals) {
                        $('#totalSmsCount').text(json.totals.totalSmsCount || 0);
                        $('#totalMessageLength').text(json.totals.totalMessageLength || 0);
                        $('#totalCost').text(json.totals.totalCost || '0.00');
                    }
                    return json.data || [];
                }
            },
            order: [[0, 'desc']], // Sort by date time descending
            columns: [
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.date_time || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.source || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.from_number || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.to_number || '-';
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.message_length || 0;
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.sms_count || 0;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.unit_cost || '0.45';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_cost || '0.00';
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(smsHistoryReportUrls);

