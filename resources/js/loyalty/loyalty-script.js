WinPos.Loyalty = (function (Urls) {
    function validateCardForm() {
        const card = $('#card_number').val().trim();
        const errors = [];

        if (!card || card.length < 11 || card.length > 20) {
            errors.push('Card number must be 11 to 20 digits.');
        }

        return errors;
    }

    var saveLoyaltyCard = function (formData) {
        WinPos.Common.postAjaxCall(Urls.saveLoyaltyCard, JSON.stringify(formData), function (response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("addLoyaltyCardModal");
                location.reload();
            } else {
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateLoyaltyCard = function (formData, cardId) {
        WinPos.Common.putAjaxCallPost(Urls.updateLoyaltyCard.replace("cardId", cardId), JSON.stringify(formData), function (response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                location.reload();
            } else {
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var getLoyaltyStatus = function (customerId) {
        const url = loyaltyUrls.getLoyaltyStatus.replace('customerID', customerId);
        return new Promise((resolve, reject) => {
            WinPos.Common.getAjaxCall(
                url,
                (response) => {
                    if (response.status === 'success') {
                        console.log('Loyalty Status:', response.loyaltyInfo);
                        $('#posLoyaltyHistoryModalBtn').prop('disabled', false);
                        $('#loyaltyVerificationModal').modal('hide');
                        $("#hiddenTerminalCustomerCardId").val(response.loyaltyInfo.cardStatus.card_id);

                        $(".terminal-customer-loyalty-info").addClass("d-none"); // hide all info 

                        let settings = response.loyaltyInfo.settings;
                        let card = response.loyaltyInfo.cardStatus;

                        console.log('Settings:', card); //56464545645

                        let remainingNote = "";
                        let eligibleText = "";
                        if (settings.minimum_sales_amount_applies_for == 'Single') {
                            if (response.loyaltyInfo.isEligibleForNewCard) {
                                remainingNote = "This customer has maximum spent " + response.loyaltyInfo.currentTotalSpent + "Tk. in a single visit.";
                                eligibleText = "Eligible";
                            } else {
                                remainingNote = "New card requires a " + settings.minimum_sales_amount + " BDT minimum sales in a SINGLE visit.";
                                remainingNote += " This customer has maximum spent " + response.loyaltyInfo.currentTotalSpent + "Tk. in a single visit.";
                                eligibleText = "Not Eligible";
                            }
                        } else {

                            if (response.loyaltyInfo.isEligibleForNewCard) {
                                remainingNote = "This customer has already spent " + response.loyaltyInfo.currentTotalSpent + "Tk. in all visit.";
                                eligibleText = "Eligible";
                            } else {
                                remainingNote = "New card requires a " + settings.minimum_sales_amount + " BDT minimum sales in ALL visit.";
                                remainingNote += " This customer has already spent " + response.loyaltyInfo.currentTotalSpent + "Tk. in all visit.";
                                remainingNote += " She has to spend " + response.loyaltyInfo.needForNextCard + "Tk. more to get a new card.";
                                eligibleText = "Not Eligible";
                            }
                        }

                        let loyaltyInfo = "";

                        if (response.loyaltyInfo.status === 'Loyal') {


                            setLoyalCustomerLoyaltyInfo(response.loyaltyInfo.status, response.loyaltyInfo.visitCount, settings.max_visits, card.valid_until);


                            $('#loyaltyVerificationModal').modal('dispose').modal({
                                backdrop: 'static',
                                keyboard: false
                            }).modal('show');

                            $('#loyaltyVerificationModal').off('shown.bs.modal').on('shown.bs.modal', function () {
                                $('#verifyLoyaltyCardNumber').focus();
                            });
                        }
                        else if (response.loyaltyInfo.status === 'Limited') {
                            setLimitedCustomerLoyaltyInfo(response.loyaltyInfo.status, response.loyaltyInfo.visitCount, settings.max_visits, card.valid_until);
                        }
                        else if (response.loyaltyInfo.status === 'Completed') {
                            setCompletedCustomerLoyaltyInfo(response.loyaltyInfo.status, response.loyaltyInfo.visitCount, settings.max_visits, eligibleText, remainingNote);
                        }
                        else if (response.loyaltyInfo.status === 'Expired') {
                            setExpiredCustomerLoyaltyInfo(response.loyaltyInfo.status, response.loyaltyInfo.visitCount, settings.max_visits, eligibleText, remainingNote, card.valid_until);
                        }
                        else {
                            // No Card
                            setNoCardCustomerLoyaltyInfo(response.loyaltyInfo.status, settings.minimum_sales_amount_applies_for, response.loyaltyInfo.currentTotalSpent, eligibleText, remainingNote);

                            $('#posLoyaltyHistoryModalBtn').prop('disabled', true);
                            $('#loyaltyVerificationModal').modal('hide');


                        }

                        // $('[data-toggle="tooltip"]').tooltip('dispose');
                        // $('[data-toggle="tooltip"]').tooltip();

                    } else {
                        console.warn('Loyalty Status Error:', response.message);
                    }

                    resolve(response);
                },
                (response) => {
                    console.error('AJAX Error:', response);
                    reject(response);
                }
            );
        });
    }


    var setLoyalCustomerLoyaltyInfo = function (status, visitCount, settingsMaximumVisit, validUntil) {
        $(".terminal-customer-loyalty-info").addClass('d-none');
        $(".loyal-status-info").removeClass('d-none');
        $(".all-status-info").removeClass('d-none');

        $("#terminalCustomerLoyaltyShow").html(status);
        $("#terminalCustomerVisitShow").html(visitCount + '/' + settingsMaximumVisit);
        $("#terminalCustomerValidUntilShow").html(validUntil);
    }

    var setLimitedCustomerLoyaltyInfo = function (status, visitCount, settingsMaximumVisit, validUntil) {
        $(".terminal-customer-loyalty-info").addClass('d-none');
        $(".limitted-status-info").removeClass('d-none');
        $(".all-status-info").removeClass('d-none');

        $("#terminalCustomerLoyaltyShow").html(status + '(Loyal)');
        $("#terminalCustomerVisitShow").html(visitCount + '/' + settingsMaximumVisit + '(Used Today)');
        $("#terminalCustomerValidUntilShow").html(validUntil);
    }

    var setCompletedCustomerLoyaltyInfo = function (status, visitCount, settingsMaximumVisit, eligibleText, eligibleTooltipText) {
        $(".terminal-customer-loyalty-info").addClass('d-none');
        $(".completed-status-info").removeClass('d-none');
        $(".all-status-info").removeClass('d-none');

        $("#terminalCustomerLoyaltyShow").html(status);
        $("#terminalCustomerVisitShow").html(visitCount + '/' + settingsMaximumVisit);
        $("#terminalCustomerNextCardShow span").html(eligibleText);

        $("#terminalCustomerNextCardShow i").attr('title', eligibleTooltipText);
        $("#terminalCustomerNextCardShow i").attr('data-bs-original-title', eligibleTooltipText);
        $("#terminalCustomerNextCardShow i").attr('aria-label', eligibleTooltipText);
    }

    var setExpiredCustomerLoyaltyInfo = function (status, visitCount, settingsMaximumVisit, eligibleText, eligibleTooltipText, validUntil) {
        $(".terminal-customer-loyalty-info").addClass('d-none');
        $(".expired-status-info").removeClass('d-none');
        $(".all-status-info").removeClass('d-none');

        $("#terminalCustomerLoyaltyShow").html(status + '(' + validUntil + ')');
        $("#terminalCustomerVisitShow").html(visitCount + '/' + settingsMaximumVisit);
        $("#terminalCustomerNextCardShow span").html(eligibleText);

        $("#terminalCustomerNextCardShow i").attr('title', eligibleTooltipText);
        $("#terminalCustomerNextCardShow i").attr('data-bs-original-title', eligibleTooltipText);
        $("#terminalCustomerNextCardShow i").attr('aria-label', eligibleTooltipText);

        // data-bs-original-title

    }

    var setNoCardCustomerLoyaltyInfo = function (status, settingMinimumSalesAmountSales, currentTotalSpent, eligibleText, eligibleTooltipText) {
        $(".terminal-customer-loyalty-info").addClass('d-none');
        $(".new-customer-info").removeClass('d-none');
        $(".all-status-info").removeClass('d-none');

        $("#terminalCustomerLoyaltyShow").html(status);

        if (settingMinimumSalesAmountSales == 'Single') {
            $("#terminalCustomerMaxSalesShow").html(currentTotalSpent + ' Tk');
        } else {
            $("#terminalCustomerTotalSpentShow").html(currentTotalSpent + ' Tk');
        }

        $("#terminalCustomerNextCardShow span").html(eligibleText);

        // $("#terminalCustomerNextCardShow i").attr('title', eligibleTooltipText);
        $("#terminalCustomerNextCardShow i").attr('data-bs-original-title', eligibleTooltipText);
        $("#terminalCustomerNextCardShow i").attr('aria-label', eligibleTooltipText);
    }


    var setNewCustomerLoyaltyInfo = function () {
        $(".terminal-customer-loyalty-info").addClass('d-none');
        $(".new-customer-info").removeClass('d-none');
        $(".all-status-info").removeClass('d-none');

        let minimumSalesAmount = $("#terminalCustomerLoyaltyInfo").data('minimum-sales-amount');
        let minimumSalesAmountSales = $("#terminalCustomerLoyaltyInfo").data('minimum-sales-amount-sales');

        $("#terminalCustomerLoyaltyShow").html('No Card');

        if (minimumSalesAmountSales == 'Single') {
            $("#terminalCustomerMaxSalesShow").html('0 Tk');
        } else {
            $("#terminalCustomerTotalSpentShow").html('0 Tk');
        }

        $("#terminalCustomerNextCardShow span").html('Not Eligible');
        $("#terminalCustomerNextCardShow i").attr('title', `New card requires a ${minimumSalesAmount} BDT minimum sales in ${minimumSalesAmountSales} visit.`);
    }

    var getCardHistory = function (cardId) {
        const url = loyaltyUrls.getCardHistory.replace('cardId', cardId);
        return new Promise((resolve, reject) => {
            WinPos.Common.getAjaxCall(
                url,
                (response) => {
                    if (response.status === 'success') {
                        console.log('Loyalty Status:', response);
                        populateHistoryTable(response.data);
                    } else {
                        console.log('Loyalty e Status Error:', response);
                    }

                    resolve(response);
                },
                (response) => {
                    console.error('AJAX Error:', response);
                    reject(response);
                }
            );
        });
    }


    function populateHistoryTable(historyData) {
        const tableBody = $('#cardHistoryTable tbody');
        tableBody.empty(); // Clear existing rows

        if (historyData.length === 0) {
            tableBody.append(`
                <tr>
                    <td colspan="8" class="text-center align-middle">No history found for this card</td>
                </tr>
            `);
            return;
        }

        historyData.forEach(function (history) {
            const row = `
                <tr>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.visit_number}</td>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.date}</td>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.invoice_no}</td>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.total_amount}</td>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.discount_type === 'Percentage' ? history.discount + '%' : history.discount + '(Fixed)'}</td>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.discount_amount}</td>
                    <td class="text-center align-middle ${history.isSkipped ? 'bg-warning' : ''}">${history.note}</td>
                </tr>
            `;
            tableBody.append(row);
        });

        // Re-initialize tooltips for new buttons
        $('[data-toggle="tooltip"]').tooltip();
    }

    var verifyLoyaltyCardAndGetHistory = function (cardNumber, customerId) {
        const data = {
            card_number: cardNumber,
            customer_id: customerId
        };

        console.log('Verifying Loyalty Card with data:', data);

        return new Promise((resolve, reject) => {
            WinPos.Common.postAjaxCall(
                loyaltyUrls.verifyCard,
                JSON.stringify(data),
                (response) => {
                    if (response.status === 'success') {
                        console.log('Loyalty Card Verified:', response);
                        let card = response.card;
                        if (card.card_data) {
                            if (card.status === 'Loyal') {
                                toastr.success("Loyalty Card Verified Successfully");

                                WinPos.Pos.customer.setCustomerLoyaltyCard({
                                    id: response.card.card_id,
                                    card_number: response.card.card_number,
                                    status: response.card.status,
                                    verifyCard: true
                                });

                                // We can show an verifyed icon
                                $(document).find("#terminalCustomerLoyaltyShow").html(
                                    'Loyal <i title="Verified" class="fa-solid fa-circle-check text-success"></i>'
                                );

                                $("#loyaltyVerificationModal").modal('hide');
                                $('#verifyLoyaltyCardNumber').val('');
                            } else {
                                toastr.success("This card is not eligible for Loyalty Program. The current card status is " + card.status);
                            }
                        }else{
                            toastr.warning('Pleas enter a valid card number.');
                            $('#verifyLoyaltyCardNumber').focus();
                        }

                    } else {
                        console.warn('Loyalty Card Verification Error:', response.errors);
                        WinPos.Common.showValidationErrors(response.errors);
                        $('#verifyLoyaltyCardNumber').focus();
                    }
                    resolve(response);
                },
                (response) => {
                    $('#verifyLoyaltyCardNumber').focus();
                    console.error('AJAX Error:', response);
                    reject(response);
                }
            );
        });
    }

    var prepareLoyaltyHistoryModal = function (loyaltyHistory) {
        console.log('Loyalty History:', loyaltyHistory);
        const tbody = $('#loyaltyHistoryTable tbody');
        tbody.empty();

        if (loyaltyHistory.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center">No history found</td></tr>');
        } else {
            loyaltyHistory.forEach(function (row) {

                // if row.isSkipped is true, add a warning class to the row
                if (row.isSkipped) {
                    tbody.append(`
                        <tr>
                            <td class="bg-warning">${row.date}</td>
                            <td class="bg-warning">${row.invoice_no}</td>
                            <td class="bg-warning">${row.total_amount}</td>
                            <td class="bg-warning">${row.discount_type === 'Percentage' ? row.discount + '%' : row.discount_type}</td>
                            <td class="bg-warning">${row.discount_amount}</td>
                            <td class="bg-warning">${row.note}</td>
                        </tr>
                    `);
                    return;
                } else {
                    tbody.append(`
                        <tr>
                            <td>${row.date}</td>
                            <td>${row.invoice_no}</td>
                            <td>${row.total_amount}</td>
                            <td>${row.discount_type === 'Percentage' ? row.discount + '%' : row.discount_type}</td>
                            <td>${row.discount_amount}</td>
                            <td>${row.note}</td>
                        </tr>
                    `);
                }
            });
        }
    }

    var showLoyaltyHistoryModal = function (cardId) {
        console.log('Card ID:', cardId);
        const url = loyaltyUrls.getCardHistory.replace('cardId', cardId);
        return new Promise((resolve, reject) => {
            WinPos.Common.getAjaxCall(
                url,
                (response) => {
                    if (response.status === 'success') {
                        console.log('Loyalty Status:', response);
                        prepareLoyaltyHistoryModal(response.data);
                        $('#loyaltyHistoryModal').modal('show');
                    } else {
                        console.log('Loyalty e Status Error:', response);
                    }

                    resolve(response);
                },
                (response) => {
                    console.error('AJAX Error:', response);
                    reject(response);
                }
            );
        });
    }


    return {
        validateCardForm: validateCardForm,
        saveLoyaltyCard: saveLoyaltyCard,
        getLoyaltyStatus: getLoyaltyStatus,
        verifyLoyaltyCardAndGetHistory: verifyLoyaltyCardAndGetHistory,
        prepareLoyaltyHistoryModal: prepareLoyaltyHistoryModal,
        getCardHistory: getCardHistory,
        updateLoyaltyCard: updateLoyaltyCard,
        showLoyaltyHistoryModal: showLoyaltyHistoryModal,
        setNewCustomerLoyaltyInfo: setNewCustomerLoyaltyInfo
    }
})(loyaltyUrls);
