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
    $(".fulfillcontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'vendorsview':
            $("#vendorsview").show();
            init_vendorpage();
            document.title = 'Lift: Vendors';
            break;
        case 'fullfilstatusview':
            $("#fullfilstatusview").show();
            init_statuspage();
            document.title = 'Lift: Status';
            break;
        case 'pototalsview':
            $("#pototalsview").show();
            // init_purchase_orders();
            // Temporary
            // $(".pooverdataview").hide();
            // $(".pohistorydataview").show();
            // init_pohistory();
            init_pototals();
            document.title = 'Lift: PO Totals';
            break;
        case 'printshopinventview':
            $("#printshopinventview").show();
            // init_inventory_content();
            init_master_inventory();
            document.title = 'Lift: Inventory';
            break;
        case 'invneedlistview':
            $("#invneedlistview").show();
            init_needinvlist_content('fulfillment');
            document.title = 'Lift: Inventory Need List';
            break;
        case 'salesrepinventview':
            $("#salesrepinventview").show();
            init_invsalesreport_content();
            document.title = 'Lift: Sales Rep Inventory';
            break;
        case 'printshopreportview':
            $("#printshopreportview").show();
            init_orderreport_content();
            document.title = 'Lift: Print Shop Report';
            break;
        case 'printscheduleview':
            $("#printschedulerview").show();
            init_printscheduler_content();
            document.title = 'Lift: Print Shop Report';
            break;
        case 'btitems':
            $("#btitemsview").show();
            init_btitemslist_view();
            document.title = 'Lift: BT items DB';
            break;
        case 'sritems':
            $("#sritemsview").show();
            init_relievers_items();
            break;
    }

}