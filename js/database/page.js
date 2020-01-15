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
            }
        } else {
            show_error(response);
        }
    },'json');
}


// $("#categoryview").show().empty().html();