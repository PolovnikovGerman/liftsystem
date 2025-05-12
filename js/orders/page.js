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
    $(".orderscontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='"+objid+"']").addClass('active');
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