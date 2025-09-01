function init_dailydetails_manage() {
    $(".maingrey-close").unbind('click').click(function (){
        var year = $("#printcaledyear").val();
        init_printcalendar(year);
        init_printstatistic(year);
        $("#printcalendardetailsview").hide();
        $("#printcalendarfullview").show();
        $("#calendarprintdate").val(0);
    });
    $(".pscalendar-daybox").unbind('click').click(function (){
        if ($(this).hasClass('today')) {
        } else {
            var printdate = $(this).data('printdate');
            $(".pscalendar-daybox").removeClass('today');
            $(".pscalendar-daybox[data-printdate='"+printdate+"']").addClass('today');
            init_printdate_details(printdate);
            $("#calendarprintdate").val(printdate);
        }
    });
    $(".btnreschedular-btn").unbind('click').click(function(){
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
                $("#loader").hide();
                // $(".history-section").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })
    $(".pscalendar-arrowsleft").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'week', value: $("#printcalendarcurweek").val()})
        params.push({name: 'direct', value: 'prev'});
        var url = '/printcalendar/weekcalendarmove';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".pscalendar-week").empty().html(response.data.content);
                init_dailydetails_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".pscalendar-arrowsright").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'week', value: $("#printcalendarcurweek").val()})
        params.push({name: 'direct', value: 'next'});
        var url = '/printcalendar/weekcalendarmove';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".pscalendar-week").empty().html(response.data.content);
                init_dailydetails_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".userprinter").unbind('click').click(function (){
        var order = $(this).data('order');
        var curusr = $(this).data('user');
        $(".assign-popup").hide();
        $(".assign-popup[data-order='"+order+"']").show();
        init_printer_assign(order, curusr);
    });
}

function init_printer_assign(order, curuser) {
    $("li.assignusr").unbind('click').click(function (){
        var user = $(this).data('user');
        if (parseInt(curuser)==0 && parseInt(user)==0) {
            $(".assign-popup[data-order='"+order+"']").hide();
        } else if (parseInt(curuser)==parseInt(user)){
            $(".assign-popup[data-order='"+order+"']").hide();
        } else {
            var params = new Array();
            params.push({name: 'order', value: order});
            params.push({name: 'user', value: user});
            var url = '/printcalendar/assignprintorder';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $(".maingreyblock.fullinfo").empty().html(response.data.content);
                    $("#loader").hide();
                    init_dailydetails_manage();
                } else {
                    show_error(response);
                    $("#loader").hide();
                }
            },'json');
        }
    });
}