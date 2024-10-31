function init_pototals() {
    if ($(".pooverdataview").css('display', 'block')) {
        init_pooverview();
    } else {
        init_pohistory()
    }
}

function init_pooverview() {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    params.push({name: 'domesticpoyear', value: $("#domesticpoyear").val()});
    params.push({name: 'custompoyear', value: $("#custompoyear").val()});
    var url = "/purchaseorders/pooverview";
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pooverviewdomestictablearea").empty().html(response.data.otherview)
            $(".pooverviewcustomtablearea").empty().html(response.data.customview);
            $("#loader").hide();
            leftmenu_alignment();
            init_pooverview_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_pooverview_content() {
    $(".pohistoryviewlink").unbind('click').click(function (){
        $(".pooverdataview").hide();
        $(".pohistorydataview").show();
        init_pohistory();
    });
    // Order
    $(".pooverviewcustomtablearea").find('div.ordernum').unbind('click').click(function (){
        var order = $(this).data('order');
        poedit_order(order);
    })
    $(".pooverviewdomestictablearea").find('div.ordernum').unbind('click').click(function (){
        var order = $(this).data('order');
        poedit_order(order);
    })
}

function poedit_order(order) {
    var callpage = 'pooverview';
    var brand = $("#pototalsbrand").val();
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(order)==0) {
                init_onlineleadorder_edit();
                init_rushpast();
                if (parseInt($("#ordermapuse").val())==1) {
                    // Init simple Shipping address
                    initShipOrderAutocomplete();
                    if ($("#billorder_line1").length > 0) {
                        initBillOrderAutocomplete();
                    }
                }
            } else {
                if (parseInt(response.data.cancelorder)===1) {
                    $("#artModal").find('div.modal-header').addClass('cancelorder');
                } else {
                    $("#artModal").find('div.modal-header').removeClass('cancelorder');
                }
                navigation_init();
            }
        } else {
            show_error(response);
        }
    },'json');
}
