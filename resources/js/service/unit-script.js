WinPos.Unit = (function (Urls){
    var showUnit = function (unitID){
        WinPos.Common.getAjaxCall(Urls.showUnit.replace('unitID', unitID), function (response){
            if(response.status === 'success'){
                var unit = response.unit;
                $("#showName").html(unit.name);
                $("#showShortForm").html(unit.shortform);
                $("#showNote").html(unit.note);
                $("#showUnitID").html(' | Unit ID: '+unit.id);
                WinPos.Common.showBootstrapModal('unitModalShow');
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var editUnit = function (unitID){
        WinPos.Common.getAjaxCall(Urls.editUnit.replace('unitID', unitID), function (response){
            if(response.status === 'success'){
                var unit = response.unit;

                $("#name").val(unit.name);
                $("#shortform").val(unit.shortform);
                $("#note").val(unit.note);
                $("#hiddenUnitID").val(unit.id);
                $("#unitID").html(' | Unit ID: '+unit.id);

                $("#saveUnit").hide();
                $("#updateUnit").show();
                $("#unitAddEditModal").modal('show');

            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var saveUnit = function (formData){
        WinPos.Common.postAjaxCall(Urls.saveUnit, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareDatatableRow(response.unit), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("unitAddEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateUnit = function (formData, unitID){
        WinPos.Common.putAjaxCallPost(Urls.updateUnit.replace("unitID", unitID), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareDatatableRow(response.unit), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("unitAddEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteUnit = function (unitID){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteUnit.replace('unitID', unitID), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var isValidUnit = function(formData){
        
        if (!formData.name || formData.name.trim().length < 3 || formData.name.trim().length > 200) {
            toastr.error("Name is not valid!");
            return false;
        }
    
        if (formData.note && (formData.note.trim().length < 3 || formData.note.trim().length > 1000)) {
            toastr.error("Note is not valid!");
            return false;
        }
    
        return true;
    }

    var prepareDatatableRow = function (unit){
        let actionCell = [];

        actionCell.push(' <button data-unitID="');
        actionCell.push(unit.id);
        actionCell.push('" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-unit"><i class="fa-solid fa-pen-to-square"></i></button>');

        actionCell.push(' <button data-unitID="');
        actionCell.push(unit.id);
        actionCell.push('" class="btn btn-sm thm-btn-bg thm-btn-text-color delete-unit"><i class="fa-solid fa-trash"></i></button>');

        return [
            unit.id,
            unit.name,
            unit.shortform,
            WinPos.Common.dataTableCreatedOnCell(unit.formattedTime, unit.formattedDate),
            unit.createdBy,
            actionCell.join("")
        ];
    }

    var applyCssToNewlyAddedRow = function(row){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);
            col.addClass('text-center');
            col.addClass('align-middle');
        });
    }

    return {
        saveUnit: saveUnit,
        updateUnit: updateUnit,
        deleteUnit: deleteUnit,
        showUnit: showUnit,
        editUnit: editUnit,
        isValidUnit: isValidUnit
    }
})(unitUrls);
