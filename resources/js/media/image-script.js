WinPos.MediaImage = (function (Urls) {

    var datatableConfiguration = function () {
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
                    render: function (data, type, row) {
                        return row.id;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        if (row.imageUrl) {
                            return '<img src="' + row.imageUrl + '" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px;" alt="' + row.file_name + '">';
                        }
                        return '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return row.file_name;
                    }
                },
                {
                    data: null,
                    type: 'num',
                    orderable: true,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return row.formattedSize || '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return row.type ? row.type.toUpperCase() : '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return row.relation || '-';
                    }
                },
                {
                    data: null,
                    orderable: true,
                    searchable: false,
                    type: 'date',
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return WinPos.Common.dataTableCreatedOnCell(row.formattedTime, row.formattedDate);
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return row.createdBy || 'N/A';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        var showBtn = '<button class="btn btn-sm thm-btn-bg thm-btn-text-color show-image" data-imageid="' + row.id + '" title="Show"><i class="fa-solid fa-eye"></i></button>';
                        var copyBtn = '<button class="btn btn-sm btn-info copy-path" data-path="' + (row.fullPath || row.imageUrl || '') + '" title="Copy Path"><i class="fa-solid fa-copy"></i></button>';
                        var deleteBtn = '<button class="btn btn-sm btn-danger delete-image" data-imageid="' + row.id + '" title="Delete"><i class="fa-solid fa-trash"></i></button>';
                        return showBtn + ' ' + copyBtn + ' ' + deleteBtn;
                    }
                }
            ]
        };
    };

    var uploadImage = function (formData) {
        // Create FormData for file upload
        var uploadFormData = new FormData();
        
        if (formData.image instanceof File) {
            uploadFormData.append('image', formData.image);
        } else if (formData.image && formData.image.length > 0) {
            uploadFormData.append('image', formData.image[0]);
        }
        
        uploadFormData.append('relation', formData.relation);

        $.ajax({
            url: Urls.store,
            type: 'POST',
            data: uploadFormData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === 'success') {
                    WinPos.Datatable.refresh();
                    toastr.success(response.message);
                    $('#uploadImageModal').modal('hide');
                } else {
                    if (response.errors) {
                        WinPos.Common.showValidationErrors(response.errors);
                    } else {
                        toastr.error(response.message || 'Something went wrong');
                    }
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                if (response) {
                    if (response.errors) {
                        // Handle Laravel validation errors
                        WinPos.Common.showValidationErrors(response.errors);
                    } else if (response.message) {
                        toastr.error(response.message);
                    } else {
                        // Fallback for other error formats
                        var errorMsg = 'Validation failed. ';
                        if (response.error) {
                            errorMsg += response.error;
                        } else {
                            errorMsg += 'Please check your input.';
                        }
                        toastr.error(errorMsg);
                    }
                } else {
                    // Handle network or other errors
                    if (xhr.status === 422) {
                        toastr.error('Validation failed. Please check your input.');
                    } else {
                        toastr.error('Something went wrong, please try later.');
                    }
                }
            }
        });
    };

    var showImage = function (imageID) {
        WinPos.Common.getAjaxCall(Urls.show.replace('imageID', imageID), function (response) {
            if (response.status === 'success') {
                var image = response.image;
                debugger;
                $('#showImageId').text(image.id);
                $('#showImageName').text(image.file_name);
                $('#showImageSize').text(image.formattedSize);
                $('#showImageType').text(image.type ? image.type.toUpperCase() : '-');
                $('#showImageRelation').text(image.relation);
                $('#showImageCreatedAt').text(image.formattedDate + ' ' + image.formattedTime);
                $('#showImageCreatedBy').text(image.createdBy);
                
                if (image.imageUrl) {
                    $('#showImagePreview').css('background-image', 'url(' + image.imageUrl + ')').html('');
                } else {
                    $('#showImagePreview').html('<span class="text-muted">No image available</span>');
                }
                
                $('#showImageModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load image details');
            }
        });
    };

    var deleteImage = function (imageID) {
        WinPos.Common.deleteAjaxCallPost(Urls.destroy.replace('imageID', imageID), function (response) {
            if (response.status === 'success') {
                WinPos.Datatable.refresh();
                toastr.success(response.message);
            } else {
                toastr.error(response.message || 'Failed to delete image');
            }
        });
    };

    return {
        datatableConfiguration: datatableConfiguration,
        uploadImage: uploadImage,
        showImage: showImage,
        deleteImage: deleteImage
    };

})(ImageUrls);
