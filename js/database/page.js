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
        case 'requestlist':
            $("#requestlist").show();
            init_proofdata();
            break;
        case 'taskview':
            $("#taskview").show();
            init_tasks_management();
            init_tasks_page();
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
            // } else if (page_name=='extraservice') {
            //     $("#service").empty().html(response.data.content);
            //     init_service_page();
            }
        } else {
            show_error(response);
        }
    },'json');
}


// $("#categoryview").show().empty().html();