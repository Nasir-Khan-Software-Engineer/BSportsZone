WinPos.Report.sales = (function(Urls) {
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
                    $('#totalAmount').text(json.totals.totalAmount);
                    $('#totalDiscount').text(json.totals.totalDiscountAmount);
                    $('#totalPayable').text(json.totals.totalPayable);
                    $('#totalDiscountAmount').text(json.totals.totalDiscountAmount);
                    $('#totalAdjustmentAmt').text(json.totals.totalAdjustmentAmt);
                    $('#totalPaid').text(json.totals.totalPaid);
                    return json.data || [];
                }
            },
            order: [[0, 'desc'], [2, 'desc']],
            columns: [
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.invoice_code;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.customer.name;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.customer.phone1;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    type: 'date',
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return WinPos.Common.dataTableCreatedOnCell(row.formattedTime, row.formattedDate);
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_amount;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        let discountTitle = row.disountTpye == 'fixed'? `Fixed`: `${row.discount_value??0}%`;
                        return `${row.discount_amount} (${discountTitle})`;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.adjustmentAmt;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_payable_amount;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.paidAmount;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.created_by_user ? row.created_by_user.name : '-';
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(salesReportUrls);