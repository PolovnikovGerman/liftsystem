$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".headmenuitem.active").length > 0 ) {
        start = $(".headmenuitem.active").data('lnk');
    } else {
        start = $(".headmenuitem:first").data('lnk');
    }
    init_page(start);
    $(".headmenuitem").unbind('click').click(function () {
        var objid = $(this).data('lnk');
        init_page(objid);
    })
});
function init_page(objid) {
    $(".accountcontentarea").hide();
    $(".headmenuitem").removeClass('active');
    $(".headmenuitem[data-lnk='"+objid+"']").addClass('active');
    switch (objid) {
        case 'pototalsview':
            $("#pototalsview").show();
            init_purchase_orders();
            break;
    }

}