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
}

function add_newfolder() {
    $(".cancel-folder").unbind('click').click(function (){
        console.log('Cancel save folder');
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

}

