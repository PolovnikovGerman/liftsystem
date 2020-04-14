function init_calendars_page() {
    init_calendars();
    // Change Brand
    $("#calendarsviewbrandmenu").find("div.left_tab").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#calendarsviewbrand").val(brand);
        $("#calendarsviewbrandmenu").find("div.left_tab").removeClass('active');
        $("#calendarsviewbrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
        init_calendars();
    });
}

function init_calendar_manage() {
    $(".addcalend").click(function(){
        add_calend();
    });
}

function init_calendars() {
    initCalendPagination();
}

function initCalendPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalusers').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpage").val();
    var datqry=new Date().getTime();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".Pagination").empty();
        pageCalendCallback(0);
    } else {
        var curpage = $("#curpage").val();
        // Create content inside pagination element
        $(".Pagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageCalendCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageCalendCallback(page_index) {
    var url = '/settings/calenddata';
    var params = new Array();
    params.push({name: 'offset', value :page_index});
    params.push({name: 'limit', value :$("#perpagecalend").val()});
    params.push({name: 'order_by', value :$("#ordercalend").val()});
    params.push({name: 'direction', value: $("#direccalend").val()});
    params.push({name: 'maxval', value :$('#totalcalend').val()});
    params.push({name: 'brand', value: $("#calendarsviewbrand").val()});
    /* Search */
    $("#loader").css('display','block');
    $.post(url, params,function(response){
        $("#loader").css('display','none');
        if (response.errors=='') {
            $("div#calendinfo").empty().html(response.data.content);
            /* Change view */
            init_calendlist_content();
        } else {
            show_error(response);
        }
    },'json');
}

function init_calendlist_content() {
    $(".caldel").click(function(){
        var calendar = $(this).data('calendar');
        del_calend(calendar);
    })
    $(".caledit").click(function(){
        var calendar = $(this).data('calendar');
        edit_calend(calendar);
    });
};

function edit_calend(calend_id) {
    var url="/settings/calendar_edit";
    $.post(url,{'calend_id':calend_id}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','565px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_calendedit_content();
        } else {
            show_error(response);
        }
    },'json');
}

function add_calend() {
    var calend_id=0;
    var url="/admin/calendar_edit";
    $.post(url,{'calend_id':calend_id}, function(response){
        if (response.errors=='') {
            show_popup('calendeditarea');
            $("#pop_content").empty().html(response.data.content);
            init_calendedit_content();
        } else {
            show_error(response);
        }
    },'json');
}

function del_calend(calend_id) {
    if (confirm('You realy want to delete this business calendar?')) {
        var url="/calendars/delcalend";
        $.post(url, {'calendar_id':calend_id}, function(response){
            if (response.errors=='') {
                $("#tableinfo").empty().html(response.data.content);
            } else {
                alert(response.errors);
                if(response.data.url !== undefined) {
                    window.location.href=response.data.url;
                }
            }
        }, 'json');
    }
}

// $(document).ready(function() {
function init_calendedit_content() {
    $("#f_btn").datepicker({
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate', function (e) {
        // add_holiday(e.date);
        var d = new Date(e.date);
        add_holiday(d.getTime())
    });
    $("a#save").click(function(){
        save_calend();
    });
    $(".busday").unbind('click').live('click',function(){
        busday_change(this);
    });
    $(".calenddelrow").unbind('click').click(function(){
        var line = $(this).data('calendline');
        delete_calendata(line);
    });
}

function add_holiday(newdate) {
    var url = '/settings/calendar_addholliday';
    var params = new Array();
    params.push({name: 'session', value: $("#editcalendsession_id").val()});
    params.push({name: 'newdate', value: newdate});
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#holidayslist").empty().html(response.data.content);
            $(".calenddelrow").unbind('click').click(function(){
                var line = $(this).data('calendline');
                delete_calendata(line);
            });
        } else {
            show_error(response);
        }
    },'json');
}

function busday_change(obj) {
    var objid=obj.id;
    var spanid="name_"+objid;

    if ($("#"+objid).prop('checked')) {
        $("#"+spanid).removeClass('holiday_bisday').addClass('active_bisday');
    } else {
        $("#"+spanid).removeClass('active_bisday').addClass('holiday_bisday');
    }
}

function delete_calendata(line) {
    if (confirm('You realy want to delete this date?')) {
        var url="/settings/calendar_delline";
        var params = new Array();
        params.push({name: 'session', value: $("#editcalendsession_id").val()});
        params.push({name: 'line', value: line});
        $.post(url,params,function(response){
            if (response.errors=='') {
                $("#holidayslist").empty().html(response.data.content);
                $(".calenddelrow").unbind('click').click(function(){
                    var line = $(this).data('calendline');
                    delete_calendata(line);
                });
            } else {
                show_error(response);
            }
        },'json');
    }
}


function save_calend(obj) {
    var url="/settings/calendar_save";
    var params = new Array();
    params.push({name: 'session', value: $("#editcalendsession_id").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
            initCalendPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}
