function init_sitecontent(brand) {
    var start = '';
    if (brand=='BT') {
        if ($("#btcontentview").find(".submenu_item.active").length > 0 ) {
            start = $("#btcontentview").find(".submenu_item.active").data('link');
        } else {
            start = $("#btcontentview").find(".submenu_item:first").data('link');
        }
    } else {
        if ($("#sbcontentview").find(".submenu_item.active").length > 0 ) {
            start = $("#sbcontentview").find(".submenu_item.active").data('link');
        } else {
            start = $("#sbcontentview").find(".submenu_item:first").data('link');
        }
    }
    init_sitecontent_page(start, brand);
    $(".submenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        var brandid = $(this).data('brand');
        init_sitecontent_page(objid, brandid);
    });
}

function init_sitecontent_page(objid, brand) {
    $(".contentpagearea").hide();
    $(".submenu_item[data-brand='" + brand + "']").removeClass('active');
    $(".submenu_item[data-link='" + objid + "'][data-brand='" + brand + "']").addClass('active');
    switch (objid) {
        case 'bthomeview':
            init_contentpage('home','BT');
            break;
        case 'sbhomeview':
            init_contentpage('home','SB');
            break;
        case 'btcustomshappedview':
            init_contentpage('custom','BT');
            break;
        case 'sbcustomshappedview':
            init_contentpage('custom', 'SB');
            break;

    }
}

function init_contentpage(page_name, brand) {
    var params=new Array();
    params.push({name:'page_name', value: page_name});
    params.push({name:'brand', value: brand});
    var url = '/content/get_content_view';
    $.post(url, params, function(response) {
        if (response.errors=='') {
            if (page_name=='home') {
                if (brand=='SB') {
                    $("#sbhomeview").show().empty().html(response.data.content);
                } else {
                    $("#bthomeview").show().empty().html(response.data.content);
                }
            } else if (page_name=='custom') {
                if (brand=='SB') {
                    $("#sbcustomshappedview").show().empty().html(response.data.content);
                } else {
                    $("#btcustomshappedview").show().empty().html(response.data.content);
                }
                init_customshape_view(brand);
            } else if (page_name=='faq') {
                $("#faqview").show().empty().html(response.data.content);
                init_faqpage_view();
            } else if (page_name=='terms') {
                $("#termsview").show().empty().html(response.data.content);
                init_terms_view();
            } else if (page_name=='about') {
                $("#aboutusview").show().empty().html(response.data.content);
                init_aboutpage_view();
            } else if (page_name=='contactus') {
                $("#contactusview").show().empty().html(response.data.content);
                init_contactus_view();
            } else if (page_name=='extraservice') {
                $("#serviceview").show().empty().html(response.data.content);
                init_service_page();
            }
        } else {
            show_error(response);
        }
    },'json');
}
