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
/*        case 'requestlist':
            $("#requestlist").show();
            init_proofdata();
            break;
        case 'taskview':
            $("#taskview").show();
            init_tasks_management();
            init_tasks_page();
            break; */
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
            //     $("#faq").empty().html(response.data.content);
            //     init_faqpage_view();
            // } else if (page_name=='terms') {
            //     $("#terms").empty().html(response.data.content);
            //     init_terms_view();
            // } else if (page_name=='about') {
            //     $("#aboutus").empty().html(response.data.content);
            //     init_aboutpage_view();
            // } else if (page_name=='contactus') {
            //     $("#contactus").empty().html(response.data.content);
            //     init_contactus_view();
            // } else if (page_name=='categories') {
            //     $("#categories").empty().html(response.data.content);
            //     init_categories_page();
            // } else if (page_name=='extraservice') {
            //     $("#service").empty().html(response.data.content);
            //     init_service_page();
            }
        } else {
            show_error(response);
        }
    },'json');
}
