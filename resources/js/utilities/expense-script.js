WinPos.Expense = (function(Urls){
    /// public methods
    /*
    * get the create expense form UI modal
    *
    * @param containerId {string} The div (DOM) id which will contain the modal
    * @param callback {function} Callback function that will show the modal after loaded
    */
    var createExpenseForm = function (containerId, callback){

        WinPos.Common.getAjaxCall(Urls.createExpense, function (response){
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
    * Delete expense and update the datatable
    *
    * @param categoryId {int} id of the expense
    */

    var deleteExpense = function (expenseId){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteExpense.replace('expenseid', expenseId), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
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


    /*
    * get the update expense form UI modal
    *
    * @param containerId {string} The div (DOM) id which will contain the modal
    * @param expenseId {int} id of the expense
    * @param callback {function} Callback function that will show the modal after loaded
    */
    var updateExpenseForm = function (containerId, expenseId, callback){
        WinPos.Common.getAjaxCall(Urls.editExpense.replace("expenseid", expenseId), function (response){
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
    * Validate the create expense form data
    *
    * @param formData {object} contain the form data as key value pair (input field name => input field value)
    * @param type {string} action type save or update
    */
    var validateExpense = function (formData, type){
        return new Promise(function(resolve, reject) {

            var arr = formData['expenseAmount'].split('.');

            if(arr.length <= 1){
                formData['expenseAmount'] = formData['expenseAmount'] + '.00';
            }

            WinPos.Validator.config = {
                expenseTitle: 'isAlphaNumericAndUnderscoreExpenseName',
                expensedOn: 'isLessThanNextDay',
                expenseAmount: 'isMoney'
            };

            WinPos.Validator.validate(formData);

            if(WinPos.Validator.hasErrors()){

                var msg = [];
                msg.push('<ul>');
                $.each(WinPos.Validator.message, function(index){
                   msg.push('<li>'+ WinPos.Validator.message[index]+ '</li>')
                });
                msg.push('</ul>');
                toastr.error(msg.join(''));
                return reject();
            }

            if(type === 'create'){
                save(formData).then(() => {
                    resolve();
                });
            }else {
                update(formData, formData.expenseId).then(() => {
                    resolve();
                });
            }
        });
    }

    /*
    * Call back-end to save the expense
    * On success response, insert the newly added expense to the datatable
    *
    * @param formData {object} contain the form data as key value pair (input field name => input field value)
    *
    */
    var save = function (formData){
        return new Promise((resolve, rejected) => {
            WinPos.Common.postAjaxCall(Urls.saveExpense, JSON.stringify(formData), function (response){
                if(response.status === 'success'){
                    WinPos.Datatable.refresh();
                    toastr.success(response.message);
                    resolve();
                }else{
                    if (typeof response.errors != 'undefined') {
                        WinPos.Common.showValidationErrors(response.errors);
                    }else{
                        toastr.error(response.message);
                    }

                    rejected();
                }
            });
        });
    }

    /*
    * Call back-end to update the expense
    * On success response, update the datatable
    *
    * @param formData {object} contain the form data as key value pair (input field name => input field value)
    * @param expenseId {int} id of the expense that will be updated
    */
    var update = function (formData, expenseId){
        return new Promise((resolve, reject) => {
            WinPos.Common.postAjaxCall(Urls.updateExpense.replace("expenseid", expenseId), JSON.stringify(formData), function (response){
                if(response.status === 'success'){
                    WinPos.Datatable.refresh();
                    toastr.success(response.message);
                    resolve();
                }else{

                    if(typeof response.errors != 'undefined'){
                        WinPos.Common.showValidationErrors(response.errors);
                    }else{
                        toastr.error(response.message);
                    }

                    reject();
                }
            });
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
                        return row.title;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.expense_category.title;
                    }
                },
                {
                    data: null,
                    type: 'num',
                    orderable: true,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.amount || 0;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    type: 'date',
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.formattedExpenseDate;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    searchable: false,
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
                        let editBtn = ` <button class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" data-placement="top" title="Expense created before today, can not be edited." ><i class="fa-solid fa-pen-to-square"></i></button>`;
                        let deleteBtn = ` <button class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" data-placement="top"  title="Expense created before today, can not be deleted." ><i class="fa-solid fa-trash"></i></button>`;

                        if(row.deletable){
                            deleteBtn = ` <button data-expenseid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color delete-expense"><i class="fa-solid fa-trash"></i></button>`;
                        }

                        if(row.editable){
                            editBtn = `<button data-expenseid="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-expense"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        }

                        return editBtn + deleteBtn;
                    }
                }
            ],
            drawCallback: function () {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }
    }

    return{
        deleteExpense: deleteExpense,
        getCreateExpenseForm: createExpenseForm,
        getUpdateExpenseForm: updateExpenseForm,
        saveExpense: validateExpense,
        datatableConfig: datatableConfig,
        disableEditUpdateButtons: function(){
            var deleteButtons = $('.delete-expense'),
                editButtons = $('.edit-expense'),
                today = new Date(),
                formatedDate = today.getFullYear() + '-'+ today.getMonth()+'-'+today.getDay();

            $.each(deleteButtons, function(key, value){
                var item = $(value);

                if(item.attr('data-created-at') != formatedDate){
                    item.attr('disabled', 'disabled');
                    item.attr('title', 'Expense created before today, can not be deleted.');
                }
            });

            $.each(editButtons, function(key, value){
                var item = $(value);

                if(item.attr('data-created-at') != formatedDate){
                    item.attr('disabled', 'disabled');
                    item.prop('title', 'Expense created before today, can not be deleted.');
                }
            });
        },
    };

})(ExpenseUrls);
