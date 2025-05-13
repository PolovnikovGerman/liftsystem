$(document).ready(function(){
    // Find first item
    google.charts.load('current', {packages: ['corechart', 'line']});
    var start = '';
    if ($(".contentsubmenu_item.active").length > 0 ) {
        start = $(".contentsubmenu_item.active").data('link');
    } else {
        start = $(".contentsubmenu_item:first").data('link');
    }
    init_page(start);
    $(".contentsubmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    })
});
function init_page(objid) {
    $(".analyticcontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='" + objid + "']").addClass('active');
    switch (objid) {
         case 'reportsalestypeview':
             $("#reportsalestypeview").show();
             salestype_report_init();
             break;
        case 'reportitemsoldyearview':
            $("#reportitemsoldyearview").show();
            itemsales_reportinit();
            break;
        case 'reportitemsoldmonthview':
            $("#reportitemsoldmonthview").show();
            itemmonth_reportinit();
            break;
        case 'checkoutreportview':
            $("#checkoutreportview").show();
            init_ordersreports();
    }
}

// Show item details - Item Sold (Yr) Item Sold (Mo)
function show_vendoritem_prices(item) {
    var url="/analytics/vendoritem_prices";
    $.post(url,{'item': item}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html('Sales Month Details');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','685px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
        } else {
            show_error(response);
        }
    },'json');
}