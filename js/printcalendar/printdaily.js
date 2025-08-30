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
}