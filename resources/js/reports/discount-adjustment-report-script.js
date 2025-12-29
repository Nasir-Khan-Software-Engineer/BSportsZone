WinPos.Report.discountAdjustment = (function(Urls) {
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
                    $('#totalDiscountAmount').text(json.totals.totalDiscountAmount);
                    $('#totalPositiveAdjustment').text(json.totals.totalPositiveAdjustment);
                    $('#totalNegativeAdjustment').text(json.totals.totalNegativeAdjustment);
                    $('#totalNetAdjustment').text(json.totals.totalNetAdjustment);
                    return json.data || [];
                }
            },
            order: [[0, 'desc']],
            columns: [
                {
                    data: null,
                    orderable: true,
                    type: 'date',
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.formattedDate;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.totalDiscountAmount;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.totalPositiveAdjustment;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.totalNegativeAdjustment;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.netAdjustmentImpact;
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(discountAdjustmentReportUrls);

