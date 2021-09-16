function init_tasks_management() {
    $("select#ordproofview").unbind('change').change(function(){
        init_tasks_page();
    })
    $("input#ordersproofs").unbind('change').change(function(){
        change_approved_view('need_approve');
    })
    $("div.taskview_timetitle").unbind('click').click(function(){
        var tasktype=$(this).parent("div.taskview_devstage_subtitle").prop('id');
        change_tasksort(tasktype,'time');
    })
    $("div.taskview_ordertitle").unbind('click').click(function(){
        var tasktype=$(this).parent("div.taskview_devstage_subtitle").prop('id');
        change_tasksort(tasktype,'order');
    })
    $("a#clear_tasks").unbind('click').click(function(){
        $("input#tasksearch").val('');
        restore_task_view();
    })
    $("a#find_tasks").unbind('click').click(function(){
        searchtasks();
    })
    $("input#viewallapproved").unbind('change').change(function(){
        change_approved_view('just_approved');
    });
    // Change Brand
    $("#arttasksviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#arttasksviewbrand").val(brand);
        $("#arttasksviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#arttasksviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#arttasksviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_tasks_page();
    });
}

function init_tasks_page() {
    var inclreq=$("input#ordersproofs").prop('checked');
    var showreq=0;
    if (inclreq===true) {
        showreq=1;
    }
    var showallapprov=0;
    if ($("input#viewallapproved").prop('checked')==true) {
        showallapprov=1;
    }
    var params=new Array();
    params.push({name:'taskview',value:$("select#ordproofview").val()});
    params.push({name:'showreq',value:showreq});
    params.push({name:'nonart_sort',value:$("input#nonart_sort").val()});
    params.push({name:'nonart_direc',value:$("input#nonart_direc").val()});
    params.push({name:'redraw_sort',value:$("input#redraw_sort").val()});
    params.push({name:'redraw_direc',value:$("input#redraw_direc").val()});
    params.push({name:'proof_sort',value:$("input#proof_sort").val()});
    params.push({name:'proof_direc',value:$("input#proof_direc").val()});
    params.push({name:'needapr_sort',value:$("input#needapr_sort").val()});
    params.push({name:'needapr_direc',value:$("input#needapr_direc").val()});
    params.push({name:'approved_sort',value:$("input#aproved_sort").val()});
    params.push({name:'aproved_direc',value:$("input#aproved_direc").val()});
    params.push({name:"aproved_viewall", value: showallapprov});
    params.push({name: 'brand', value: $("input#arttasksviewbrand").val()});
    var url="/art/tasks_data";
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div#dataneedartarea").empty().html(response.data.nonart);
            $("div#dataredrawnarea").empty().html(response.data.redrawn);
            $("div#datatoproofarea").empty().html(response.data.toproof);
            $("div#dataneedaprarea").empty().html(response.data.needapr);
            $("div#dataaprovedarea").empty().html(response.data.aproved);
            $("#loader").hide();
            /* Call popup */
            $("div.taskview_order").click(function(){
                call_details(this);
            })
            $("div.reminderarea").click(function(){
                var task_id=$(this).data('taskid');
                call_reminder(task_id);
            });
            jQuery.balloon.init();
            // $("div.taskview_order").qtip({
            //     content: {
            //         attr: 'data-content'
            //     },
            //     position: {
            //         'my': 'bottom center',
            //         'at': 'top center'
            //     },
            //     style: 'qtip_light task_detailview',
            // });
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}

