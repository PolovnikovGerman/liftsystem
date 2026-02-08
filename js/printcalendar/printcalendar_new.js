function init_printcalendar_content() {
    var year = $("#printcaledyear").val();
    init_printcalendar(year);
    init_printstatistic();

    $(".psleft-years-box").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
        } else {
            var newyear = $(this).data('yearprint');
            $(".psleft-years-box").removeClass('active');
            $(".psleft-years-box[data-yearprint='"+newyear+"']").addClass('active');
            $("#printcaledyear").val(newyear);
            init_printcalendar_content();
        }
    });
}

function init_printcalendar(year) {
    var params = new Array();
    params.push({name: 'year', value: year});
    var url = '/printcalendar/yearcalendar';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#clndrfull-body").empty().html(response.data.calendarview);
            $(".weeklytotal-body").empty().html(response.data.totalsview);
            // var scrollElement = new SimpleBar(document.getElementById('clndrfull-body'), { autoHide: false });
            // var heightcss = parseInt($("#psctable-body").css('height'));
            // var heightreal = parseInt($("#psctable-body").find('div.simplebar-content').height()) + 5;
            // if (heightreal < heightcss) {
            //     $("#psctable-body").css('height', heightreal);
            // }
            // setTimeout(() => {
            //     scrollElement.getScrollElement().scrollTop = scrollElement.getScrollElement().scrollHeight;
            //     // Get css val and compare with real value
            // }, "300");
            // // Init Calendar
            init_fullcalendar();
            $("#calendweekbgn").val(response.data.minweek);
            $("#calendweekend").val(response.data.maxweek);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_printstatistic() {
    var params = new Array();
    // params.push({name: 'year', value: year});
    var url = '/printcalendar/yearstatic';
    $.post(url, params, function (response){
        if (response.errors=='') {
            // $(".lateorders-box").empty().html(response.data.latecontent);
            $(".statistics-block").empty().html(response.data.statistic);
        } else {
            show_error(response);
        }
    },'json');
}

function init_fullcalendar() {
    // Scrolls
    $(".calnd-up").unbind('click').click(function (){
        var minweek = parseInt($("#calendweekbgn").val());
        var maxweek = 0;
        minweek = minweek - 13 + 1;
        if (minweek<=1) {
            minweek = 1;
            maxweek = 13;
        } else {
            maxweek = minweek + 13 - 1;
        }
        $("#clndrfull-body").find('div.week-tr').hide();
        $(".weeklytotal-body").find('div.week-tr').hide();
        var i=minweek;
        for (i=minweek; i<=maxweek; i++) {
            $("#clndrfull-body").find("div.week-tr[data-week='"+i+"']").show();
            $(".weeklytotal-body").find("div.week-tr[data-week='"+i+"']").show();
        }
        $("#calendweekbgn").val(minweek);
        // $("#calendweekend").val(maxweek);
    });
    $(".calnd-down").unbind('click').click(function (){
        var minweek = parseInt($("#calendweekbgn").val());
        var maxweek = 0;
        var limitweek = parseInt($("#calendweekend").val()) - 13 +1;
        minweek = minweek + 13 + 1;
        if (minweek > limitweek) {
            minweek = limitweek;
            maxweek = parseInt($("#calendweekend").val());
        } else {
            maxweek = minweek + 13 - 1;
        }
        $("#clndrfull-body").find('div.week-tr').hide();
        $(".weeklytotal-body").find('div.week-tr').hide();
        var i=minweek;
        for (i=minweek; i<=maxweek; i++) {
            $("#clndrfull-body").find("div.week-tr[data-week='"+i+"']").show();
            $(".weeklytotal-body").find("div.week-tr[data-week='"+i+"']").show();
        }
        $("#calendweekbgn").val(minweek);
    });
}