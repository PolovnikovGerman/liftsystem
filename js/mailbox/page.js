$(document).ready(function () {
    // Search first
    var start = '';
    start = $(".maincontentmenu_item:first").data('postbox');
    if (start) {
        init_postbox(start);
    }
})

function init_postbox(postbox) {
    var params = new Array();
    params.push({name: 'postbox', value: postbox});
    var url = '/mailbox/postbox_details';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".maincontent_view").empty().html(response.data.content);
            $("#loader").hide();
        } else {
            show_errors(response);
            $("#loader").hide();
        }
    },'json');
}