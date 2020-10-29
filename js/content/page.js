$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".maincontentmenu_item.active").length > 0 ) {
        start = $(".maincontentmenu_item.active").data('link');
    } else {
        start = $(".maincontentmenu_item:first").data('link');
    }
    init_page(start);
    $(".maincontentmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    });
});

function init_page(objid) {
    $(".artcontentarea").hide();
    $(".maincontentmenu_item").removeClass('active');
    $(".maincontentmenu_item[data-link='"+objid+"']").addClass('active');
    $(".sitecontentpagearea").hide();
    switch (objid) {
        case 'btcontentview':
            break;
        case 'sbcontentview':
            $("#sbcontentview").show();
            init_sitecontent('SB');
            break;
    }
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

