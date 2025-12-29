WinPos.Report.netProfit = (function(Urls) {
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
                    // Update footer totals
                    $('#totalSalesRevenue').text(json.totals.totalSalesRevenue);
                    $('#totalExpenses').text(json.totals.totalExpenses);
                    $('#totalNetProfit').text(json.totals.totalNetProfit);
                    $('#totalProfitMargin').text(json.totals.totalProfitMargin);
                    return json.data || [];
                }
            },
            order: [[0, 'desc']], // Sort by date descending
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
                        return row.totalSalesRevenue;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.totalExpenses;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.netProfit;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.profitMargin;
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(netProfitReportUrls);
