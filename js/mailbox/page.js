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
            init_mailbox_manage();
            $(".maincontentmenu_item[data-postbox='"+postbox+"']").addClass('active');
        } else {
            show_errors(response);
            $("#loader").hide();
        }
    },'json');
}

function init_mailbox_manage() {
    // Hide folders
    $("li.hideshowfolders").unbind('click').click(function (){
        var hidefold = $("#hidefolders").val();
        if (hidefold=='1') {
            $("#hidefolders").val(0);
            $("li.hideallow").hide();
            $("li.hideshowfolders").empty().html('<span class="arrow-less"><i class="fa fa-chevron-down" aria-hidden="true"></i></span>More');
        } else {
            $("#hidefolders").val(1);
            $("li.hideallow").show();
            $("li.hideshowfolders").empty().html('<span class="arrow-less"><i class="fa fa-chevron-up" aria-hidden="true"></i></span>Less');
        }
    });
    // Show / hide custom folders
    $(".list-folder-title").find('span').unbind('click').click(function (){
        var hidecustom = $("#hidecustom").val();
        if (hidecustom=='0') {
            $(".list-newfolder").hide();
            $("ul.list-folders").hide();
            $(".list-folder-title").find('span').empty().html('Show');
            $("#hidecustom").val(1);
        } else {
            $(".list-newfolder").show();
            $("ul.list-folders").show();
            $(".list-folder-title").find('span').empty().html('Hide');
            $("#hidecustom").val(0);
        }
    });
    // Add new folders
    $(".newfolderadd").unbind('click').click(function(){
        $(".list-newfolder").empty().html('<span class="cancel-folder"><i class="fa fa-times" aria-hidden="true"></i></span>\n' +
            '        <input type="text" class="newfoldername" placeholder="" value=""/>\n' +
            '        <span class="save-newfolder"><img src="/img/mailbox/long-arrow-right-white.svg"/></span>');
        $(".list-newfolder").unbind('click');
        add_newfolder();
    });
    // Chose mail folders
    $("li.viewfoldermsg").unbind('click').click(function(){
        var folder = $(this).data('folder');
        var params = new Array();
        params.push({name: 'folder', value: folder});
        params.push({name: 'postbox', value: $("#postbox").val()});
        params.push({name: 'postsort', value: $("#postboxsort").val()});
        var url = '/mailbox/view_folder';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("li.viewfoldermsg").removeClass('active');
                $("li.customfoldermsg").removeClass('active');
                $("li.viewfoldermsg[data-folder='"+folder+"']").addClass('active');
                $(".emails-block-body").empty().html(response.data.messages);
                $(".emails-block-header").empty().html(response.data.header);
                $("#loader").hide();
                init_mailbox_manage();
            } else {
                $("#loader").hide();
                show_errors(response);
            }
        },'json')
    });
    // Chose custom folders
    $("li.customfoldermsg").unbind('click').click(function(){
        var folder = $(this).data('folder');
        var params = new Array();
        params.push({name: 'folder', value: folder});
        params.push({name: 'postbox', value: $("#postbox").val()});
        var url = '/mailbox/view_folder';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("li.viewfoldermsg").removeClass('active');
                $("li.customfoldermsg").removeClass('active');
                $("li.customfoldermsg[data-folder='"+folder+"']").addClass('active');
                $(".emails-block-body").empty().html(response.data.messages);
                $(".emails-block-header").empty().html(response.data.header);
                $("#loader").hide();
                init_mailbox_manage();
            } else {
                $("#loader").hide();
                show_errors(response);
            }
        },'json');
    });
    // Mark as read
    $(".tab-th-02").find('span.ic-blue').unbind('click').click(function (){
        var message = $(this).data('message');
        update_message_readstatus(message);
    });
    // Mark as unread
    $(".tab-th-02").find('span.ic-normal').unbind('click').click(function (){
        var message = $(this).data('message');
        update_message_readstatus(message);
    });
    // Mark as flagged
    $(".tab-th-04").find('span.ic-grey').unbind('click').click(function (){
        var message = $(this).data('message');
        update_message_flagged(message);
    });
    // Mark as unflagged
    $(".tab-th-04").find('span.ic-orange').unbind('click').click(function (){
        var message = $(this).data('message');
        update_message_flagged(message);
    });
    // Show message details
    $(".tab-th-03").unbind('click').click(function (){
        var message = $(this).data('message');
        view_message(message);
    });
}

function add_newfolder() {
    $(".cancel-folder").unbind('click').click(function (){
        $(".list-newfolder").empty().html('<div class="newfolderadd"><span class="plus-folder"><i class="fa fa-plus" aria-hidden="true"></i></span> New Folder</div>');
        init_mailbox_manage();
    });
    $(".newfoldername").keypress(function (event) {
        if (event.which == 13) {
            add_postbox_folder($(".newfoldername").val());
        }
    });
    $(".save-newfolder").unbind('click').click(function (){
        add_postbox_folder($(".newfoldername").val());
    });
}

function add_postbox_folder(folder_name) {
    var params = new Array();
    params.push({name: 'postbox', value: $("#postbox").val()});
    params.push({name: 'folder', value: folder_name});
    var url = '/mailbox/postbox_addfolder';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".list-newfolder").empty().html('<div class="newfolderadd"><span class="plus-folder"><i class="fa fa-plus" aria-hidden="true"></i></span> New Folder</div>');
            $("ul.list-folders").empty().html(response.data.content);
            init_mailbox_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_errors(response);
        }
    },'json');
}

function update_message_readstatus(message) {
    var params = new Array();
    params.push({name: 'message_id', value: message});
    params.push({name: 'postbox', value: $("#postbox").val()});
    var url = '/mailbox/update_message_readstatus';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".tab-th-02[data-message='"+message+"']").empty().html(response.data.content);
            $("tr.tab-tr[data-message='"+message+"']").removeClass('tr-read').removeClass('tr-unread');
            if (parseInt(response.data.read_state)==1) {
                $("tr.tab-tr[data-message='"+message+"']").addClass('tr-unread');
            } else {
                $("tr.tab-tr[data-message='"+message+"']").addClass('tr-read');
            }
            $("#loader").hide();
            init_mailbox_manage();
        } else {
            $("#loader").hide();
            show_errors(response);
        }
    },'json');
}

function update_message_flagged(message) {
    var params = new Array();
    params.push({name: 'message_id', value: message});
    params.push({name: 'postbox', value: $("#postbox").val()});
    var url = '/mailbox/update_message_star';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".tab-th-04[data-message='"+message+"']").empty().html(response.data.content);
            $("#loader").hide();
            init_mailbox_manage();
        } else {
            $("#loader").hide();
            show_errors(response);
        }
    },'json');
}

function view_message(message) {
    var params = new Array();
    params.push({name: 'message_id', value: message});
    params.push({name: 'postbox', value: $("#postbox").val()});
    var url = '/mailbox/view_message';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".emails-block").empty().html(response.data.content);
            // Get content
            $("#iframe").ready(function() {
                // var body = $("#iframe").contents().find("body");
                // body.html(response.data.body);
                $("#iframe").attr('srcdoc', response.data.body);
            });
            // $("iframe.box-email-body").prop('scr', );
            $("#loader").hide();
            // Init message manage
        } else {
            $("#loader").hide();
            show_errors(response);
        }
    },'json');

}