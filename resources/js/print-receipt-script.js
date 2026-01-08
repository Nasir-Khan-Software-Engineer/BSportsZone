WinPos.PrintReceipt = (function(){
    return {
    // Implementing the printing functionality as strtagy pattern
    // Select algorighm (receipt type in this case) using config property
    // Type property will contain the implementation for every receipt
    // We can move the implementation of type property in the printing page

    config: [],
    algoTypes: {},
    receiptDomString: [],

    getPreview: function(data){
        this.receiptDomString = [];

        for(let x in this.config){
            let type = this.config[x];

            if(this.algoTypes.hasOwnProperty(type)){
                let domString = this.algoTypes[type].preparePreview(data.hasOwnProperty(type) ? data[type]:{company_address: '', company_logo: '', company_phone: ''});

                this.receiptDomString.push(domString);
            }
       }

       return this.receiptDomString.join('');
    },

    print: function(domString, redirectTo = ''){
        let printWindow = window.open('', '', 'width=600,height=400');

        printWindow.document.write('<html><head><title></title>');
        printWindow.document.write('<style>body { font-family: Arial, sans-serif; line-height: 16px; font-size: 11pt; font-weight: 800px;} p {margin: 0px;} .nl{display:block; height: 5px;} @media print {body {width: 80mm;}}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(domString);

        //printWindow.document.write('<script> window.print(); window.onafterprint = window.close;<script>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        printWindow.focus();
        printWindow.addEventListener('load', function () {
            printWindow.print();

            const printTimer = setInterval(function() {
                if (!printWindow.document.readyState || printWindow.document.readyState === 'complete') {
                    clearInterval(printTimer);
                    printWindow.close();

                    // Reload the original window
                    if (window.opener) {
                        if(redirectTo == ''){
                            window.opener.location.reload();
                        }else{
                            window.opener.location = redirectTo;
                        }
                    } else {
                        if(redirectTo == ''){
                            window.location.reload();
                        }else{
                            window.location = redirectTo;
                        }
                    }
                }
            }, 100);
        });


        //printWindow.addEventListener('load', function () {
            //printWindow.print();
            //printWindow.onafterprint = printWindow.close;

            //setTimeout(function(){
                //printWindow.close();
            //}, 0);
        //});

        window.onafterprint = function() {
            window.location.reload();
        }
    }
}

})();

WinPos.PrintReceipt.algoTypes.header = {
    preparePreview: function(data){
        const dom = [];
        dom.push('<div style="text-align: center;">');
        dom.push('<div style="display: block;"><img style="max-height: 100px;" src="'+data.company_pos_logo+'" alt="Company Logo"></div>');
        dom.push('<span class="nl"></span>');
        dom.push('<h4 style="margin: 0px;">'+data['company_name']+'</h4>');
        dom.push('<span class="nl"></span>');
        dom.push('<p>'+data['company_phone']+'</p>');
        dom.push('<span class="nl"></span>');
        dom.push('<p style="margin: 0px;">'+data['company_address']+'</p>');
        dom.push('</div>');
        dom.push('<br>');

        return dom.join('');
    }
}


WinPos.PrintReceipt.algoTypes.footer = {
    preparePreview: function(data){
        const dom = [];

        dom.push('<div style="width: 100%; text-align: center;">');
        dom.push('<p>------------------------------------------------------------</p>');

        dom.push('<br>');

        dom.push('<p>***THANK YOU. COME AGAIN***</p><span class="nl"></span>');
        //dom.push('<p>Exchange and return only with invoice, within 15 days from the date of sales (T&C apply)</p>');
        dom.push('<br>');
        dom.push('<p>Powered By: ParlourPOS</p>');
        dom.push('<p>www.ParlourPOS.com</p>');
        dom.push('<br>');
        dom.push('</div>');

        return dom.join('');
    }
}

WinPos.PrintReceipt.algoTypes.salesDetails = {
    preparePreview: function(data){
        let dom = [];
        let date = new Date();
       

        let options = {
            day: "numeric",
            month: "short",
            year: "numeric",
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
            timeZone: "Asia/Dhaka"
        };

        let formattedDate = new Intl.DateTimeFormat("en-US", options).format(date);
        formattedDate = formattedDate.replace(",", "");

        let c_name = (data.customer.name == undefined)?' ':data.customer.name;
        let c_phone = (data.customer.phone1 == undefined)?'':data.customer.phone1;
        let time = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();


        dom.push('<style>');
            dom.push('.item{width: 100%; clear: both; display: block; overflow: hidden;} .item .item-left{width: 70%; float:left; text-align: left;} .item .item-right{width: 28%; float: right; text-align: right;}');
            dom.push('.total-row{width: 100%; display: block; overflow: hidden; text-align: right;} .total-key{width: 78%; float: left;} .total-value{width: 20%; float: right; text-align: left;}');
        dom.push('</style>');


        dom.push('<p>Cashier: '+data.cashier.name+'</p>');
        dom.push('<p style="margin-bottom: 5px;">' + formattedDate + '</p>');

        dom.push('<p>Customer: '+c_name+'</p>');
        dom.push('<p>Phone: '+c_phone+'</p><span class="nl"></span>');
        dom.push('<table style="width: 100%;">');
            dom.push('<tr>');
                dom.push('<th style="text-align: left;">ITEM</th>');
                dom.push('<th style="width: 50px; text-align: right;">QTY</th>');
                dom.push('<th style="width: 50px; text-align: right;">UP</th>');
                dom.push('<th style="width: 50px; text-align: right;">TOT</th>');
            dom.push('</tr>');
        dom.push('</table>');
        dom.push('<p style="text-align: center;">-------------------------------------------------------------</p>');
        dom.push('<table style="width: 100%;">');

        data.cartInfo.items.forEach(function(item){
            let shortServiceName = (item.name.length > 20 ? (item.name).slice(0,20) : item.name);
            let staffName = item.staff_name || 'None';

            dom.push('<tr>');
                if(item.tagline){
                    dom.push('<td style="text-align: left;">' + shortServiceName + ' (' + item.tagline + ')</td>');
                }else{
                    dom.push('<td style="text-align: left;">' + shortServiceName + '</td>');
                }
                dom.push('<td style="width: 50px; text-align: right;">'+ item.quantity +'</td>');
                dom.push('<td style="width: 50px; text-align: right;">'+ (item.price) +'</td>');
                dom.push('<td style="width: 50px; text-align: right;">'+ ((item.price) * item.quantity) +'</td>');
            dom.push('</tr>');
            if (staffName !== 'None') {
                dom.push('<tr>');
                    dom.push('<td colspan="4" style="text-align: left; font-size: 10px; padding-left: 10px;">Staff: ' + staffName + '</td>');
                dom.push('</tr>');
            }
        });

        dom.push('</table>');

        dom.push('<p style="text-align: center;">-------------------------------------------------------------</p>');
        const totalAmount = data.cartInfo.total;
        const discountAmount = (data.cartInfo.discountType == 'fixed')? data.cartInfo.discount : (totalAmount * data.cartInfo.discount)/100;
        const adjustmentAmount = data.cartInfo.adjustment;
        const finalAmount = (totalAmount - discountAmount) + adjustmentAmount;
        const loyalty = data.cartInfo.loyaltyCard.verifyCard ? true : false;

        dom.push('<div class="total-row">');

            dom.push('<div class="total-key">');
                dom.push('<p>TOTAL: </p>');
                dom.push('<p>' + (loyalty ? 'LOYALTY ' : '') + 'DISCOUNT: </p>');
                dom.push('<p>ADJUSTMENT: </p>');
                dom.push('<p>PAYABLE: </p>');
            dom.push('</div>');

            dom.push('<div class="total-value">');
                dom.push('<p>'+data.cartInfo.total.toFixed(2)+'</p>');
                dom.push('<p>'+discountAmount.toFixed(2)+'</p>');
                dom.push('<p>'+data.cartInfo.adjustment.toFixed(2)+'</p>');
                dom.push('<p>'+(finalAmount).toFixed(2)+'</p>');
            dom.push('</div>');
        dom.push('</div>');

        return dom.join('');
    }
}
