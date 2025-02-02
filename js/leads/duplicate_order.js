$(document).ready(function (){
    // clearTimeout(timerId);
    $("#artModal").find('div.modal-dialog').css('width','1004px');
    $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
    init_onlineleadorder_edit();
    init_rushpast();
    $('select.addnewitem').select2({
        dropdownParent: $('#artModal'),
        matcher: matchStart,
    });
    if (parseInt($("#ordermapuse").val())==1) {
        // Init billing autofill
        if ($("#billorder_line1").length > 0) {
            initBillOrderAutocomplete();
        }
        // Init simple Shipping address
        if ($("#shiporder_line1").length > 0) {
            initShipOrderAutocomplete();
        }
    }
})