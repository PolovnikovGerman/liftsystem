function init_printcalendar_content() {
    var year = $("#printcaledyear").val();
    init_printcalendar(year);
    init_printstatistic(year);
    // Statistic
}

function init_printcalendar(year) {
    var params = new Array();
    params.push({name: 'year', value: year});
    var url = '/printcalendar/yearcalendar';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".psctable-body").empty().html(response.data.calendarview);
            var scrollElement = new SimpleBar(document.getElementById('psctable-body'), { autoHide: false });
            setTimeout(() => {
                scrollElement.getScrollElement().scrollTop = scrollElement.getScrollElement().scrollHeight;
            }, "300");
            // Init Calendar
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_printstatistic(year) {
    var params = new Array();
    params.push({name: 'year', value: year});
    var url = '/printcalendar/yearstatic';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".lateorders-box").empty().html(response.data.latecontent);
            $(".statistics-boxes").empty().html(response.data.statistic);
        } else {
            show_error(response);
        }
    },'json');
}