WinPos.Report.employee = (function(Urls) {
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
                        designation_id: $('#designationId').val()
                    }
                },
                dataSrc: function (json) {
                    return json.data || [];
                }
            },
            order: [[1, 'asc']], // Sort by designation ascending
            columns: [
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.employee_name || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-left align-middle',
                    render: function(data, type, row){
                        return row.designation || '-';
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
                        return row.total_working_days || 0;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.present_display || '0% (0)';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.absent_display || '0% (0)';
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_leave || 0;
                    }
                },
                {
                    data: null,
                    type: 'number',
                    orderable: true,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.total_review || 0;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.positive_display || '0% (0)';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.warning_display || '0% (0)';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-right align-middle',
                    render: function(data, type, row){
                        return row.negative_display || '0% (0)';
                    }
                }
            ]
        }
    }

    return {        
        datatableConfiguration: datatableConfig
    };
})(employeeReportUrls);

