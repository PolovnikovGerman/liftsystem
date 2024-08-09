function init_printscheduler_content() {
    init_printscheduler_past();
    init_printscheduler_current();
    leftmenu_alignment();
}

function init_printscheduler_past() {
    var params = new Array();
    params.push({name: 'brand', value: $("#printschbrand").val()});
    params.push({name: 'calendar', value: 'past'});
    var url = '/printscheduler/get_calendar';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pastdue-body").empty().html(response.data.content);
            $("#loader").hide();
        } else {
            show_error(response);
            $("#loader").hide();
        }
    },'json');
}

function init_printscheduler_current() {
    var params = new Array();
    params.push({name: 'brand', value: $("#printschbrand").val()});
    params.push({name: 'calendar', value: 'ontime'});
    var url = '/printscheduler/get_calendar';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#printschcurrentbody").empty().html(response.data.content);
            $("#loader").hide();
        } else {
            show_error(response);
            $("#loader").hide();
        }
    },'json');
}