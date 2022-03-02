function init_purchase_orders() {
    init_potables_content();
    init_poorders_content();
}

function init_poorders_content() {
    $(".manage-purchase-methods").unbind('click').click(function () {
        init_manage_methods();
    });
    $(".pototals-placefiltr").find('i').ubind('click').click(function () {
        change_inner_filter();
    });
    $(".addnonlistedpo").unbind('click').click(function () {
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
            $("#pageModal").find('div.modal-dialog').css('width','625px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_poedit();
        } else {
            show_error(response);
        }
    }, 'json');
}
