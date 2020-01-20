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
    $(".dbcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'categoryview':
            init_contentpage('categories');
            break;
        case 'itempriceview':
            init_contentpage('itemprice');
            break;
        case 'itemcategoryview':
            init_contentpage('itemcategory');
            break;
        case 'itemsequenceview':
            init_contentpage('itemsequence');
            break;
        case 'itemmisinfoview':
            init_contentpage('itemmisinfo');
            break;
        case 'itemprofitview':
            init_contentpage('itemprofit');
            break;
        case 'itemtemplateview':
            init_contentpage('itemtemplates');
            break;
    }
}

function init_contentpage(page_name) {
    var params=new Array();
    params.push({name:'page_name', value: page_name});
    var url = '/database/get_content_view';
    $.post(url, params, function(response) {
        if (response.errors=='') {
            if (page_name=='categories') {
                $("#categoryview").show().empty().html(response.data.content);
                init_categories_page();
            } else if (page_name=='itemprice') {
                $("#itempriceview").show().empty().html(response.data.content);
                init_dbprice_view();
            } else if (page_name=='itemcategory') {
                $("#itemcategoryview").show().empty().html(response.data.content);
                init_dbcategory_view();
            } else if (page_name=='itemsequence') {
                $("#itemsequenceview").show().empty().html(response.data.content);
                init_dbsequence_view();
            } else if (page_name=='itemmisinfo') {
                $("#itemmisinfoview").show().empty().html(response.data.content);
                init_misinfo_view();
            } else if (page_name=='itemprofit') {
                $("#itemprofitview").show().empty().html(response.data.content);
                init_profit_view();
            } else if (page_name=='itemtemplates') {
                $("#itemtemplateview").show().empty().html(response.data.content);
                init_profit_view();
                init_templates_view();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function view_itemdetails(item_id) {
    var params=new Array();
    params.push({name: 'item_id', value: item_id});
    var url = '/database/view_item';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".dbcontentarea").hide();
            $("#itemdetailsview").show().empty().html(response.data.content);
            // init_categories_page();
        } else {
            show_error(response);
        }
    },'json');
}