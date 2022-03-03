function init_purchase_orders() {
    init_potables_content();
    init_poorders_content();
}

function init_poorders_content() {
    $(".manage-purchase-methods").unbind('click').click(function () {
        init_manage_methods();
    });
    $(".pototals-placefiltr").find('i').unbind('click').click(function () {
        change_inner_filter();
    });
    $(".addnonlistedpo").unbind('click').unbind('click').click(function () {
        add_newamount();
    });
}

function init_manage_methods() {
    var url="/purchaseorders/purchasemethods_edit";
    $.post(url,{}, function (response) {
        if (response.errors=='') {
            $("#modalManage").find('div.modal-body').empty().html(response.data.content);
            $("#modalManage").modal({backdrop: 'static', keyboard: false, show: true});
            init_managemethods_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function init_managemethods_edit() {
    $(".purchmethodaction.deactivate").unbind('click').click(function () {
        var methodname = $(this).data('methodlabel');
        if (confirm('Deactivate purchase method '+methodname+'?')==true) {
            var params = new Array();
            params.push({name: 'method', value: $(this).data('method')});
            params.push({name: 'action', value: 0});
            var url = '/purchaseorders/purchasemethod_status';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#activemethods").empty().html(response.data.active);
                    $("#inactivemethods").empty().html(response.data.inactive);
                    init_managemethods_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    $(".purchmethodaction.activate").unbind('click').click(function () {
        var methodname = $(this).data('methodlabel');
        if (confirm('Activate purchase method '+methodname+'?')==true) {
            var params = new Array();
            params.push({name: 'method', value: $(this).data('method')});
            params.push({name: 'action', value: 1});
            var url = '/purchaseorders/purchasemethod_status';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#activemethods").empty().html(response.data.active);
                    $("#inactivemethods").empty().html(response.data.inactive);
                    init_managemethods_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $(".addmethodbtn").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'method', value: $(".addnewpurchase").val()});
        var url = '/purchaseorders/purchasemethod_new';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#activemethods").empty().html(response.data.active);
                $(".addnewpurchase").val('');
                init_managemethods_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_potables_content() {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    params.push({name: 'inner', value: $("#pototalsinner").val()});
    var url = '/purchaseorders/pototals_details';
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $(".poplace-unsigntablebody").empty().html(response.data.unsignview);
            $(".poplace-approvtablebody").empty().html(response.data.approvview);
            $(".poplace-prooftablebody").empty().html(response.data.needproofview);
            init_poorders_content();
        } else {
            show_error(response);
        }
    },'json');
}

function change_inner_filter() {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    params.push({name: 'inner', value: $("#pototalsinner").val()});
    var url = '/purchaseorders/pototals_filter';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#pototalsinner").val(response.data.inner);
            $(".pototals-placefiltr").empty().html(response.data.filtr);
            $(".pototals-unsign-tolalqty").empty().html(response.data.toplace_qty);
            $(".pototals-unsign-tolalsum").empty().html(response.data.toplace_sum);
            $(".pototals-approved-tolalqty").empty().html(response.data.toapprove_qty);
            $(".pototals-approved-tolalsum").empty().html(response.data.toapprove_sum);
            $(".pototals-proof-tolalqty").empty().html(response.data.toproof_qty);
            $(".pototals-proof-tolalsum").empty().html(response.data.toproof_sum);
            init_potables_content();
        } else {
            show_error(response);
        }
    },'json')
}

/* Add NEW PO */
function add_newamount() {
    var amountid=0;
    var url="/purchaseorders/purchaseorder_edit";
    $.post(url, {'amount_id': amountid}, function(response){
        if (response.errors=='') {
            /* $("#pageModal").find('div.modal-dialog').css('width','625px'); */
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_poedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* PO EDIT FUnctions */
/* Common Edit INIT */
function init_poedit() {
    // Change Ship Check
    // Add Order Data
    if ($("input#newpoorder").length>0) {
        // Lock Input fields
        lock_poeditflds(1);
        $("input#newpoorder").unbind('change').change(function(){
            order_purchase_details($(this).val());
        });
    } else {
        lock_poeditflds(0);
    }
    $("input#amount_sum").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('amount_sum', newval);
    });
    $("input#po_shipping").unbind('click').click(function(){
        var value=0;
        if ($(this).prop('checked')==true) {
            value=1;
        }
        show_amountsave();
        save_amntdetails('is_shipping', value);
    });
    $("select#vendor_id").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('vendor_id', newval);
    })
    $("select#method_id").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('method_id', newval);
    });
    $("textarea#change_comment").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('comment', newval);
    });
    $("textarea#po_comment").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('low_profit', newval);
    });
    $("div.poamount-save").unbind('click').click(function(){
        save_amount();
    });
}

