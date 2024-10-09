$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".ordercontentmenu").find('div.dropdown-item.active').length > 0) {
    // if ($(".maincontentmenu_item.active").length > 0 ) {
        start = $(".ordercontentmenu").find('a.dropdown-item.active').data('link');
    } else {
        start = $(".ordercontentmenu").find('a.dropdown-item:first').data('link');
    }
    init_page(start);

    $(".ordercontentmenu").find('a.dropdown-item').unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    });
});

function init_page(objid) {
    $(".orderscontentarea").hide();
    $("ul.tb-nav-tabs").find("li.whitetab").hide();
    $(".ordercontentmenu").find('a.dropdown-item').removeClass('active');
    $(".ordercontentmenu").find("a.dropdown-item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'ordersview':
            $("#ordersview").show();
            $("#ordersviewtab").css('display','inline-block');
            init_ordersviewdata();
            break;
        case 'orderlistsview':
            $("#orderlistsview").show();
            $("#orderlistsviewtab").css('display','inline-block');
            init_leadorderlist();
            break;
        case 'onlineordersview':
            $("#onlineordersview").show();
            $("#onlineordersviewtab").css('display','inline-block');
            init_orderview();
            break;
    }
}