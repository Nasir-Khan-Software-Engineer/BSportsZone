WinPos.Report.expense = (function(Urls) {
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
                    return json.data || [];
                }
            },
            order: [[0, 'desc']],
            columns: [
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.id;
                    }
                },
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
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.title;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.expense_category ? row.expense_category.title : 'N/A';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.creator ? row.creator.name : '-';
                    }
                },
                {
                    data: null,
                    orderable: true,
                    type: 'date',
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return WinPos.Common.dataTableCreatedOnCell(row.formattedCreatedAtTime, row.formattedCreatedAt);
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.amount;
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(expenseReportUrls);
