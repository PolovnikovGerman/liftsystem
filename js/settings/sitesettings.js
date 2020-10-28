function init_sitesettings_view(brand){
    // Find first item
    var start = '';
    if (brand=='BT') {
        if ($("#btsettingsview").find(".submenu_item.active").length > 0 ) {
            start = $("#btsettingsview").find(".submenu_item.active").data('link');
        } else {
            start = $("#btsettingsview").find(".submenu_item:first").data('link');
        }
        console.log('Start '+start);
    } else {

    }
    init_sitesetting_page(start, brand);
    $(".submenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        var brandid = $(this).data('brand');
        init_sitesetting_page(objid, brandid);
    });
}

function init_sitesetting_page(objid, brand) {
    $(".sitesettingcontentarea").hide();
    $(".submenu_item[data-brand='" + brand + "']").removeClass('active');
    $(".submenu_item[data-link='" + objid + "'][data-brand='" + brand + "']").addClass('active');
    switch (objid) {
        case 'btshippingview':
            $("#btshippingview").show();
            init_shiipings_page(brand);
            break;
        case 'btnotificationsview':
            $("#btnotificationsview").show();
            init_notifications_page(brand);
            break;
        case 'btrushoptionsview':
            $("#btrushoptionsview").show();
            init_rushoptions_page(brand);
            break;
    }
}