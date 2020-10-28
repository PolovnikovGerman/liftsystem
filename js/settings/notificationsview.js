function init_notifications_page(brand) {
    init_notifications(brand);
    // Change Brand
}

function init_notifications(brand) {
    $(".addnotification[data-brand='"+brand+"']").unbind('click').click(function(){
        add_notification(brand);
    });
    pageselectCallback(0, brand);
}

/* Init pagination */
// function initPagination() {
//     // count entries inside the hidden content
//     var num_entries = $('#totalrec').val();
//     var perpage = itemsperpage;
//     var curpage = $("#curpage").val();
//     // Create content inside pagination element
//     $("#Pagination").pagination(num_entries, {
//         current_page: curpage,
//         callback: pageselectCallback,
//         items_per_page: perpage, // Show only one item per page
//         load_first_page: true,
//         num_edge_entries : 1,
//         num_display_entries : 7,
//         prev_text : '<<',
//         next_text : '>>'
//     });
// }

function pageselectCallback(page_index, brand) {
    var url = '/settings/emailnotificationdat';
    var params = new Array();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: 1000});
    params.push({name: 'order_by', value: $(".orderby[data-brand='"+brand+"']").val()});
    params.push({name: 'direction', value: $(".direction[data-brand='"+brand+"']").val()});
    params.push({name: 'maxval', value: $(".totalrec[data-brand='"+brand+"']").val()});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $(".table_notification[data-brand='"+brand+"']").empty().html(response.data.content);
            // $("#curpage").val(page_index);
            init_notification_content(brand);
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_notification_content(brand) {
    var heigh = $(".table_notification[data-brand='"+brand+"']").css('height');
    heigh = heigh.replace('px', '');
    heigh = parseInt(heigh);
    if (heigh < 529) {
        $(".last_column").css('width', '85');
    }
    /* Init delete func */
    $("a.delnotificationlnk").unbind('click').click(function() {
        var notification = $(this).data('notification');
        delete_notification(notification, brand);
    })
    $("a.edtnotificationlnk").unbind('click').click(function() {
        var notification = $(this).data('notification');
        edit_notification(notification,brand);
    });
}

function delete_notification(notif_id, brand) {
    var mail=$("#notline"+notif_id+" .notification_email").text();
    if (confirm('Do you realy want to delete notificaion email '+mail+'?')) {
        var url="/settings/deletenotification";
        var params = new Array();
        params.push({name: 'notification_id', value: notif_id});
        params.push({name: 'brand', value: brand});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input.totalrec[data-brand='"+brand+"']").val(response.data.totals);
                pageselectCallback(0, brand);
            } else {
                show_error(response);
            }
        }, 'json')
    }
}

function add_notification(brand) {
    var url="/settings/notification";
    var params = new Array();
    params.push({name: 'notification_id', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','565px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $(".saveediting").unbind('click').click(function(){
                save_notification(brand);
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

function edit_notification(notification_id, brand) {
    var url="/settings/notification";
    var params=new Array();
    params.push({name: 'notification_id', value: notification_id});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','565px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $(".saveediting").unbind('click').click(function(){
                save_notification(brand);
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_notification(brand) {
    var url = '/settings/save_notification';
    var params = new Array();
    params.push({name: 'notification_id', value :$("#notification_id").val()});
    params.push({name: 'notification_system', value: $("#notification_system").val()});
    params.push({name: 'notification_email', value: $("#notification_email").val()});
    params.push({name: 'brand', value: brand});
    var url="/settings/save_notification";
    $.post(url, params, function(response){
        if (response.errors!='') {
            show_error(response);
        } else {
            $("#pageModal").modal('hide');
            pageselectCallback(0, brand);
        }
    }, 'json');
}

// function save_surveysettings() {
//     var url="/otherpages/save_surveycfg";
//     var params=new Array();
//     params.push({name: 'survey_apiid', value: $("input#surveyid").val()});
//     params.push({name: 'survey_show', value:$("select#surveyshow").val()});
//     $.post(url, params, function(response){
//         if (response.errors=='') {
//             $("div.survaycfgsave").hide();
//         } else {
//             show_error(response);
//         }
//     },'json');
// }