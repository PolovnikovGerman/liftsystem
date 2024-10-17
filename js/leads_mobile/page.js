$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".leadscontentmenu").find('div.dropdown-item.active').length > 0) {
        start = $(".leadscontentmenu").find('a.dropdown-item.active').data('link');
    } else {
        start = $(".leadscontentmenu").find('a.dropdown-item:first').data('link');
    }
    init_page(start);

    $(".leadscontentmenu").find('a.dropdown-item').unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    });
});

function init_page(objid) {
    $(".leadscontentarea").hide();
    $("ul.tb-nav-tabs").find("li.whitetab").hide();
    $(".leadscontentmenu").find('a.dropdown-item').removeClass('active');
    $(".leadscontentmenu").find("a.dropdown-item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'leadsview':
            $("#leadsview").show();
            $("#leadsviewtab").css('display','inline-block');
            break;
        case 'itemslistview':
            $("#itemslistview").show();
            $("#itemslistviewtab").css('display','inline-block');
            break;
        case 'onlinequotesview':
            $("#onlinequotesview").show();
            $("#onlinequotesviewtab").css('display','inline-block');
            break;
        case 'proofrequestsview':
            $("#proofrequestsview").show();
            $("#proofrequestsviewtab").css('display','inline-block');
            break;
        case 'questionsview':
            $("#questionsview").show();
            $("#questionsviewtab").css('display','inline-block');
            break;
        case 'customsbformview':
            $("#customsbformview").show();
            $("#customsbformviewtab").css('display','inline-block');
            break;
        case 'checkoutattemptsview':
            $("#checkoutattemptsview").show();
            $("#checkoutattemptsviewtab").css('display','inline-block');
            break;
        case 'customorders':
            $("#customorders").show();
            $("#customorderstab").css('display','inline-block');
            break;
    }
}