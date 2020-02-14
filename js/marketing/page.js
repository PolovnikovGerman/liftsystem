$(document).ready(function(){
    // Find first item
    var start = $(".maincontentmenu_item:first").data('link');
    init_page(start);
    $(".maincontentmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    });
});

function init_page(objid) {
    $(".marketingcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'searchestimeview':
            $("#searchestimeview").show();
            init_searchtime_content();
            break;
        case 'searcheswordview':
            $("#searcheswordview").show();
            init_searchkeyword_content();
            break;
        case 'searchesipadrview':
            $("#searchesipadrview").show();
            init_searchipaddres_content();
            break
        case 'signupview':
            $("#signupview").show();
            init_signup();
            break;
    }

}