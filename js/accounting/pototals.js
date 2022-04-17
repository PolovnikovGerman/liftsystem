function init_purchase_orders() {
    init_potables_content();
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

function init_poorders_content() {
    jQuery.balloon.init();
    $(".poplace-tablerow").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        }
    );
    // Manage methods
    $(".manage-purchase-methods").unbind('click').click(function () {
        init_manage_methods();
    });
    // Show / hide inner
    $(".pototals-placefiltr").find('i').unbind('click').click(function () {
        change_inner_filter();
    });
    $(".addnonlistedpo").unbind('click').click(function () {
        add_newamount();
    });
    $(".poplace-poactionbtn").unbind('click').click(function(){
        var order = $(this).data('order');
        add_notplacedpo(order);
    });
    $(".poreportsortselect").unbind('change').change(function () {
        var page = $("#poreportcurpage").val();
        pagePOReportCallback(page);
    });
    $(".poyearcompare").unbind('change').change(function () {
        init_poreport_Pagination();
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
            $("#modalEditpurchase").find('div.modal-body').empty().html(response.data.content);
            $("#modalEditpurchase").modal({backdrop: 'static', keyboard: false, show: true});
            init_poedit();
            // Date picker
            $("input#podateinpt").datepicker({
                autoclose: true,
                todayHighlight: true,
            }).on("change", function() {
                show_amountsave();
                save_amntdetails('amount_date', $(this).val());
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

function add_notplacedpo(order) {
    var url="/purchaseorders/purchasenotplaced_add";
    $.post(url, {'order_id': order}, function(response){
        if (response.errors=='') {
            $("#modalEditpurchase").find('div.modal-body').empty().html(response.data.content);
            $("#modalEditpurchase").modal({backdrop: 'static', keyboard: false, show: true});
            init_poedit();
            // Date picker
            $("input#podateinpt").datepicker({
                autoclose: true,
                todayHighlight: true,
            }).on("change", function() {
                show_amountsave();
                save_amntdetails('amount_date', $(this).val());
            });
        } else {
            show_error(response);
        }
    },'json');
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
        $("input#newpoorder").keypress(function(event){
            var ordernum = $("input#newpoorder").val()+String.fromCharCode(event.which);
            search_poorderdata(ordernum);
        });
    } else {
        lock_poeditflds(0);
    }
    $("input.amountvalueinpt").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('amount_sum', newval);
    });
    $("input.po_shipping").unbind('click').click(function(){
        var value=0;
        if ($(this).prop('checked')==true) {
            value=1;
        }
        show_amountsave();
        save_amntdetails('is_shipping', value);
    });
    $("select.amountvendorselect").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('vendor_id', newval);
    })
    $("select.amountmethodselect").unbind('change').change(function(){
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
    $("div.poamount-save").find('img').unbind('click').click(function(){
        save_amount();
    });
}

/* Lock/Unlock PO Edit fields */
function lock_poeditflds(type) {
    if (type==1) {
        $("input.poamntdateinpt").prop('readonly',true);
        // $("input.amountvalueinpt").prop('readonly',true);
        $("input.po_shipping").prop('disabled',true);
        $("select.amountvendorselect").prop('disabled',true);
        $("select.amountmethodselect").prop('disabled',true);
        $("textarea.poreasondata").prop('readonly',true);
    } else {
        $("input.poamntdateinpt").prop('readonly',false);
        // $("input.amountvalueinpt").prop('readonly',false);
        $("input.po_shipping").prop('disabled',false);
        $("select.amountvendorselect").prop('disabled',false);
        $("select.amountmethodselect").prop('disabled',false);
        $("textarea.poreasondata").prop('readonly',false);
    }
}
/* Check Order # in mode ADD PO */
function search_poorderdata(order_num) {
    var url="/purchaseorders/purchaseorder_presearch";
    // var  = $("input#newpoorder").val();
    $.post(url, {'order_num':order_num}, function(response){
        if (response.errors=='') {
            if (parseInt(response.data.find)===1) {
                $(".ordercustomerplace").empty().html(response.data.customer);
                $(".orderitemnameplace").empty().html(response.data.item);
                $(".amountprofitval").empty().html(response.data.profitval);
                $(".amountprofitprc").empty().html(response.data.profitprc);
                $(".amountprofitval").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profitclass);
                $(".amountprofitprc").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profitclass);
            }
        } else {
            show_error(response);
        }
    },'json');
}
/* Check Order # in mode ADD PO */
function order_purchase_details(order_num) {
    var url="/purchaseorders/purchaseorder_details";
    $.post(url, {'order_num':order_num}, function(response){
        if (response.errors=='') {
            $("div#orderdataarea").empty().html(response.data.content);
            $(".orderitemnameplace").empty().html(response.data.item);
            $(".amountprofitval").empty().html(response.data.profitval);
            $(".amountprofitprc").empty().html(response.data.profitprc);
            $(".amountprofitval").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profitclass);
            $(".amountprofitprc").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profitclass);
            if (parseInt(response.data.vendor_id)>0) {
                $(".amountvendorselect").val(response.data.vendor_id);
            }
            if (parseInt(response.data.is_shipping)==1) {
                $("input.po_shipping").prop('checked',true);
            } else {
                $("input.po_shipping").prop('checked',false);
            }
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
                $("div.amountprofitval").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profit_class);
                $("div.amountprofitprc").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profit_class);
            }
            if (response.data.profit_perc) {
                $("div.amountprofitprc").empty().html(response.data.profit_perc);
            }
            if (response.data.profit) {
                $("div.amountprofitval").empty().html(response.data.profit);
            }
            $("div#lowprofitpercreasonarea").empty().html(response.data.reason);
            $("textarea#po_comment").unbind('change').change(function(){
                var newval=$(this).val();
                show_amountsave();
                save_amntdetails('reason', newval);
            });
            // $("textarea#po_comment").unbind('change').change(function(){
            //     var newval=$(this).val();
            //     show_amountsave();
            //     save_amntdetails('reason', newval);
            // });
        } else {
            show_error(response);
        }
    },'json');
}
/* Save Amount DATA to DB */
function save_amount() {
    var url="/purchaseorders/purchaseorder_amountsave";
    var data = new Array();
    data.push({name:'brand', value: $("#pototalsbrand").val()});
    data.push({name: 'inner', value: $("#pototalsinner").val()});
    $.post(url, data, function(response){
        if (response.errors=='') {
            $("#modalEditpurchase").modal('hide');
            $(".pototals-unsign-tolalqty").empty().html(response.data.toplace_qty);
            $(".pototals-unsign-tolalsum").empty().html(response.data.toplace_sum);
            $(".pototals-approved-tolalqty").empty().html(response.data.toapprove_qty);
            $(".pototals-approved-tolalsum").empty().html(response.data.toapprove_sum);
            $(".pototals-proof-tolalqty").empty().html(response.data.toproof_qty);
            $(".pototals-proof-tolalsum").empty().html(response.data.toproof_sum);
            init_potables_content();
            // initPurchaseOrderPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}
