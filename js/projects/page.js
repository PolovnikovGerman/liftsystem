$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".contentsubmenu_item.active").length > 0 ) {
        start = $(".contentsubmenu_item.active").data('link');
    } else {
        start = $(".contentsubmenu_item:first").data('link');
    }
    init_page(start);
    $(".contentsubmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    })
});

function init_page(objid) {
    console.log(objid);
    $(".projcontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'projectsview':
            $("#projectsview").show();
            break;
    }
}