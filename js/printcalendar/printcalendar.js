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
            init_fullcalendar();
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

function init_fullcalendar() {
    $(".psctable-td").unbind('click').click(function (){
        var printdate = $(this).data('printdate');
        var printweek = $(this).data('printweek');
        var params = new Array();
        params.push({name: 'printdate', value: printdate});
        params.push({name: 'printweek', value: printweek});
        var url = '/printcalendar/weekcalendar';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".pscalendar-week").empty().html(response.data.content);
                $(".pscalendar-daybox[data-printdate='"+printdate+"']").addClass('today');
                init_printdate_details(printdate);
                $("#printcalendarfullview").hide();
                $("#printcalendardetailsview").show();
                $("#calendarprintdate").val(printdate);
            } else {
                show_error(response);
            }
        },'json');
    });
    // $(".btnreschedular-btn")
    $(".btn-reschedular").unbind('click').click(function (){
        var url = '/printcalendar/rescheduletoday';
        var params = new Array();
        var sortfld = 'print_date';
        if ($(".reschdl-tab.active").length > 0) {
            sortfld = $(".reschdl-tab.active").data('sortfld');
        }
        params.push({name: 'sortfld', value: sortfld});
        $("#loader").show();
        $.post(url, params, function (response){
            // show week caledar
            var printdate = response.data.printdate;
            $(".pscalendar-week").empty().html(response.data.weekcalend);
            $(".pscalendar-daybox[data-printdate='"+printdate+"']").addClass('today');
            $("#printcalendarfullview").hide();
            $("#printcalendardetailsview").show();
            $("#calendarprintdate").val(printdate);
            // Call reschedule
            $(".btn-reschedular").hide();
            $(".btn-reschedular-open").show();
            $(".maingreyblock.fullinfo").hide();
            $(".history-section").hide();
            $(".maingreyblock-small").empty().html(response.data.content);
            $(".history-section-small").empty().html(response.data.historyview);
            $(".reschedularbody").empty().html(response.data.calendarview);
            $(".pschedul-leftside").show();
            $(".pschedul-rightside").show();
            init_reschedule_management();
            init_dailydetails_manage();
            if ($("#reschdltabl-body").length > 0) {
                new SimpleBar(document.getElementById('reschdltabl-body'), { autoHide: false });
            }
            if ($("#reschditms-body").length > 0) {
                new SimpleBar(document.getElementById('reschditms-body'), { autoHide: false });
            }
            if (parseInt(response.data.warningcnt) > 0) {
                $(".warning-section").show();
            }
            $("#loader").hide();
        },'json')
    });
}

function init_current_reschedule() {
    var printdate = $("#calendarprintdate").val();
    var params = new Array();
    params.push({name: 'printdate', value: printdate});
    var url = '/printcalendar/rescheduleview';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".btn-reschedular").hide();
            $(".btn-reschedular-open").show();
            $(".maingreyblock.fullinfo").hide();
            $(".history-section").hide();
            $(".maingreyblock-small").empty().html(response.data.content);
            $(".history-section-small").empty().html(response.data.historyview);
            $(".reschedularbody").empty().html(response.data.calendarview);
            $(".pschedul-leftside").show();
            $(".pschedul-rightside").show();
            init_reschedule_management();
            init_dailydetails_manage();
            if ($("#reschdltabl-body").length > 0) {
                new SimpleBar(document.getElementById('reschdltabl-body'), { autoHide: false });
            }
            if ($("#reschditms-body").length > 0) {
                new SimpleBar(document.getElementById('reschditms-body'), { autoHide: false });
            }
            $("#loader").hide();
            // $(".history-section").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');

}
function init_printdate_details(printdate) {
    var smallview = 0;
    if ($(".pschedul-leftside").css('display')=='block') {
        smallview = 1;
    }
    var params = new Array();
    params.push({name: 'printdate', value: printdate});
    params.push({name: 'smallview', value: smallview});
    var url = '/printcalendar/daylidetails';
    $.post(url, params, function (response){
        if (response.errors=='') {
            if (parseInt(smallview)==1) {
                $(".maingreyblock-small").empty().html(response.data.content);
                $(".history-section-small").empty().html(response.data.historyview);
            } else {
                $(".maingreyblock.fullinfo").empty().html(response.data.content);
                $(".history-section").empty().html(response.data.historyview);
            }
            if (parseInt(response.data.warningcnt)==0) {
                $(".warning-section").hide();
            } else {
                $(".warning-section").show();
            }
            init_dailydetails_manage()
        } else {
            show_error(response);
        }
    },'json');
}
