function init_legacy_view(){
    // Find first item
    var start = '';
    if ($(".summenu_item.active").length > 0 ) {
        start = $(".summenu_item.active").data('link');
    } else {
        start = $(".summenu_item:first").data('link');
    }
    init_legacy_page(start);
    $(".summenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_legacy_page(objid);
    })
}

function init_legacy_page(objid) {
    $(".dbitemspage").hide();
    $(".summenu_item").removeClass('active');
    $(".summenu_item[data-link='" + objid + "']").addClass('active');
    switch (objid) {
        case 'itempriceview':
            $("#itempriceview").show();
            init_dbprice_view();
            break;
        case 'itemcategoryview':
            $("#itemcategoryview").show();
            init_dbcategory_view();
            break;
        case 'itemsequenceview':
            $("#itemsequenceview").show();
            init_dbsequence_view();
            break;
        case 'itemmisinfoview':
            $("#itemmisinfoview").show();
            init_misinfo_view();
            break;
        case 'itemprofitview':
            $("#itemprofitview").show();
            init_profit_view();
            break;
        case 'itemtemplateview':
            $("#itemtemplateview").show();
            init_templates_view();
            break;
        case 'itemexportview':
            $("#itemexportview").show();
            init_export_view();
            break;
        case 'categoryview':
            $("#categoryview").show();
            init_categories_page();
            break;
    }
}