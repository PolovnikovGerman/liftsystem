$(document).ready(function (){
    var postbox = $("#currentpostbox").val();
    if (postbox!=='') {
        $(".emailsmenu-tab[data-postbox='"+postbox+"']").addClass('active');
        init_mailbox_content();
    }
});

function init_mailbox_content() {
    var postbox = $("#currentpostbox").val();
    var params = new Array();
    params.push({name: 'postbox', value: postbox});
    var url = '/mailbox/postbox_details';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".eml-mainbtns").empty().html(response.data.folders_main);
            $(".eml-folders").empty().html(response.data.folders_other);
            $(".emlsblock-name").empty().html(response.data.folder_name);
            $("#currentpostfolder").val(response.data.folder);
            $("#eml-table-messages").empty().html(response.data.messages);
            $("#loader").hide();
            init_messages_management();
            init_postbox_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}
function init_postbox_content() {
    // Change main folder
    $(".mainbtn").unbind('click').click(function (){
        var folder = $(this).data('folder');
        $(".mainbtn").removeClass('active');
        $(".btn-folder").removeClass('active');
        $(".mainbtn[data-folder='"+folder+"']").addClass('active');
        var params = new Array();
        params.push({name: 'folder', value: folder});
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'postsort', value: $("#postboxsort").val()});
        var url = '/mailbox/view_folder';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".emlsblock-name").empty().html(response.data.folder_name);
                $("#currentpostfolder").val(response.data.folder);
                $("#eml-table-messages").empty().html(response.data.messages);
                $("input[name='allemls']").prop('checked',false);
                $("#loader").hide();
                init_messages_management();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Change user folder
    $(".btn-folder").unbind('click').click(function (){
        var folder = $(this).data('folder');
        $(".mainbtn").removeClass('active');
        $(".btn-folder").removeClass('active');
        $(".mainbtn[data-folder='"+folder+"']").addClass('active');
        var params = new Array();
        params.push({name: 'folder', value: folder});
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'postsort', value: $("#postboxsort").val()});
        var url = '/mailbox/view_folder';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".emlsblock-name").empty().html(response.data.folder_name);
                $("#currentpostfolder").val(response.data.folder);
                $("#eml-table-messages").empty().html(response.data.messages);
                $("input[name='allemls']").prop('checked',false);
                $("#loader").hide();
                init_messages_management();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Add folder
    $(".btn-newfolder").unbind('click').click(function(){
        $(".btn-newfolder").empty().html('<span class="cancel-folder"><i class="fa fa-times" aria-hidden="true"></i></span>\n' +
            '        <input type="text" class="newfoldername" placeholder="" value=""/>\n' +
            '        <span class="save-newfolder"><img src="/img/postbox/long-arrow-right-white.svg"/></span>');
        $(".btn-newfolder").unbind('click');
        add_newfolder();
    });
    $("input[name='allemls']").unbind('change').change(function (){
        var chkall = 0;
        if ($("input[name='allemls']").prop('checked')==true) {
            chkall = 1;
        }
        if (chkall==0) {
            $("input[name='selectemail']").prop('checked',false);
        } else {
            $("input[name='selectemail']").prop('checked',true);
        }
    });
    // Delete messages
    $(".deletemsglist").unbind('click').click(function(){
        var chklist = $("input[name='selectemail']:checked").length;
        if (parseInt(chklist)>0) {
            var msgs = new Array();
            $("input[name='selectemail']:checked").each(function (e){
                msgs.push($(this).data('message'));
            });
            var params = new Array();
            params.push({name: 'messages', value: msgs});
            params.push({name: 'folder', value: $("#currentpostfolder").val()});
            params.push({name: 'postbox', value: $("#currentpostbox").val()});
            params.push({name: 'postsort', value: $("#postboxsort").val()});
            var url = '/mailbox/messages_delete';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $(".eml-mainbtns").empty().html(response.data.folders_main);
                    $(".eml-folders").empty().html(response.data.folders_other);
                    $("#eml-table-messages").empty().html(response.data.messages);
                    init_postbox_content();
                    init_messages_management();
                    $("#loader").hide();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    // Move to other folder
    $(".movemsglist").unbind('click').click(function (){
        var chklist = $("input[name='selectemail']:checked").length;
        if (parseInt(chklist)>0) {
            var msgs = new Array();
            $("input[name='selectemail']:checked").each(function (e){
                msgs.push($(this).data('message'));
            });
            var params = new Array();
            params.push({name: 'postbox', value: $("#currentpostbox").val()});
            params.push({name: 'folder', value: $("#currentpostfolder").val()});
            var url = '/mailbox/message_move_folders';
            $.post(url, params, function (response) {
                $("#msglistfolders").empty().html(response.data.content);
                $("#msglistfolders").show();
                init_messages_move(msgs);
            },'json');
        }
    })
    // More
    $(".emlmenu-more").unbind('click').click(function (){
        if ($(".eml-moremenu").css('display')=='none') {
            $(".eml-moremenu").show();
        } else {
            $(".eml-moremenu").hide();
        }
    });
    // More - Unread option
    $("#msglistunread").unbind('click').click(function (){
        var chklist = $("input[name='selectemail']:checked").length;
        if (parseInt(chklist)>0) {
            var msgs = new Array();
            $("input[name='selectemail']:checked").each(function (e){
                msgs.push($(this).data('message'));
            });
            var params = new Array();
            params.push({name: 'postbox', value: $("#currentpostbox").val()});
            params.push({name: 'folder', value: $("#currentpostfolder").val()});
            params.push({name: 'messages', value: msgs});
            params.push({name: 'postsort', value: $("#postboxsort").val()});
            params.push({name: 'readstatus', value: 0});
            var url = '/mailbox/messages_read_status';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $("#eml-table-messages").empty().html(response.data.messages);
                    var folders = response.data.folders;
                    for(index = 0; index < folders.length; ++index) {
                        $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                    }
                    $(".eml-moremenu").hide();
                    $("#loader").hide();
                    init_postbox_content();
                    init_messages_management();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        } else {
            $(".eml-moremenu").hide();
        }
    })
    $("#msglistarchive").unbind('click').click(function (){
        var chklist = $("input[name='selectemail']:checked").length;
        if (parseInt(chklist)>0) {
            var msgs = new Array();
            $("input[name='selectemail']:checked").each(function (e){
                msgs.push($(this).data('message'));
            });
            var params = new Array();
            params.push({name: 'postbox', value: $("#currentpostbox").val()});
            params.push({name: 'folder', value: $("#currentpostfolder").val()});
            params.push({name: 'messages', value: msgs});
            params.push({name: 'postsort', value: $("#postboxsort").val()});
            var url = '/mailbox/messages_archive';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $("#eml-table-messages").empty().html(response.data.messages);
                    var folders = response.data.folders;
                    for(index = 0; index < folders.length; ++index) {
                        $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                    }
                    $(".eml-moremenu").hide();
                    $("#loader").hide();
                    init_postbox_content();
                    init_messages_management();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        } else {
            $(".eml-moremenu").hide();
        }
    });
}

function init_messages_move(messages) {
    $(".emlfolders-menu-close").unbind('click').click(function (){
        $("#msglistfolders").hide();
    });
    $(".efm-item").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'message_id', value: messages});
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'folder', value: $("#currentpostfolder").val()});
        params.push({name: 'postsort', value: $("#postboxsort").val()});
        params.push({name: 'newfolder', value: $(this).data('folder')});
        params.push({name: 'msgtype', value: 'multi'});
        var url = '/mailbox/message_move';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#msglistfolders").hide();
                $("#eml-table-messages").empty().html(response.data.messages);
                var folders = response.data.folders;
                for(index = 0; index < folders.length; ++index) {
                    $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                }
                init_postbox_content();
                init_messages_management();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })

}
function add_newfolder() {
    $(".cancel-folder").unbind('click').click(function (){
        $(".btn-newfolder").empty().html('+ New Folder');
        init_postbox_content();
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
    params.push({name: 'postbox', value: $("#currentpostbox").val()});
    params.push({name: 'folder', value: folder_name});
    var url = '/mailbox/postbox_addfolder';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".list-newfolder").empty().html('+ New Folder');
            $(".eml-folders").empty().html(response.data.content);
            init_postbox_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}


function init_messages_management() {
    $(".td-namesender").unbind('click').click(function (){
        var message = $(this).data('message');
        view_message(message);
    });
    $(".td-email").unbind('click').click(function (){
        var message = $(this).data('message');
        view_message(message);
    });
    $(".td-time").unbind('click').click(function (){
        var message = $(this).data('message');
        view_message(message);
    });
    $(".td-favorites").unbind('click').click(function (){
        var message = $(this).data('message');
        flagmessage(message);
    });
    $(".td-folder").unbind('click').click(function (){
        var message = $(this).data('message');
        var params = new Array();
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'folder', value: $("#currentpostfolder").val()});
        var url = '/mailbox/message_move_folders';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".emlfolders-menu[data-message='"+message+"']").empty().html(response.data.content).show();
                init_message_move(message);
                // return true;
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_message_move(message) {
    $(".emlfolders-menu-close").unbind('click').click(function (){
        $(".emlfolders-menu[data-message='"+message+"']").hide();
    });
    $(".efm-item").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'message_id', value: message});
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'folder', value: $("#currentpostfolder").val()});
        params.push({name: 'postsort', value: $("#postboxsort").val()});
        params.push({name: 'newfolder', value: $(this).data('folder')});
        params.push({name: 'msgtype', value: 'once'});
        var url = '/mailbox/message_move';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                // $(".emlfolders-menu").hide();
                $("#eml-table-messages").empty().html(response.data.messages);
                var folders = response.data.folders;
                for(index = 0; index < folders.length; ++index) {
                    $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                }
                init_postbox_content();
                init_messages_management();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })
}

