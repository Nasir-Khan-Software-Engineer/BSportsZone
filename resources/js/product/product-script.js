WinPos.Product = (function (Urls){

    var editProduct = function (productID){
        WinPos.Common.getAjaxCall(Urls.editProduct.replace('productID', productID), function (response){
            if(response.status === 'success'){
                var product = response.product;
                var categories = response.categories;
                var hasSales = response.hasSales || false;

                $("#editProductID").html(' | Service ID: '+product.id);
                $("#hiddenProductID").val(product.id); 
                $("#editCode").val(product.code);
                $("#editName").val(product.name);
               
                // Reset price field state first
                $("#editPrice").removeAttr('title');
                $("#editPrice").removeAttr('data-toggle');
                $("#editPrice").removeAttr('data-placement');
                $("#editPrice").removeAttr('data-bs-original-title');
                $("#editPrice").removeAttr('area-label');
                $("#editPrice").removeClass('bg-light');
                // Remove any existing warning text
                $("#editPrice").closest('.form-group').find('label .text-warning').remove();
                
                $("#editPrice").val(product.price);
                
                // Set beautician
                if(product.beautician_id){
                    markSingleSelectBoxItem('editBeautician', product.beautician_id);
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
                
                $("#editDescription").val(product.description);
                if(product.image != null){
                    $("#productImagePreviewEdit").html( '<div id="imagePreviewEdit" style="background-image: url(' + Urls.productImagePath + '/' + product.image + ');"></div' );
                }else{
                    $("#productImagePreviewEdit").html( '<div id="imagePreviewEdit" style="background-image: url();"></div' );
                }

                markMultipleSelectBoxItem('editCategory',categories);
                
                // Initialize tooltips if Bootstrap tooltips are used
                if(typeof $().tooltip === 'function'){
                    $('[data-toggle="tooltip"]').tooltip();
                }
                
                $("#updateProduct").show();
                $("#editProductBasicInfoTab").click();
                $("#productEditModal").modal('show');

            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var saveProduct = function (formData){
        WinPos.Common.postAjaxCall(Urls.saveProduct, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("productAddModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateProduct = function (formData, productID){
        WinPos.Common.putAjaxCallPost(Urls.updateProduct.replace("productID", productID), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("productEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteProduct = function (productID){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteProduct.replace('productID', productID), function (response){
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
                            image = `<img width="100px" height="50px" src="`+Urls.productImagePath+`/`+row.image+`" alt="`+row.image+`">`;
                        }else{
                            image = `<img width="100px" height="50px" src="`+Urls.defaultProductImagePath+`" alt="`+row.name+`">`;
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
                        let showBtn = `<a data-toggle="tooltip" data-placement="top" data-bs-original-title="Show" href="`+Urls.showProduct.replace('productID', row.id)+`" class="btn btn-sm thm-btn-bg thm-btn-text-color"><i class="fa-solid fa-eye"></i></a>`;
                        let editBtn = ` <button data-toggle="tooltip" data-placement="top" data-bs-original-title="Edit" data-productid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-product"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        let copyBtn = ` <button data-bs-original-title="Create from this service" data-toggle="tooltip" data-placement="top" data-productid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color copy-product" title="Create from this service"><i class="fa-solid fa-copy"></i></button>`;
                        let deleteBtn = ` <button data-toggle="tooltip" data-placement="top" data-bs-original-title="Delete" data-productid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color delete-product"><i class="fa-solid fa-trash"></i></button>`;
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

    var copyProduct = function (productID){
        WinPos.Common.getAjaxCall(Urls.copyProduct.replace('productID', productID), function (response){
            if(response.status === 'success'){
                var product = response.product;
                var categories = response.categories;

                // Reset form
                $("#productAddForm")[0].reset();
                $('#imagePreview').css('background-image', '');

                // Auto-fill form fields
                $("#name").val(product.name);
                $("#price").val(product.price);
                $("#details").val(product.description || '');
                
                // Note: beautician is not copied when copying a product

                // Mark categories
                markMultipleSelectBoxItem('category_id', categories);

                // Open modal and go to basic info tab
                $("#productBasicInfoTab").click();
                WinPos.Common.showBootstrapModal("productAddModal");
            }else{
                toastr.error(response.message || 'Failed to load service details.');
            }
        });
    }
    

    return {
        saveProduct: saveProduct,
        updateProduct: updateProduct,
        deleteProduct: deleteProduct,
        editProduct: editProduct,
        copyProduct: copyProduct,
        datatableConfiguration: datatableConfig
    }
})(productUrls);
