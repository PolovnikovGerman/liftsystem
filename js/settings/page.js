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
    $(".settingcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'calendarsview':
            $("#calendarsview").show();
            init_calendars_page();
            break;
        case 'btsettingsview':
            $("#btsettingsview").show();
            init_sitesettings_view('BT');
            break;
        case 'countriesview':
            $("#countriesview").show();
            init_countries();
            break;
    }
}
