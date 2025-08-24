function init_printcalendar_content() {
    var year = $("#printcaledyear").val();
    init_printcalendar(year);
    // Statistic
}

function init_printcalendar(year) {
    var params = new Array();
    params.push({name: 'year', value: year});
    var url = '/printcalendar/yearcalendar';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".psctable-body").empty().html(response.data.calendarview);
            var scrollElement = new SimpleBar(document.getElementById('psctable-body'), { autoHide: false });
            setTimeout(() => {
                scrollElement.getScrollElement().scrollTop = scrollElement.getScrollElement().scrollHeight;
            }, "300");
            // Init Calendar
        } else {
            show_error(response);
        }
    },'json');
}