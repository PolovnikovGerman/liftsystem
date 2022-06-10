$(document).ready(function(){
    $(".dbcenter-main-button").unbind('click').click(function () {
        var url = '/database?start='+$(this).data('link');
        window.location.href=url;
    })
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
    $(".dbcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'vendorsview':
            $("#vendorsview").show();
            init_vendorpage();
            break;
        case 'legacyview':
            $("#legacyview").show();
            init_legacy_view();
            break;
        case 'itempriceview':
            $(".dbitemspage").hide();
            $("#itempriceview").show();
            init_dbprice_view();
            break;
        case 'itemcategoryview':
            $(".dbitemspage").hide();
            $("#itemcategoryview").show();
            init_dbcategory_view();
            break;
        case 'itemsequenceview':
            $(".dbitemspage").hide();
            $("#itemsequenceview").show();
            init_dbsequence_view();
            break;
        case 'itemmisinfoview':
            $(".dbitemspage").hide();
            $("#itemmisinfoview").show();
            init_misinfo_view();
            break;
        case 'itemprofitview':
            $(".dbitemspage").hide();
            $("#itemprofitview").show();
            init_profit_view();
            break;
        case 'itemtemplateview':
            $(".dbitemspage").hide();
            $("#itemtemplateview").show();
            init_templates_view();
            break;
        case 'itemexportview':
            $(".dbitemspage").hide();
            $("#itemexportview").show();
            init_export_view();
            break;
        case 'categoryview':
            $(".dbitemspage").hide();
            $("#categoryview").show();
            init_categories_page();
            break;
    }
}

function view_itemdetails(item_id, brand) {
    var params=new Array();
    params.push({name: 'item_id', value: item_id});
    // params.push({name: 'brand', value: brand});
    var url = '/database/view_item';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".dbitemspage").hide();
            // $(".dbcontentarea").hide();
            // $("#itemdetailsview").find('div.left_maincontent').empty().html(response.data.menu);
            $("#itemdetailsview").find('div.right_maincontent').empty().html(response.data.content);
            $("#itemdetailsview").show();
            init_itemdetails_view();
        } else {
            show_error(response);
        }
    },'json');
}