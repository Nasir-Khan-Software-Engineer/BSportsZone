<div class="modal fade" id="createEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <form id="createEmployeeForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="createEmployeeModalLabel">Create New Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="employeeID" id="employeeID">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employeeName">Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded" id="employeeName" placeholder="Enter employee name" name="employeeName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded" id="phone" placeholder="Enter phone number" name="phone" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dateOfBirth">Date of Birth:<span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded" id="dateOfBirth" name="dateOfBirth" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender:<span class="text-danger">*</span></label>
                                <select class="form-control rounded" id="gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="designationId">Designation:<span class="text-danger">*</span></label>
                                <select class="form-control rounded" id="designationId" name="designationId" required>
                                    <option value="">Select Designation</option>
                                    @if(isset($designations))
                                        @foreach($designations as $designation)
                                            <option value="{{$designation->id}}">{{$designation->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jobTitle">Job Title:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded" id="jobTitle" placeholder="Enter job title" name="jobTitle" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hireDate">Hire Date:<span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded" id="hireDate" name="hireDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status:<span class="text-danger">*</span></label>
                                <select class="form-control rounded" id="status" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="note">Note:</label>
                                <textarea class="form-control rounded" id="note" name="note" rows="3" placeholder="Enter any additional notes about the employee"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-type="create" id="saveUpdateEmployee"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

