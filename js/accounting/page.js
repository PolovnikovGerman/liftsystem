$(document).ready(function(){
    // Find first item
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

    $(".accountcontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'profitordesview':
            $("#profitordesview").show();
            init_profit_orders();
            break;
        case 'profitdatesview':
            $("#profitdatesview").show();
            init_profitcalend_content();
            break;
        case 'purchaseordersview':
            $("#purchaseordersview").show();
            init_purchase_orders();
            break;
        case 'openinvoicesview':
            $("#openinvoicesview").show();
            init_paymonitor();
            break;
        case 'financebatchesview':
            $("#financebatchesview").show();
            init_batches_content();
            break;
        case 'netprofitview':
            $("#netprofitview").show();
            init_netprofit_area();
            break;
        case 'ownertaxesview':
            $("#ownertaxesview").show();
            init_ownertax_content();
            break;
        case 'expensesview':
            $("#expensesview").show();
            init_opercalc();
            break;
        case 'accreceiv':
            $("#accreceivview").show();
            init_accounts_receivable();
            break;
    }

}