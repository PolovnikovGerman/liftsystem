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
    $(".leadscontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'leadsview':
            $("#leadsview").show();
            init_leadsview();
            break;
        case 'itemslistview':
            $("#itemslistview").show();
            init_leaditems();
            break;
        case 'onlinequotesview':
            $("#onlinequotesview").show();
            init_quotes();
            break;
        case 'proofrequestsview' :
            $("#proofrequestsview").show();
            init_proofdata();
            break;
        case 'questionsview':
            $("#questionsview").show();
            init_questions();
            break;
        case 'checkoutattemptsview':
            $("#checkoutattemptsview").show();
            init_attempts();
            break;
        case 'customsbform':
            $("#customsbformview").show();
            init_customforms();
            break;
        case 'leadquotes':
            $("#leadquotesview").show();
            init_leadquotes();
            break;
        case 'customorders':
            $("#customorders").show();
            init_leadcustomorders();
            break;
    }
}

function replyquestmail(mail) {
    var mailtourl = "mailto:" + mail;
    location.href = mailtourl;
    return false;
}
