function init_printcalendar_content() {
    var year = $("#printcaledyear").val();
    init_printcalendar(year);
    init_printstatistic();
    init_reshedule_totals(year);
    $(".psleft-years-box").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
        } else {
            var newyear = $(this).data('yearprint');
            $(".psleft-years-box").removeClass('active');
            $(".psleft-years-box[data-yearprint='"+newyear+"']").addClass('active');
            $("#printcaledyear").val(newyear);
            init_printcalendar_content();
            init_reshedule_totals(newyear);
        }
    });
    $(".reshedlordr-btn").unbind('click').click(function(){
        init_reshedule_view();
    })
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
            $(".statistics-block").empty().html(response.data.statistic);
        } else {
            show_error(response);
        }
    },'json');
}

function init_reshedule_totals(year) {
    var params = new Array();
    params.push({name: 'year', value: year});
    var url = '/printcalendar/reshedulestatic';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("div.sprbox-result[data-fld='stilorders']").empty().html(response.data.stil_orders);
            $("div.sprbox-result[data-fld='stilitems']").empty().html(response.data.stil_items);
            $("div.sprbox-result[data-fld='stilprints']").empty().html(response.data.stil_prints);
            $("div.lateordbox-result[data-fld='lateorders']").empty().html(response.data.late_orders);
            $("div.lateordbox-result[data-fld='lateitems']").empty().html(response.data.late_items);
            $("div.lateordbox-result[data-fld='lateprints']").empty().html(response.data.late_prints);
        } else {
            show_error(response);
        }
    },'json');
}

function init_reshedule_view() {
    if ($(".reshedlordr-btn").hasClass('opened')) {
        $(".btnreschedular-btn").empty().html('<i class="fa fa-caret-down" aria-hidden="true"></i>');
        $(".reschedulartabs").hide();
        $(".reschdl-body").hide();
        $(".clndrfull-weeklytotal").show();
        $(".reschdl-infobody").show();
        $(".reschdl-linebody").hide();
        $(".reshedlordr-btn").removeClass('opened');
    } else {
        var url = '/printcalendar/rescheduletoday';
        var params = new Array();
        var sortfld = 'print_date';
        if ($(".reschdl-tab.active").length > 0) {
            sortfld = $(".reschdl-tab.active").data('sortfld');
        }
        params.push({name: 'sortfld', value: sortfld});
        $("#loader").show();
        $.post(url, params, function (response){
            $(".btnreschedular-btn").empty().html('<i class="fa fa-times" aria-hidden="true"></i>');
            $(".reschedulartabs").show();
            $(".reschdl-body").show();
            $(".clndrfull-weeklytotal").hide();
            $(".reschdl-infobody").hide();
            $(".reschdl-linebody").show();
            $(".reshedlordr-btn").addClass('opened');
            $(".reschdl-body").empty().html(response.data.calendarview);
            // $(".pschedul-leftside").show();
            // $(".pschedul-rightside").show();
            init_reschedule_management();
            // init_dailydetails_manage();
            if ($("#reschdltabl-body").length > 0) {
                new SimpleBar(document.getElementById('reschdltabl-body'), { autoHide: false });
            }
            if ($("#reschditms-body").length > 0) {
                new SimpleBar(document.getElementById('reschditms-body'), { autoHide: false });
            }
            // if (parseInt(response.data.warningcnt) > 0) {
            //     $(".warning-section").show();
            //     $(".maingrey-close").hide();
            // } else {
            //     $(".warning-section").hide();
            //     $(".maingrey-close").show();
            // }
            $("#loader").hide();
        },'json')
    }
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
    // Click on week day
    $(".psctable-td").unbind('click').click(function (){
        var printdate = $(this).data('printdate');
        var printweek = $(this).data('printweek');
        var params = new Array();
        params.push({name: 'printdate', value: printdate});
        params.push({name: 'printweek', value: printweek});
        var url = '/printcalendar/weekcalendar';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".psleft-topbar").hide();
                $(".calendar-week").show()
                $(".calendar-full").hide()
                $(".clndrfull-weeklytotal").hide();
                $(".statistics-block").hide();
                $(".simpltodayblock").hide();
                $(".todayblock").show();
                $(".reschedulartabs").show();
                $(".reschdl-body").hide();
                $(".reschdl-infobody").hide();
                $(".reschdl-linebody").show();

                // $(".pscalendar-week").empty().html(response.data.content);
                // $(".pscalendar-daybox[data-printdate='"+printdate+"']").addClass('today');
                // init_printdate_details(printdate);
                // $("#printcalendarfullview").hide();
                // $("#printcalendardetailsview").show();
                // $("#calendarprintdate").val(printdate);
            } else {
                show_error(response);
            }
        },'json');
    });

}

function init_reschedule_management() {
    $(".reschdl-tab").unbind('click').click(function (){
        var sortfld = $(this).data('sortfld');
        if ($(this).hasClass('active')) {
        } else {
            $(".reschdl-tab").removeClass('active');
            $(".reschdl-tab[data-sortfld='"+sortfld+"']").addClass('active');
            var params = new Array();
            params.push({name: 'sortfld', value: sortfld});
            var url = '/printcalendar/reschedulechangeview';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $(".reschdl-body").empty().html(response.data.calendarview);
                    init_reschedule_management();
                    if ($("#reschdltabl-body").length > 0) {
                        new SimpleBar(document.getElementById('reschdltabl-body'), { autoHide: false });
                    }
                    if ($("#reschditms-body").length > 0) {
                        new SimpleBar(document.getElementById('reschditms-body'), { autoHide: false });
                    }
                    $("#loader").hide();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    })
}