function view_message(message) {
    var params = new Array();
    params.push({name: 'message_id', value: message});
    params.push({name: 'postbox', value: $("#currentpostbox").val()});
    params.push({name: 'folder', value: $("#currentpostfolder").val()});
    params.push({name: 'postsort', value: $("#postboxsort").val()});
    var url = '/mailbox/view_message';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            var folders = response.data.folders;
            for(index = 0; index < folders.length; ++index) {
                $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
            }
            $(".emailer-body").hide();
            $("#eml-table-messages").empty();
            $(".emaildetails").empty().html(response.data.content);
            $(".emaildetails").show();
            $("#loader").hide();
            init_message_management();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}

function flagmessage(message) {
    var params = new Array();
    params.push({name: 'message_id', value: message});
    params.push({name: 'postbox', value: $("#currentpostbox").val()});
    var url = '/mailbox/flag_message';
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            var folders = response.data.folders;
            for(index = 0; index < folders.length; ++index) {
                $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
            }
            $(".td-favorites[data-message='"+message+"']").empty().html(response.data.content);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}
function init_message_management() {
    // Back button
    $(".eml-bnt-back").unbind('click').click(function (){
        back_folder_view();
    });
    // Close button
    $(".eml-bnt-close").unbind('click').click(function (){
        back_folder_view();
    });
    // Delete button
    $(".emailnav-delete").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'message_id', value: $(this).data('message')});
        params.push({name: 'folder', value: $("#currentpostfolder").val()});
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'postsort', value: $("#postboxsort").val()});
        var url = '/mailbox/message_remove';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.redirect)==1) {
                    back_folder_view();
                }
                var folders = response.data.folders;
                for(index = 0; index < folders.length; ++index) {
                    $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                }
                $(".emaildetails").empty().html(response.data.content);
                $("#loader").hide();
                init_postbox_content();
                init_message_management();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Unread
    $(".emailnav-readsatus").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'message_id', value: $(this).data('message')});
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        var url = '/mailbox/message_read_status';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".emailnav-readsatus").empty().html(response.data.content);
                if (parseInt(response.data.unread)==1) {
                    $(".eml-subjicn").removeClass('unread').addClass('readed');
                } else {
                    $(".eml-subjicn").removeClass('readed').addClass('unread');
                }
                var folders = response.data.folders;
                for(index = 0; index < folders.length; ++index) {
                    $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                }
                $("#loader").hide();
                init_message_management();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json')
    });
    // Move Prev / Next
    $(".othereml-prev").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var message_id = $(this).data('prev');
            view_message(message_id);
        }
    });
    $(".othereml-next").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var message_id = $(this).data('next');
            view_message(message_id);
        }
    });
}

function back_folder_view() {
    var params = new Array();
    params.push({name: 'folder', value: $("#currentpostfolder").val()});
    params.push({name: 'postbox', value: $("#currentpostbox").val()});
    params.push({name: 'postsort', value: $("#postboxsort").val()});
    var url = '/mailbox/view_folder';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".emaildetails").empty();
            $(".emaildetails").hide();
            $(".emailer-body").show();
            $(".emlsblock-name").empty().html(response.data.folder_name);
            $("#currentpostfolder").val(response.data.folder);
            $("#eml-table-messages").empty().html(response.data.messages);
            $("input[name='allemls']").prop('checked',false);
            $("#loader").hide();
            init_messages_management();
            init_postbox_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}