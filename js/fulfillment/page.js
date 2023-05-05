$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".maincontentmenu_item.active").length > 0 ) {
        start = $(".maincontentmenu_item.active").data('link');
    } else {
        start = $(".maincontentmenu_item:first").data('link');
    }
    init_page(start);
    $(".maincontentmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    })
});

function init_page(objid) {
    $(".fulfillcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
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
            init_purchase_orders();
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
    }

}