WinPos.Report.customer = (function(Urls) {
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
                        to_date: $('#toDate').val(),
                        customer_type: $('#customerType').val()
                    }
                },
                dataSrc: function (json) {
                    return json.data || [];
                }
            },
            order: [[5, 'desc']], // Sort by total spending descending
            columns: [
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.customer_id || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.customer_name || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.phone || '-';
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_sales || 0;
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_quantity || 0;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_spending || '0';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_discount_amount || '0';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_adjustment_amount || '0';
                    }
                },
                {
                    data: null,
                    orderable: true,
                    type: 'date',
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.formatted_last_visited_date || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.customer_type || '-';
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(customerReportUrls);

