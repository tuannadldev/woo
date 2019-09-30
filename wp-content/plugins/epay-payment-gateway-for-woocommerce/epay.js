
var path = wpurl.siteurl;

window.addEventListener('message', function (e) {
    if (e.data.closeLayer == 'close') {
        alert("Lưu ý: Đơn hàng của bạn có thể bị huỷ khi bạn tắt popup thanh toán! Hệ thống sẽ tự động chuyển hướng về trang giỏ hàng sau vài giây để bạn có thể thanh toán lại.");
            window.top.closeLayer();
            jQuery("#megapayForm").appendTo("#payment-form-wrapper");
            jQuery.ajax({
                url: postepay.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {action: 'remove_order_fail', order_id: getSearchParams('process')},
                success: function (res) {
                    window.location.href = path+'/cart';
                }
            });
    }
});

jQuery(document).ready(function(){
    if(getSearchParams('process')){
        var el = document.getElementById('megapayForm');
        if (el) {
            payment();
        }
        else{
            jQuery('.payment-icon').show();
            setTimeout(payment, 3000);
        }
    }
});
function getSearchParams(k){
    var p={};
    location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
    return k?p[k]:p;
}


function payment() {

    //$('#megapayForm input[type="submit"]').prop('disabled', true);

    jQuery('.payment-icon').show();

    var goodsAmount = jQuery('#goodsAmount').val();
    var userFee = jQuery('#userFee').val();
    // Check Amount
    if (goodsAmount == '' || goodsAmount == null) {
        alert('Please Enter Amount!');
        jQuery('#goodsAmount').focus();
        return false;
    }

    if (userFee == '' || userFee == null) {
        alert('userFee not null!');
        return false;
    }

    var amount = parseInt(goodsAmount) + parseInt(userFee);

    jQuery.ajax({
        url: postepay.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {action: 'token_generate', amount: amount, type: 1, order_id: getSearchParams('process')},
        success: function (res) {
            if (res.success) {

                domain = res.domain;
                paymentForm = document.getElementById('megapayForm');

                paymentForm.elements["timeStamp"].value = res.timeStamp;
                paymentForm.elements['merTrxId'].value = res.merTrxId;
                paymentForm.elements["merchantToken"].value = res.token;
                paymentForm.elements["amount"].value = amount;
                paymentForm.elements["buyerFirstNm"].value = jQuery('#billing_last_name').val();
                paymentForm.elements["buyerLastNm"].value = jQuery('#billing_last_name').val();

                paymentForm.elements["invoiceNo"].value = getSearchParams('process');

                jQuery("#megapayForm").appendTo("body");
                jQuery('.form #megapayForm').remove();
                jQuery('.payment-icon').hide();
                openPayment(1, domain);
            } else {
                alert(res.mes);
            }
        },
        error: function () {
            alert('Có lỗi trong quá trình xử lý!');
        }
    });
}

// Function Check Transaction
function status() {
    var merId = jQuery('#merId').val();
    var merTrxId = jQuery('#merTrxId').val();

    // Check Merchant ID
    if (merId == '') {
        alert('Please Enter Merchant ID!');
        jQuery('#merId').focus();
        return false;
    }

    // Check Merchant Transaction ID
    if (merTrxId == '') {
        alert('Please Enter Merchant Transaction ID!');
        jQuery('#merTrxId').focus();
        return false;
    }

    $.ajax({
        url: path + 'process',
        type: 'post',
        dataType: 'json',
        data: {type: 2, merId: merId, merTrxId: merTrxId},
        success: function (res) {
            if (res.success) {
                var transStatusForm = document.getElementById('transStatusForm');
                transStatusForm.elements["timeStamp"].value = res.timeStamp;
                transStatusForm.elements["merchantToken"].value = res.token;

                transStatusForm.submit();
            } else {
                alert(res.mes);
            }
        },
        error: function () {
            alert('Có lỗi trong quá trình xử lý!');
        }
    });
}

// Function Refund
function refund() {
    var merId = $('#merIdCancel').val();
    var merTrxId = $('#merTrxIdCancel').val();
    var trxId = $('#trxIdCancel').val();
    var amount = $('#amountCancel').val();

    // Check Merchant ID
    if (merId == '') {
        alert('Please Enter Merchant ID!');
        $('#merIdCancel').focus();
        return false;
    }

    // Check Merchant Transaction ID
    if (merTrxId == '') {
        alert('Please Enter Merchant Transaction ID!');
        $('#merTrxIdCancel').focus();
        return false;
    }

    // Check Trx ID
    if (trxId == '') {
        alert('Please Enter Trx ID!');
        $('#trxIdCancel').focus();
        return false;
    }

    // Check Amount
    if (amount == '') {
        alert('Please Enter Amount!');
        $('#amountCancel').focus();
        return false;
    }

    $.ajax({
        url: path + 'process',
        type: 'post',
        dataType: 'json',
        data: {type: 3, merId: merId, merTrxId: merTrxId, trxId: trxId, amount: amount},
        success: function (res) {
            if (res.success) {
                console.log(res.token);
                var refundForm = document.getElementById('refundForm');

                refundForm.elements["timeStamp"].value = res.timeStamp;
                refundForm.elements["merchantToken"].value = res.token;
                refundForm.elements["cancelPw"].value = res.cancelPw;

                refundForm.submit();
            } else {
                alert(res.mes);
            }
        },
        error: function () {
            alert('Có lỗi trong quá trình xử lý!');
        }
    });
}
