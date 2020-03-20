function init_notifications_page() {
    init_notifications();
    // Change Brand
    $("#notificationsviewbrandmenu").find("div.left_tab").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#notificationsviewbrand").val(brand);
        $("#notificationsviewbrandmenu").find("div.left_tab").removeClass('active');
        $("#notificationsviewbrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
        init_notifications();
    });
}

function init_notifications() {
    $("#addnotification").unbind('click').click(function(){
        add_notification();
    });
    pageselectCallback(0);
}

/* Init pagination */
function initPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrec').val();
    var perpage = itemsperpage;
    var curpage = $("#curpage").val();
    // Create content inside pagination element
    $("#Pagination").pagination(num_entries, {
        current_page: curpage,
        callback: pageselectCallback,
        items_per_page: perpage, // Show only one item per page
        load_first_page: true,
        num_edge_entries : 1,
        num_display_entries : 7,
        prev_text : '<<',
        next_text : '>>'
    });
}

function pageselectCallback(page_index) {
    var url = '/settings/emailnotificationdat';
    var params = new Array();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: 1000});
    params.push({name: 'order_by', value: $("#orderby").val()});
    params.push({name: 'direction', value: $("#direction").val()});
    params.push({name: 'maxval', value: $('#totalrec').val()});
    params.push({name: 'brand', value: $("#notificationsviewbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $('#tabinfo').empty().html(response.data.content);
            $("#curpage").val(page_index);
            init_notification_content();
        } else {
            alert(response.errors);
            if (response.data.url !== undefined) {
                window.location.href = response.data.url;
            }
        }
    }, 'json');
}

function init_notification_content() {
    var heigh = $("#tabinfo").css('height');
    heigh = heigh.replace('px', '');
    heigh = parseInt(heigh);
    if (heigh < 529) {
        $(".last_column").css('width', '85');
    }
    /* Init delete func */
    $("a.delnotificationlnk").unbind('click').click(function() {
        var notification = $(this).data('notification');
        delete_notification(notification);
    })
    $("a.edtnotificationlnk").unbind('click').click(function() {
        var notification = $(this).data('notification');
        edit_notification(notification);
    });
}

function delete_notification(notif_id) {
    var mail=$("#notline"+notif_id+" .notification_email").text();
    if (confirm('Do you realy want to delete notificaion email '+mail+'?')) {
        var url="/settings/deletenotification";
        var params = new Array();
        params.push({name: 'notification_id', value: notif_id});
        params.push({name: 'brand', value: $("#notificationsviewbrand").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#totalrec").val(response.data.totals);
                pageselectCallback(0);
            } else {
                alert(response.errors);
                if(response.data.url !== undefined) {
                    window.location.href=response.data.url;
                }
            }
        }, 'json')
    }
}

function add_notification() {
    var url="/settings/notification";
    var params = new Array();
    params.push({name: 'notification_id', value: 0});
    params.push({name: 'brand', value: $("#notificationsviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','565px');
            $("#pageModal").modal('show');
            $(".saveediting").unbind('click').click(function(){
                save_notification();
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function edit_notification(notification_id) {
    var url="/settings/notification";
    var params=new Array();
    params.push({name: 'notification_id', value: notification_id});
    params.push({name: 'brand', value: $("#notificationsviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','565px');
            $("#pageModal").modal('show');
            $(".saveediting").unbind('click').click(function(){
                save_notification();
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function save_notification() {
    var url = '/settings/save_notification';
    var params = new Array();
    params.push({name: 'notification_id', value :$("#notification_id").val()});
    params.push({name: 'notification_system', value: $("#notification_system").val()});
    params.push({name: 'notification_email', value: $("#notification_email").val()});
    params.push({name: 'brand', value: $("#notificationsviewbrand").val()});
    var url="/settings/save_notification";
    $.post(url, params, function(response){
        if (response.errors!='') {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        } else {
            $("#pageModal").modal('hide');
            pageselectCallback(0);
        }
    }, 'json');
}

function save_surveysettings() {
    var url="/otherpages/save_surveycfg";
    var params=new Array();
    params.push({name: 'survey_apiid', value: $("input#surveyid").val()});
    params.push({name: 'survey_show', value:$("select#surveyshow").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.survaycfgsave").hide();
        } else {
            show_error(response);
        }
    },'json');
}