WinPos.Service = (function (Urls){

    var editService = function (serviceID){
        WinPos.Common.getAjaxCall(Urls.editService.replace('serviceID', serviceID), function (response){
            if(response.status === 'success'){
                var service = response.service;
                var categories = response.categories;
                var hasSales = response.hasSales || false;

                $("#editServiceID").html(' | Service ID: '+service.id);
                $("#hiddenServiceID").val(service.id); 
                $("#editCode").val(service.code);
                $("#editName").val(service.name);
               
                // Reset price field state first
                $("#editPrice").removeAttr('title');
                $("#editPrice").removeAttr('data-toggle');
                $("#editPrice").removeAttr('data-placement');
                $("#editPrice").removeAttr('data-bs-original-title');
                $("#editPrice").removeAttr('area-label');
                $("#editPrice").removeClass('bg-light');
                // Remove any existing warning text
                $("#editPrice").closest('.form-group').find('label .text-warning').remove();
                
                $("#editPrice").val(service.price);
                
                // Set beautician
                if(service.beautician_id){
                    markSingleSelectBoxItem('editBeautician', service.beautician_id);
                }else{
                    $("#editBeautician").val('');
                }
                
                // Disable price field if service has sales
                if(hasSales){
                    $("#editPrice").addClass('bg-light');
                    $("#editPrice").attr('title', 'This service already has sales, so the price cannot be changed.');
                    $("#editPrice").attr('data-toggle', 'tooltip');
                    $("#editPrice").attr('data-placement', 'top');
                    // Add a helper text or warning
                    var priceLabel = $("#editPrice").closest('.form-group').find('label');
                    if(priceLabel.length && !priceLabel.find('.text-warning').length){
                        priceLabel.append(' <small class="text-warning"><i class="fa-solid fa-info-circle"></i> Price locked (service has sales)</small>');
                    }
                }
                
                $("#editDescription").val(service.description);
                if(service.image != null){
                    $("#serviceImagePreviewEdit").html( '<div id="imagePreviewEdit" style="background-image: url(' + Urls.serviceImagePath + '/' + service.image + ');"></div' );
                }else{
                    $("#serviceImagePreviewEdit").html( '<div id="imagePreviewEdit" style="background-image: url();"></div' );
                }

                markMultipleSelectBoxItem('editCategory',categories);
                
                // Initialize tooltips if Bootstrap tooltips are used
                if(typeof $().tooltip === 'function'){
                    $('[data-toggle="tooltip"]').tooltip();
                }
                
                $("#updateService").show();
                $("#editServiceBasicInfoTab").click();
                $("#serviceEditModal").modal('show');

            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var saveService = function (formData){
        WinPos.Common.postAjaxCall(Urls.saveService, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("serviceAddModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateService = function (formData, serviceID){
        WinPos.Common.putAjaxCallPost(Urls.updateService.replace("serviceID", serviceID), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("serviceEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteService = function (serviceID){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteService.replace('serviceID', serviceID), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var datatableConfig = function(){
        return {
            serverSide: true,
            processing: true,
            ajax: {
                url: Urls.datatable,
                type: 'GET',
                data: function (d) {
                    return {
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        search: d.search.value,
                        order: d.order
                    }
                }
            },
            order: [[0, 'desc']],
            columns: [
                {
                    data: null,
                    type: 'num',
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.id;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.code;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        var image = "";
                        if(row.image){
                            image = `<img width="100px" height="50px" src="`+Urls.serviceImagePath+`/`+row.image+`" alt="`+row.image+`">`;
                        }else{
                            image = `<img width="100px" height="50px" src="`+Urls.defaultServiceImagePath+`" alt="`+row.name+`">`;
                        }
                        return image;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.name;
                    }
                },
                {
                    data: null,
                    type: 'num',
                    orderable: true,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return "৳ " + Number(row.price).toFixed(2);
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.beautician ? row.beautician.name : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    type: 'date',
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return WinPos.Common.dataTableCreatedOnCell(row.formattedTime, row.formattedDate);
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.createdBy;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        let showBtn = `<a data-toggle="tooltip" data-placement="top" data-bs-original-title="Show" href="`+Urls.showService.replace('serviceID', row.id)+`" class="btn btn-sm thm-btn-bg thm-btn-text-color"><i class="fa-solid fa-eye"></i></a>`;
                        let editBtn = ` <button data-toggle="tooltip" data-placement="top" data-bs-original-title="Edit" data-serviceid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-service"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        let copyBtn = ` <button data-bs-original-title="Create from this service" data-toggle="tooltip" data-placement="top" data-serviceid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color copy-service" title="Create from this service"><i class="fa-solid fa-copy"></i></button>`;
                        let deleteBtn = ` <button data-toggle="tooltip" data-placement="top" data-bs-original-title="Delete" data-serviceid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color delete-service"><i class="fa-solid fa-trash"></i></button>`;
                        return showBtn + editBtn + copyBtn + deleteBtn;
                    }
                }
            ]
        }
    }

    var markSingleSelectBoxItem = function(selectBoxId, selectdId){
        let selectBox = document.getElementById(selectBoxId);
        if (selectBox) {
            for (let i = 0; i < selectBox.options.length; i++) {
                if (selectBox.options[i].value == selectdId) {
                    selectBox.selectedIndex = i;
                }
            }
        }
    }

    var markMultipleSelectBoxItem = function(selectBoxId, itemList) {
        let selectBox = document.getElementById(selectBoxId);
        if (selectBox) {
            for (let i = 0; i < selectBox.options.length; i++) {
                for (let j = 0; j < itemList.length; j++) {
                    if (selectBox.options[i].value == itemList[j].id) {
                        selectBox.options[i].selected = true;
                    }
                }
            }
        }
    }

    var copyService = function (serviceID){
        WinPos.Common.getAjaxCall(Urls.copyService.replace('serviceID', serviceID), function (response){
            if(response.status === 'success'){
                var service = response.service;
                var categories = response.categories;

                // Reset form
                $("#serviceAddForm")[0].reset();
                $('#imagePreview').css('background-image', '');

                // Auto-fill form fields
                $("#name").val(service.name);
                $("#price").val(service.price);
                $("#details").val(service.description || '');
                
                // Note: beautician is not copied when copying a service

                // Mark categories
                markMultipleSelectBoxItem('category_id', categories);

                // Open modal and go to basic info tab
                $("#serviceBasicInfoTab").click();
                WinPos.Common.showBootstrapModal("serviceAddModal");
            }else{
                toastr.error(response.message || 'Failed to load service details.');
            }
        });
    }
    

    return {
        saveService: saveService,
        updateService: updateService,
        deleteService: deleteService,
        editService: editService,
        copyService: copyService,
        datatableConfiguration: datatableConfig
    }
})(serviceUrls);
