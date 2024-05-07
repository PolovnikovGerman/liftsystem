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
    });
});

function init_page(objid) {
    $(".marketingcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'searchestimeview':
            $("#searchestimeview").show();
            init_searchtime_content();
            break;
        case 'searcheswordview':
            $("#searcheswordview").show();
            show_keywords_result();
            break;
        case 'searchesipadrview':
            $("#searchesipadrview").show();
            show_ipaddress_result();
            break
        case 'signupview':
            $("#signupview").show();
            init_signup();
            break;
        case 'couponsview':
            $("#couponsview").show();
            init_coupon_view();
            break;
    }

}