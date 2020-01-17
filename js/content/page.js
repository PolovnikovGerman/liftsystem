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
    $(".artcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    $(".contentpagearea").hide();
    switch (objid) {
        case 'homeview':
            init_contentpage('home');
            break;
        case 'customshappedview':
            init_contentpage('custom');
            break;
        case 'serviceview':
            init_contentpage('extraservice');
            break;
        case 'aboutusview':
            init_contentpage('about');
            break;
        case 'faqview':
            init_contentpage('faq');
            break;
        case 'contactusview':
            init_contentpage('contactus');
            break;
        case 'termsview':
            init_contentpage('terms');
            break;
    }
}

function init_contentpage(page_name) {
    var params=new Array();
    params.push({name:'page_name', value: page_name});
    var url = '/content/get_content_view';
    $.post(url, params, function(response) {
        if (response.errors=='') {
            if (page_name=='home') {
                $("#homeview").show().empty().html(response.data.content);
            } else if (page_name=='custom') {
                 $("#customshappedview").show().empty().html(response.data.content);
                 init_customshape_view();
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
            // } else if (page_name=='categories') {
            //     $("#categories").empty().html(response.data.content);
            //     init_categories_page();
            } else if (page_name=='extraservice') {
                 $("#serviceview").show().empty().html(response.data.content);
                 init_service_page();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function window_alignment() {
    if ($(".container").length > 0 && $(".content_window").length > 0) {
        var contentheigth = parseInt($(".container").css('height'));
        var wincontent = parseInt($(".content_window").css('height'))+125;
        if (wincontent > contentheigth ) {
            $(".container").css('height',wincontent+'px');
        } else {
            $(".content_window").css('height',(contentheigth-125)+'px');
        }
    }
}