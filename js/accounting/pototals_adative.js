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
        } else {
            show_error(response);
        }
    },'json');
}