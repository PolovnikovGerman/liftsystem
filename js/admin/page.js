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
    $(".admincontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'usersview':
            $("#usersview").show();
            init_users();
            break;
        case 'parseremailsview':
            $("#parseremailsview").show();
            init_parsedemails_content();
            break;
        case 'artalertsview':
            $("#artalertsview").show();
            init_taskalertsys();
            break;
        case 'calendarsview':
            $("#calendarsview").show();
            init_calendars_page();
            break;
    }
}
