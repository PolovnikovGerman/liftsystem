function init_parsedemails_content() {
    // get list of senders
    show_senders_list();
    initParsedemailsPagination();
}

function show_senders_list() {
    var url = '/admin/whitelistdata';
    $.post(url, {}, function (response) {
        if (response.errors=='') {
            $("div.whitelist_data").empty().html(response.data.content);
            init_whitelist_management();
        } else {
            show_error(response);
        }
    },'json');
}

function init_whitelist_management() {
    $("div.delwhitelist").click(function(){
        var email_id=$(this).data('whitelistid');
        delete_sender(email_id);
    });
    $("div.editwhitelist").click(function(){
        var email_id=$(this).data('whitelistid');
        edit_sender(email_id);
    });
    $("div.newsender").unbind('click').click(function(){
        add_sender();
    })
}

function delete_sender(email_id) {
    var sender=$("div.whitelist_datarow[data-whitelistid="+email_id+"]").find('div.whitelist_senderdata').text();
    if (confirm('Delete '+sender+' from Parser White List ?')) {
        var url="/admin/whitelist_delete";
        $.post(url, {'email_id':email_id},function(response){
            if (response.errors=='') {
                show_senders_list();
            } else {
                show_error(response);
            }
        },'json');
    }
}

function edit_sender(email_id) {
    var url="/admin/whitelist_edit";
    $.post(url,{'email_id':email_id},function(response){
        if (response.errors=='') {
            $("div.delwhitelist").unbind('click');
            $("div.editwhitelist").unbind('click');
            $("div.newsender").unbind('click');
            $("div.whitelist_datarow[data-whitelistid="+email_id+"]").css('display','block').empty().html(response.data.content);
            $("img#addwhitelist").click(function(){
                save_whitelist(email_id);
            })
            $("img#cancelwhitelist").click(function(){
                show_senders_list();
            })
        } else {
            show_error(response);
        }
    },'json');
}

function save_whitelist(email_id) {
    var sender=$("input#sendermail").val();
    var user_id=$("select#user_id").val();
    var url="/admin/whitelist_save";
    var params = new Array();
    params.push({name: 'sender', value :sender});
    params.push({name: 'user_id', value: user_id});
    params.push({name: 'email_id', value: email_id});
    $.post(url, params, function(response){
        if (response.errors=='') {
            show_senders_list();
        } else {
            show_error(response)
        }
    },'json');
}

function add_sender() {
    var url="/admin/whitelist_new";
    $.post(url,{},function(response){
        if (response.errors=='') {
            $("div.delwhitelist img").unbind('click');
            $("div.editwhitelist img").unbind('click');
            $("div.newsender").unbind('click');
            $("div.whitelist_datarow[data-whitelistid=0]").css('display','block').empty().html(response.data.content);
            $("img#addwhitelist").click(function(){
                save_whitelist(0);
            })
            $("img#cancelwhitelist").click(function(){
                show_senders_list();
            })
        } else {
            show_error(response);
        }
    },'json')
}

function initParsedemailsPagination() {
    // count entries inside the hidden content
    var num_entries = $('#whitelisttotal').val();
    // var perpage = itemsperpage;
    var perpage = $("#whitelistperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#whitelistpagination").empty();
        pageWhitelistCallback(0);
    } else {
        var curpage = $("#curpage").val();
        // Create content inside pagination element
        $("#whitelistpagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageWhitelistCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageWhitelistCallback(page_index) {
    var direction=$("#whitelistdirect").val();
    var maxval = $('#whitelisttotal').val();

    /* Search */
    var url = '/admin/parsedemaildata';
    var params = new Array();
    params.push({name: 'offset', value :page_index});
    params.push({name: 'limit', value :$("#whitelistperpage").val()});
    params.push({name: 'order_by', value:$("#whitelistorder").val()});
    params.push({name: 'direction', value :direction});
    params.push({name: 'maxval', value :maxval});
    $("#loader").show();
    $.post(url,{},function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div.whitelist_parselog_data").empty().html(response.data.content);
            init_parsedlog_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_parsedlog_content() {
    $(".wlparse_email").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom right',
            at: 'top left',
        },
        style: 'qtip-light'
    });
}