$(document).ready(function(){
    // Find first item
    var start = $(".maincontentmenu_item:first").data('link');
    init_page(start);
    $(".maincontentmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    })
});

function init_page(objid) {
    $(".settingcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'shippingview':
            $("#shippingview").show();
            init_shiipings_page();
            break;
        case 'calendarsview':
            $("#calendarsview").show();
            init_calendars_page();
            break;
        case 'notificationsview':
            $("#notificationsview").show();
            init_notifications_page();
            break;
        case 'rushoptionsview':
            $("#rushoptionsview").show();
            init_rushoptions_page();
            break;

    }
}
