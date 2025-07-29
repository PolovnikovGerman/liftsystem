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
            $(".eml-moremenu").hide();
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
                // $(".emailnav-readsatus").empty().html(response.data.content);
                // if (parseInt(response.data.unread)==1) {
                //     $(".eml-subjicn").removeClass('unread').addClass('readed');
                // } else {
                //     $(".eml-subjicn").removeClass('readed').addClass('unread');
                // }
                var folders = response.data.folders;
                for(index = 0; index < folders.length; ++index) {
                    $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                }
                back_folder_view();
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
    // Move to folder
    $(".eml-bnt-movefrom").unbind('click').click(function (){
        var message = $(this).data('message');
        var params = new Array();
        params.push({name: 'postbox', value: $("#currentpostbox").val()});
        params.push({name: 'folder', value: $("#currentpostfolder").val()});
        var url = '/mailbox/message_move_folders';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#msgdetailsfolders").empty().html(response.data.content);
                $("#msgdetailsfolders").show();
                init_messagefolder_move(message);
            } else {
                show_error(response);
            }
        },'json');

    });
}

function init_messagefolder_move(message) {
    $(".emlfolders-menu-close").unbind('click').click(function (){
        $(".emlfolders-menu[data-message='"+message+"']").hide();
        init_messages_management();
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
                $("#msgdetailsfolders").hide();
                var folders = response.data.folders;
                for(index = 0; index < folders.length; ++index) {
                    $(".mainbtn[data-folder='"+folders[index]['folder_id']+"']").find('span.mainbtn-number').empty().html(folders[index]['cnt']);
                }
                back_folder_view();
                $("#loader").hide();
                init_message_management();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })

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