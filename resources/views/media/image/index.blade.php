@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Image List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchImage" placeholder="Search Image">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button type="button" id="storeNewImageBtn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-toggle="modal"><i class="fa-solid fa-plus"></i> Store New</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="imageTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 8%;" scope="col">Image</th>
                        <th class="text-center align-middle" style="width: 18%;" scope="col">Name</th>
                        <th class="text-center align-middle" style="width: 8%;" scope="col">Size</th>
                        <th class="text-center align-middle" style="width: 8%;" scope="col">Type</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Relation</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Created At</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Created By</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upload Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">Upload New Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#uploadImageModal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadImageForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="imageFile">Select Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="imageFile" name="image" accept="image/gif,image/jpeg,image/jpg,image/png" required>
                        <small class="form-text text-muted">Accepted formats: gif, jpg, jpeg, png. Max size: 1MB</small>
                    </div>
                    <div class="form-group">
                        <label for="imageRelation">Relation <span class="text-danger">*</span></label>
                        <select class="form-control" id="imageRelation" name="relation" required>
                            <option value="">Select Relation</option>
                            <option value="Product">Product</option>
                            <option value="Banner">Banner</option>
                            <option value="Review">Review</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Preview</label>
                        <div id="imagePreview" style="width: 100%; height: 300px; border: 2px dashed #ddd; background-size: contain; background-position: center; background-repeat: no-repeat; background-color: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted">Image preview will appear here</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#uploadImageModal').modal('hide');">Close</button>
                    <button type="submit" class="btn thm-btn-bg thm-btn-text-color">Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Image Modal -->
<div class="modal fade" id="showImageModal" tabindex="-1" role="dialog" aria-labelledby="showImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showImageModalLabel">Image Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#showImageModal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="showImagePreview" style="width: 100%; height: 300px; border: 2px solid #ddd; background-size: contain; background-position: center; background-repeat: no-repeat; background-color: #f9f9f9;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID:</th>
                                <td id="showImageId">-</td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td id="showImageName">-</td>
                            </tr>
                            <tr>
                                <th>Size:</th>
                                <td id="showImageSize">-</td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td id="showImageType">-</td>
                            </tr>
                            <tr>
                                <th>Relation:</th>
                                <td id="showImageRelation">-</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td id="showImageCreatedAt">-</td>
                            </tr>
                            <tr>
                                <th>Created By:</th>
                                <td id="showImageCreatedBy">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#showImageModal').modal('hide');">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('url-scripts')
<script>
var ImageUrls = {
    'datatable': "{{ route('media.image.datatable') }}",
    'store': "{{ route('media.image.store') }}",
    'show': "{{ route('media.image.show', ['image' => 'imageID']) }}",
    'destroy': "{{ route('media.image.destroy', ['image' => 'imageID']) }}"
};
</script>
@endpush

@push('vite-scripts')
@vite(['resources/js/media/image-script.js'])
@endpush

@section('script')
<script>
$(document).ready(function() {
    WinPos.Datatable.initDataTable("#imageTable", WinPos.MediaImage.datatableConfiguration());

    $("#searchImage").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });

    // Open upload modal
    $("#storeNewImageBtn").on('click', function() {
        $("#uploadImageForm")[0].reset();
        $('#imagePreview').css('background-image', '').html('<span class="text-muted">Image preview will appear here</span>');
        $("#uploadImageModal").modal('show');
    });

    // Close upload modal handlers
    $("#uploadImageModal").on('hidden.bs.modal', function() {
        $("#uploadImageForm")[0].reset();
        $('#imagePreview').css('background-image', '').html('<span class="text-muted">Image preview will appear here</span>');
    });

    // Close show image modal handlers
    $("#showImageModal").on('hidden.bs.modal', function() {
        $('#showImagePreview').css('background-image', '').html('');
    });

    // Preview image on file select
    $("#imageFile").on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')').html('');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Handle form submission
    $("#uploadImageForm").on('submit', function(event) {
        event.preventDefault();
        WinPos.MediaImage.uploadImage(WinPos.Common.getFormData('#uploadImageForm'));
    });

    // Show image details
    $(document).on('click', '.show-image', function() {
        WinPos.Datatable.selectRow(this);
        let imageID = $(this).data('imageid');
        WinPos.MediaImage.showImage(imageID);
    });

    // Copy image path to clipboard
    $(document).on('click', '.copy-path', function() {
        var path = $(this).data('path');
        if (path) {
            // Create a temporary textarea element
            var tempTextarea = $('<textarea>');
            $('body').append(tempTextarea);
            tempTextarea.val(path).select();
            try {
                document.execCommand('copy');
                toastr.success('Image path copied to clipboard!');
            } catch (err) {
                // Fallback for browsers that don't support execCommand
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(path).then(function() {
                        toastr.success('Image path copied to clipboard!');
                    }).catch(function() {
                        toastr.error('Failed to copy path to clipboard');
                    });
                } else {
                    toastr.error('Clipboard API not available');
                }
            }
            tempTextarea.remove();
        } else {
            toastr.error('No path available');
        }
    });

    // Delete image
    $(document).on('click', '.delete-image', function() {
        WinPos.Datatable.selectRow(this);
        if (confirm("Are you sure you want to delete this image?\nClick OK to continue or Cancel.")) {
            let imageID = $(this).data('imageid');
            WinPos.MediaImage.deleteImage(imageID);
        }
    });
});
</script>
@endsection
