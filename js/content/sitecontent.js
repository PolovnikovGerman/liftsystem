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
    $(".submenu_manage[data-brand='" + brand + "']").removeClass('active');
    $(".submenu_item[data-link='" + objid + "'][data-brand='" + brand + "']").addClass('active');
    $(".submenu_manage[data-link='" + objid + "'][data-brand='" + brand + "']").addClass('active');
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
        case 'sbfaqview':
            init_contentpage('faq','SB');
            break;
        case 'btfaqview':
            init_contentpage('faq','BT');
            break;
        case 'sbserviceview':
            init_contentpage('extraservice','SB');
            break;
        case 'btserviceview':
            init_contentpage('extraservice','BT');
            break;
        case 'btaboutusview':
            init_contentpage('about','BT');
            break;
        case 'sbaboutusview':
            init_contentpage('about','SB');
            break;
        case 'btcontactusview':
            init_contentpage('contactus','BT');
            break;
        case 'sbcontactusview':
            init_contentpage('contactus','SB');
            break;
        case 'bttermsview':
            init_contentpage('terms','BT');
            break;
        case 'sbtermsview':
            init_contentpage('terms','SB');
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
                    $(".submenu_manage[data-link='sbcustomshappedview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbcustomshappedview']").find('div.buttons').empty().html(response.data.buttons);
                } else {
                    $("#btcustomshappedview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='btcustomshappedview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='btcustomshappedview']").find('div.buttons').empty().html(response.data.buttons);
                }
                init_customshape_view(brand);
            } else if (page_name=='faq') {
                if (brand=='SB') {
                    $("#sbfaqview").show().empty().html(response.data.content);
                } else {
                    $("#btfaqview").show().empty().html(response.data.content);
                }
                init_faqpage_view(brand);
            } else if (page_name=='terms') {
                if (brand=='BT') {
                    $("#bttermsview").show().empty().html(response.data.content);
                } else {
                    $("#sbtermsview").show().empty().html(response.data.content);
                }
                init_terms_view(brand);
            } else if (page_name=='about') {
                if (brand=='BT') {
                    $("#btaboutusview").show().empty().html(response.data.content);
                } else {
                    $("#sbaboutusview").show().empty().html(response.data.content);
                }
                init_aboutpage_view(brand);
            } else if (page_name=='contactus') {
                if (brand=='BT') {
                    $("#btcontactusview").show().empty().html(response.data.content);
                } else {
                    $("#sbcontactusview").show().empty().html(response.data.content);
                }
                init_contactus_view(brand);
            } else if (page_name=='extraservice') {
                if (brand=='BT') {
                    $("#btserviceview").show().empty().html(response.data.content);
                } else {
                    $("#sbserviceview").show().empty().html(response.data.content);
                }
                init_service_page(brand);
            }
        } else {
            show_error(response);
        }
    },'json');
}