/* Lock/Unlock PO Edit fields */
function lock_poeditflds(type) {
    if (type==1) {
        $("input#amount_date").prop('readonly',true);
        $("input#amount_sum").prop('readonly',true);
        $("input#po_shipping").prop('disabled',true);
        $("select#vendor_id").prop('disabled',true);
        $("select#method_id").prop('disabled',true);
        $("textarea#po_comment").prop('readonly',true);
        $("textarea#po_comment").prop('readonly',true);
    } else {
        $("input#amount_date").prop('readonly',false);
        $("input#amount_sum").prop('readonly',false);
        $("input#po_shipping").prop('disabled',false);
        $("select#vendor_id").prop('disabled',false);
        $("select#method_id").prop('disabled',false);
        $("textarea#po_comment").prop('readonly',false);
        $("textarea#po_comment").prop('readonly',false);
        // $("div.poamount-shippinginput").jqTransform();
        // $("input#po_shipping").ezMark();
        $("input#amount_date").datepicker({
            autoclose: true,
            todayHighlight: true,
        }).on("change", function() {
            show_amountsave();
            save_amntdetails('amount_date', $(this).val());
        });
    }
}

/* Check Order # in mode ADD PO */
function order_purchase_details(order_num) {
    var url="/purchaseorders/purchaseorder_details";
    $.post(url, {'order_num':order_num}, function(response){
        if (response.errors=='') {
            $("div#orderdataarea").empty().html(response.data.content);
            lock_poeditflds(0);
            show_amountsave();
        } else {
            show_error(response);
            $('input#newpoorder').val('').focus();
        }
    }, 'json');
}

/* Show Save Button () */
function show_amountsave() {
    $("div.poamount-save").show();
}
/* Save in session AMOUNT DETAILS */
function save_amntdetails(fldname, newval) {
    // STOPED THEIR
    var url="/purchaseorders/purchaseorder_amountchange";
    $.post(url, {'fld': fldname, 'value':newval}, function(response){
        if (response.errors=='') {
            if (response.data.profit_class) {
                // poprofit-data
                // $("div.profit_class").removeClass()
                $("div.poprofit-data").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profit_class);
                $("div.poprofitperc").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profit_class);
            }
            if (response.data.profit_perc) {
                $("div.poprofitperc").empty().html(response.data.profit_perc);
            }
            if (response.data.profit) {
                $("div.poprofit-data").empty().html(response.data.profit);
            }
            $("div#lowprofitpercreasonarea").empty().html(response.data.reason);
            $("textarea#po_comment").unbind('change').change(function(){
                var newval=$(this).val();
                show_amountsave();
                save_amntdetails('reason', newval);
            });
            $("textarea#po_comment").unbind('change').change(function(){
                var newval=$(this).val();
                show_amountsave();
                save_amntdetails('reason', newval);
            });
        } else {
            show_error(response);
        }
    },'json');
}
/* Save Amount DATA to DB */
function save_amount() {
    var url="/purchaseorders/purchaseorder_amountsave";
    $("#loader").show();
    var data = new Array();
    data.push({name:'brand', value: $("#purchaseordersbrand").val()});
    $.post(url, data, function(response){
        if (response.errors=='') {
            $("input#pototal_total").val(response.data.totals);
            $("#pageModal").modal('hide');
            initPurchaseOrderPagination();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}
