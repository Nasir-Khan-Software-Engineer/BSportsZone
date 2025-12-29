WinPos.ExpenseCategory = (function(Urls){
    /// public methods
    /*
    * get the create expense category form UI modal
    *
    * @param containerId {string} The div (DOM) id which will contain the modal
    * @param callback {function} Callback function that will show the modal after loaded
    */
    var createExpenseCategoryForm = function (containerId, callback){

        WinPos.Common.getAjaxCall(Urls.createExpenseCategory, function (response){
            if(typeof response === "string"){
                $(containerId).html("");
                $(containerId).html(response);
            }else if(typeof response === "object" && typeof response.errors !== "undefined"){  
                WinPos.Common.showValidationErrors(response.errors);  
            }
            callback(); 
        });
    };

    /*
    * Delete expense category and update the datatable
    * 
    * @param categoryId {int} id of the expense category
    */

    var deleteCategory = function (categoryId){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteExpenseCategory.replace(':id', categoryId), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors); 
            }
        });
    }


    /*
    * get the update expense category form UI modal
    *
    * @param containerId {string} The div (DOM) id which will contain the modal
    * @param categoryId {int} id of the expense category
    * @param callback {function} Callback function that will show the modal after loaded
    */
    var updateExpenseCategoryForm = function (containerId, categoryId, callback){
        WinPos.Common.getAjaxCall(Urls.editExpenseCategory.replace("categoryid", categoryId), function (response){
            if(typeof response === "string"){
                $(containerId).html("");
                $(containerId).html(response);
            }else if(typeof response === "object" && typeof response.errors !== "undefined"){  
                WinPos.Common.showValidationErrors(response.errors);  
            }
            callback();
        });
    }

    /*
    * Validate the create expense category form data
    * 
    * @param formData {object} contain the form data as key value pair (input field name => input field value)
    * @param type {string} action type save or update
    */
    var validateExpenseCategory = function (formData, type){
        // var title = formData.expenseCategoryTitle, 
        // expenseCategoryRegex = new RegExp("^[a-zA-Z ]*$");

        // if(!expenseCategoryRegex.test(title) || title.length < 1 || title.length > 200){
        //     toastr.error("Expense category title is not valid.");
        //     $('#createExpenseCategoryForm #expenseCategoryTitle').addClass('is-invalid');
        //     return false;
        // }

        if(type === 'create'){
            save(formData);
        }else {
            update(formData, formData.expenseCategoryId);
        }
    }
        

    /// private methods

    /*
    * Apply css to the newly added row such as text align, color, font style
    * 
    * @param row {object} DOM object. newly added row to the datatable
    * 
    */
    var applyCssToNewlyAddedRow = function(row){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);
            col.addClass('text-center');
            col.addClass('align-middle');
        });
    }
    
    /*
    * Prepare an array using the expense category object to insert into the datatable
    * The array is ordered as the Expense Category table's column order
    * 
    * @param data {object} contain the expense category
    * @return {array} Ordered and Formatted as Expense Category table's column
    * 
    */

    var prepareExpenseCategoryRow = function (data){

        return [
            data.id, 
            data.title, 
            WinPos.Common.dataTableCreatedOnCell(data.formattedTime, data.formattedDate),
            data.createdBy,
            WinPos.Common.dataTableActionCell(data.id, 'expense-category','',['edit', 'delete'])
        ];
    }
    
    /*
    * Call back-end to save the expense category 
    * On success response, insert the newly added expense category to the datatable
    * 
    * @param formData {object} contain the form data as key value pair (input field name => input field value)
    * 
    */
    var save = function (formData){
        WinPos.Common.postAjaxCall(Urls.saveExpenseCategory, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareExpenseCategoryRow(response.expenseCategory), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
            }else{
                if (typeof response.errors != 'undefined') {
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    toastr.error(response.message);
                }
            }
        });
    }

    /*
    * Call back-end to update the expense category 
    * On success response, update the datatable
    * 
    * @param formData {object} contain the form data as key value pair (input field name => input field value)
    * @param categoryId {int} id of the expense category that will be updated
    */
    var update = function (formData, categoryId){
        WinPos.Common.postAjaxCall(Urls.updateExpenseCategory.replace("categoryid", categoryId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareExpenseCategoryRow(response.expenseCategory), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
            }else{
                
                if(typeof response.errors != 'undefined'){
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    toastr.error(response.message);
                }              
            }
        });
    }
    
    return{
        deleteExpenseCategory: deleteCategory,
        getCreateExpenseCategoryForm: createExpenseCategoryForm,
        getUpdateExpenseCategoryForm: updateExpenseCategoryForm,
        saveExpenseCategory: validateExpenseCategory,
    };
})(ExpenseCategoryUrls);