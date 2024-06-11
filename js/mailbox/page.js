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