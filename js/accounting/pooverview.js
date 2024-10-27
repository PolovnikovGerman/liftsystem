function init_pototals() {
    if ($(".pooverdataview").css('display', 'block')) {
        init_pooverview();
    } else {
        // init history
    }
}

function init_pooverview() {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    var url = "/purchaseorders/pooverview";
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pooverviewdomestictablearea").empty().html(response.data.otherview)
            $(".pooverviewcustomtablearea").empty().html(response.data.customview);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}
