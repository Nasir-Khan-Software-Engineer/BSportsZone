WinPos.Report.revenue = (function(Urls) {
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
                    $('#totalQuantity').text(json.totals.totalQuantity || 0);
                    $('#totalRevenue').text(json.totals.totalRevenue);
                    
                    return json.data || [];
                }
            },
            order: [[4, 'desc']], // Sort by revenue descending
            columns: [
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.code || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.name || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.price || '0';
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.quantity_sold || 0;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.revenue || '0';
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(revenueReportUrls);