function change_approved_view(stage) {
    var inclreq=$("input#ordersproofs").prop('checked');
    var showreq=0;
    if (inclreq===true) {
        showreq=1;
    }
    var showallapprov=0;
    if ($("input#viewallapproved").prop('checked')==true) {
        showallapprov=1;
    }
    var params=new Array();
    params.push({name:'taskview',value:$("select#ordproofview").val()});
    params.push({name:'showreq',value:showreq});
    // params.push({name:'stage',value:'need_approve'});
    params.push({name:'stage',value:stage});
    if (stage=='need_approve') {
        params.push({name:'task_sort',value:$("input#needapr_sort").val()});
        params.push({name:'task_direc',value:$("input#needapr_direc").val()});
    } else {
        params.push({name:'task_sort',value:$("input#aproved_sort").val()});
        params.push({name:'task_direc',value:$("input#aproved_direc").val()});
    }
    params.push({name:'aproved_viewall', value: showallapprov});
    params.push({name: 'brand', value: $("input#arttasksviewbrand").val()});
    var url="/art/tasks_stage";
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            if (stage=='need_approve') {
                $("div#dataneedaprarea").empty().html(response.data.content);
            } else {
                $("div#dataaprovedarea").empty().html(response.data.content);
            }
            /* Call popup */
            $("div.taskview_order").click(function(){
                call_details(this);
            })
            $("div.reminderarea").unbind('click').click(function(){
                var task_id=$(this).data('taskid');
                call_reminder(task_id);
            });
            jQuery.balloon.init();
            // $("div.taskview_order").qtip({
            //     content: {
            //         attr: 'data-content'
            //     },
            //     position: {
            //         my: 'bottom right',
            //         at: 'top left',
            //     },
            //     style: 'qtip_light task_detailview',
            //     show: 'click',
            //     hide: 'click'
            // });
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function change_tasksort(tasktype,sorttype) {
    var stage='';
    var sortby='';
    var sort='';
    var datarea='';
    switch(tasktype) {
        case 'noarttitle':
            stage='noart';
            sortby=$("input#nonart_sort").val();
            sort=$("input#nonart_direc").val();
            datarea='dataneedartarea';
            break;
        case 'redrawtitle':
            stage='redrawn';
            sortby=$("input#redraw_sort").val();
            sort=$("input#redraw_direc").val();
            datarea='dataredrawnarea';
            break;
        case 'prooftitle':
            stage='need_proof';
            sortby=$("input#proof_sort").val();
            sort=$("input#proof_direc").val();
            datarea='datatoproofarea';
            break;
        case 'needaprtitle':
            stage='need_approve';
            sortby=$("input#needapr_sort").val();
            sort=$("input#needapr_direc").val();
            datarea='dataneedaprarea';
            break;
        case 'aprovedtitle':
            stage='just_approved';
            sortby=$("input#aproved_sort").val();
            sort=$("input#aproved_direc").val();
            datarea='dataaprovedarea';
            break;
    }

    if (sorttype===sortby) {
        if (sort==='desc') {
            sort='asc';
        } else {
            sort='desc';
        }
    } else {
        sortby=sorttype;
    }

    switch(tasktype) {
        case 'noarttitle':
            $("input#nonart_sort").val(sortby);
            $("input#nonart_direc").val(sort);
            break;
        case 'redrawtitle':
            $("input#redraw_sort").val(sortby);
            $("input#redraw_direc").val(sort);
            break;
        case 'prooftitle':
            $("input#proof_sort").val(sortby);
            $("input#proof_direc").val(sort);
            break;
        case 'needaprtitle':
            $("input#needapr_sort").val(sortby);
            $("input#needapr_direc").val(sort);
            break;
        case 'aprovedtitle':
            $("input#aproved_sort").val(sortby);
            $("input#aproved_direc").val(sort);
            break;

    }
    /* Change View */
    $("div#"+tasktype+" .taskview_sortarea").removeClass('sorttaskdesc').removeClass('sorttaskasc');
    $("div#"+tasktype+" ."+sortby+"sort").addClass('sorttask'+sort);
    $("div#"+tasktype+" .taskview_timetitle").removeClass('sortactive');
    $("div#"+tasktype+" .taskview_ordertitle").removeClass('sortactive');
    $("div#"+tasktype+" .taskview_"+sortby+"title").addClass('sortactive');


    var inclreq=$("input#ordersproofs").prop('checked');
    var showreq=0;
    if (inclreq===true) {
        showreq=1;
    }
    var showallapprov=0;
    if ($("input#viewallapproved").prop('checked')==true) {
        showallapprov=1;
    }
    var params=new Array();
    params.push({name:'taskview',value:$("select#ordproofview").val()});
    params.push({name:'showreq',value:showreq});
    params.push({name:'stage',value:stage});
    params.push({name:'task_sort',value:sortby});
    params.push({name:'task_direc',value:sort});
    params.push({name:'aproved_viewall', value: showallapprov});
    params.push({name: 'brand', value: $("input#arttasksviewbrand").val()});
    var url="/art/tasks_stage";
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors==='') {
            $("div#"+datarea).empty().html(response.data.content);
            /* Call popup */
            $("div.taskview_order").click(function(){
                call_details(this);
            })
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function call_details(obj) {
    var objid=obj.id;
    if (objid.substr(0,3)=='ord') {
        var order_id=objid.substr(3);
        order_artstage(order_id,'art_tasks');
    } else {
        var mailid=objid.substr(2);
        artproof_lead(mailid);
    }
}

function call_reminder(task_id) {
    var url="/art/task_remindmail";
    var template=$("input#templateslist").val();
    $.post(url, {'task_id':task_id,'template':template}, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html('New Remind Message');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_remindermanage(task_id)
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_remindermanage(task_id) {
    $("div.approvemail_send").click(function(){
        var artwork_id=$(this).data('artworkid');
        send_reminder(task_id, artwork_id);
    })
    $("div.addbccapprove").click(function(){
        var bcctype=$(this).data('applybcc');
        if (bcctype=='hidden') {
            $(this).data('applybcc','show').empty().html('hide bcc');
            $("div#emailbccdata").show();
            $("textarea.aprovemail_message").css('height','222');
        } else {
            $(this).data('applybcc','hidden').empty().html('add bcc');
            $("div#emailbccdata").hide();
            $("textarea.aprovemail_message").css('height','241');
        }
    })
}

function send_reminder(task_id, artwork_id) {
    var params=new Array();
    params.push({name:'task_id',value:task_id});
    params.push({name:'artwork_id',value:artwork_id});
    var bcctype=$("div.addbccapprove").data('applybcc');
    var bccmail='';
    if (bcctype=='show') {
        bccmail=$("input#approvemail_copy").val();
    }
    params.push({name:'cc', value:bccmail});
    params.push({name:'customer_email', value:$("input#approvemail_to").val()});
    params.push({name:'from', value:$("input#approvemail_from").val()});
    params.push({name:'subject',value:$("input#approvemail_subj").val()});
    params.push({name:'message',value:$("textarea.aprovemail_message").val()});
    var url="/art/task_sendreminder";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModal").modal('hide');
        } else {
            show_error(response);
        }
    }, 'json')
}

function searchtasks() {
    var params=new Array();
    params.push({name:'tasktype', value: $("select#tasksearchselect").val()});
    params.push({name:'tasksearch', value:$("input#tasksearch").val()});
    params.push({name: 'brand', value: $("input#arttasksviewbrand").val()});
    var url="/art/tasksearch";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.taskview_datacontent").empty().html(response.data.content);
            if (response.data.type=='order') {
                // general_view_init();
                initGeneralPagination();
            } else {
                init_prooflistmanage();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function restore_task_view() {
    var url="/art/restore_task";
    $.post(url,{},function(response){
        $("div.taskview_datacontent").empty().html(response.data.content);
        init_tasks_page();
    },'json');
}