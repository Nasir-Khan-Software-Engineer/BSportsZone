WinPos.Role = (function (Urls){
    var showRole = function (roleID){
        WinPos.Common.getAjaxCall(Urls.showRole.replace('roleID', roleID), function (response){
            if(response.status === 'success'){
                var role = response.role;
                var accessRights = response.allAccessRights;

                $('#roleName').text(role.name);
                $('#roleDescription').text(role.description || '-');

                var $tbody = $('#roleAccessTableBody');
                $tbody.empty();

                $.each(accessRights, function(index, right) {
                    var $row = $('<tr></tr>');
                    $('<td></td>').text(right.title).appendTo($row);
                    $('<td></td>').text(right.description || '-').appendTo($row);
                    $tbody.append($row);
                });
                
                WinPos.Common.showBootstrapModal('roleShowModal');
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteRole = function (roleID){
        WinPos.Common.deleteAjaxCall(Urls.deleteRole.replace('roleID', roleID), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    return {
        deleteRole: deleteRole,
        showRole: showRole
    }
})(roleUrls);