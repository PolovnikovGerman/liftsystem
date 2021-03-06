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
    $(".orderscontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'ordersview':
            $("#ordersview").show();
            init_ordersviewdata();
            break;
        case 'orderlistsview':
            $("#orderlistsview").show();
            init_leadorderlist();
            break;
        case 'onlineordersview':
            $("#onlineordersview").show();
            init_orderview();
            break;
    }
}