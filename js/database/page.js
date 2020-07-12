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
    $(".dbcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'categoryview':
            $("#categoryview").show();
            init_categories_page();
            break;
        case 'itempriceview':
            $("#itempriceview").show();
            init_dbprice_view();
            break;
        case 'itemcategoryview':
            $("#itemcategoryview").show();
            init_dbcategory_view();
            // init_contentpage('itemcategory');
            break;
        case 'itemsequenceview':
            // init_contentpage('itemsequence');
            $("#itemsequenceview").show();
            init_dbsequence_view();
            break;
        case 'itemmisinfoview':
            // init_contentpage('itemmisinfo');
            $("#itemmisinfoview").show();
            init_misinfo_view();
            break;
        case 'itemprofitview':
            // init_contentpage('itemprofit');
            $("#itemprofitview").show();
            init_profit_view();
            break;
        case 'itemtemplateview':
            // init_contentpage('itemtemplates');
            $("#itemtemplateview").show();
            init_templates_view();
            break;
    }
}

function view_itemdetails(item_id, brand) {
    var params=new Array();
    params.push({name: 'item_id', value: item_id});
    params.push({name: 'brand', value: brand});
    var url = '/database/view_item';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".dbcontentarea").hide();
            $("#itemdetailsview").find('div.left_maincontent').empty().html(response.data.menu);
            $("#itemdetailsview").find('div.right_maincontent').empty().html(response.data.content);
            $("#itemdetailsview").show();
            init_itemdetails_view();
        } else {
            show_error(response);
        }
    },'json');
}