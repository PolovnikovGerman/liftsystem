function init_dailydetails_manage() {
    $(".maingrey-close").unbind('click').click(function (){
        var year = $("#printcaledyear").val();
        init_printcalendar(year);
        init_printstatistic(year);
        $("#printcalendardetailsview").hide();
        $("#printcalendarfullview").show();
    });
    $(".pscalendar-daybox").unbind('click').click(function (){
        if ($(this).hasClass('today')) {
        } else {
            var printdate = $(this).data('printdate');
            $(".pscalendar-daybox").removeClass('today');
            $(".pscalendar-daybox[data-printdate='"+printdate+"']").addClass('today');
            init_printdate_details(printdate);
        }
    })
}