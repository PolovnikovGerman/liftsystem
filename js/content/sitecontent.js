function init_sitecontent(brand) {
    var start = '';
    if (brand=='BT') {
        if ($("#btcontentview").find(".submenu_item.active").length > 0 ) {
            start = $("#btcontentview").find(".submenu_item.active").data('link');
        } else {
            start = $("#btcontentview").find(".submenu_item:first").data('link');
        }
    } else if (brand=='SB') {
        if ($("#sbpagesview").find(".submenu_item.active").length > 0 ) {
            start = $("#sbpagesview").find(".submenu_item.active").data('link');
        } else {
            start = $("#sbpagesview").find(".submenu_item:first").data('link');
        }
    } else if (brand=='SR') {
        if ($("#srpagesview").find(".submenu_item.active").length > 0 ) {
            start = $("#srpagesview").find(".submenu_item.active").data('link');
        } else {
            start = $("#srpagesview").find(".submenu_item:first").data('link');
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
            leftmenu_alignment();
            break;
        case 'sbhomeview':
            init_contentpage('home','SB');
            leftmenu_alignment();
            break;
        case 'btcustomshappedview':
            init_contentpage('custom','BT');
            leftmenu_alignment();
            break;
        case 'sbcustomshappedview':
            init_contentpage('custom', 'SB');
            leftmenu_alignment();
            break;
        case 'sbfaqview':
            init_contentpage('faq','SB');
            leftmenu_alignment();
            break;
        case 'btfaqview':
            init_contentpage('faq','BT');
            leftmenu_alignment();
            break;
        case 'sbserviceview':
            init_contentpage('extraservice','SB');
            leftmenu_alignment();
            break;
        case 'btserviceview':
            init_contentpage('extraservice','BT');
            leftmenu_alignment();
            break;
        case 'btaboutusview':
            init_contentpage('about','BT');
            leftmenu_alignment();
            break;
        case 'sbaboutusview':
            init_contentpage('about','SB');
            leftmenu_alignment();
            break;
        case 'btcontactusview':
            init_contentpage('contactus','BT');
            leftmenu_alignment();
            break;
        case 'sbcontactusview':
            init_contentpage('contactus','SB');
            leftmenu_alignment();
            break;
        case 'bttermsview':
            init_contentpage('terms','BT');
            leftmenu_alignment();
            break;
        case 'sbtermsview':
            init_contentpage('terms','SB');
            leftmenu_alignment();
            break;
        case 'srhomeview':
            init_contentpage('home','SR');
            leftmenu_alignment();
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
                    $(".submenu_manage[data-link='sbhomeview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbhomeview']").find('div.buttons').empty().html(response.data.buttons);
                } else if(brand=='BT') {
                    $("#bthomeview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='bthomeview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='bthomeview']").find('div.buttons').empty().html(response.data.buttons);
                } else if (brand=='SR') {
                    $("#srhomeview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='srhomeview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='srhomeview']").find('div.buttons').empty().html(response.data.buttons);
                }
                init_homepage_view(brand);
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
                    $(".submenu_manage[data-link='sbfaqview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbfaqview']").find('div.buttons').empty().html(response.data.buttons);
                } else {
                    $("#btfaqview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='btfaqview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='btfaqview']").find('div.buttons').empty().html(response.data.buttons);
                }
                init_faqpage_view(brand);
            } else if (page_name=='terms') {
                if (brand=='BT') {
                    $("#bttermsview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='bttermsview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='bttermsview']").find('div.buttons').empty().html(response.data.buttons);
                } else if (brand=='SB') {
                    $("#sbtermsview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='sbtermsview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbtermsview']").find('div.buttons').empty().html(response.data.buttons);
                }
                init_terms_view(brand);
            } else if (page_name=='about') {
                if (brand=='BT') {
                    $("#btaboutusview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='btaboutusview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='btaboutusview']").find('div.buttons').empty().html(response.data.buttons);
                } else if (brand=='SB') {
                    $("#sbaboutusview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='sbaboutusview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbaboutusview']").find('div.buttons').empty().html(response.data.buttons);
                }
                init_aboutpage_view(brand);
            } else if (page_name=='contactus') {
                if (brand=='BT') {
                    $("#btcontactusview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='btcontactusview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='btcontactusview']").find('div.buttons').empty().html(response.data.buttons);
                } else if (brand=='SB') {
                    $("#sbcontactusview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='sbcontactusview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbcontactusview']").find('div.buttons').empty().html(response.data.buttons);
                }
                init_contactus_view(brand);
            } else if (page_name=='extraservice') {
                if (brand=='BT') {
                    $("#btserviceview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='btserviceview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='btserviceview']").find('div.buttons').empty().html(response.data.buttons);
                } else {
                    $("#sbserviceview").show().empty().html(response.data.content);
                    $(".submenu_manage[data-link='sbserviceview']").find('div.submenu_label').empty().html('View Mode');
                    $(".submenu_manage[data-link='sbserviceview']").find('div.buttons').empty().html(response.data.buttons);

                }
                init_service_page(brand);
            }
        } else {
            show_error(response);
        }
    },'json');
}

function init_homepage_view(brand) {
    $(".edit_button[data-link='homeview']").unbind('click').click(function () {
        init_homepage_edit(brand);
    });
    // Open Image
    $(".homepage_imagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
}

function init_homepage_edit(brand) {
    var url = "/content/edit_homecontent";
    var params = new Array();
    params.push({name:'brand', value: brand});
    $.post(url, params, function (response) {
        if (response.errors=='') {
            if (brand=='BT') {
                $("#btcustomshappedview").empty().html(response.data.content);
                $(".submenu_manage[data-link='btcustomshappedview']").find('div.submenu_label').empty().html('Edit Mode');
                $(".submenu_manage[data-link='btcustomshappedview']").find('div.buttons').empty().html(response.data.buttons);
            } else if (brand=='SB'){
                $("#sbhomeview").empty().html(response.data.content);
                $(".submenu_manage[data-link='sbhomeview']").find('div.submenu_label').empty().html('Edit Mode');
                $(".submenu_manage[data-link='sbhomeview']").find('div.buttons').empty().html(response.data.buttons);
                $("input.metatitle").focus();
                $("input.metatitle").select()
                init_sbhomepage_editcontent();
            } else if (brand=='SR') {
                $("#srhomeview").show().empty().html(response.data.content);
                $(".submenu_manage[data-link='srhomeview']").find('div.submenu_label').empty().html('Edit Mode');
                $(".submenu_manage[data-link='srhomeview']").find('div.buttons').empty().html(response.data.buttons);
                init_srhomepage_editcontent();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function init_sbhomepage_editcontent() {
    $(".cancel_button[data-page='home']").unbind('click').click(function () {
        init_contentpage('home', 'SB');
    });
    $(".save_button[data-page='home']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name:'brand', value: 'SB'});
        var url="/content/save_homecontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('home', 'SB');
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_homepageparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_homepageparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}