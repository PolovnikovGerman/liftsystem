function init_dailydetails_manage() {
    $("input[name='shipdate']").datepicker({
        // format : 'mm/dd/yy',
        autoclose : true,
        todayHighlight: true
    });
    $(".maingrey-close").unbind('click').click(function (){
        var year = $("#printcaledyear").val();
        init_printcalendar(year);
        init_printstatistic(year);
        $("#printcalendardetailsview").hide();
        $("#printcalendarfullview").show();
        $("#calendarprintdate").val(0);
        $(".btn-reschedular-open").hide();
        $(".btn-reschedular").show();
        $(".pschedul-leftside").hide();
        $(".pschedul-rightside").hide();
        $(".maingreyblock.fullinfo").show();
        $(".history-section").show();
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
    $(".btn-reschedular").unbind('click').click(function(){
        open_reschedule();
    });
    $(".btn-reschedular-open").unbind('click').click(function(){
        close_reschedule();
    });
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
        if ($(this).hasClass('openassign')) {
            $(this).empty().html('<img src="/img/printscheduler/user-printer.svg">');
            $(".assign-popup[data-order='"+order+"']").hide();
            $(this).removeClass('openassign');
        } else {
            var curusr = $(this).data('user');
            $(".assign-popup").hide();
            $(".userprinter").empty().html('<img src="/img/printscheduler/user-printer.svg">');
            $(".userprinter").removeClass('openassign');
            $(this).empty().html('<img src="/img/printscheduler/user-printer-white.svg">');
            $(this).addClass('openassign');
            $(".assign-popup[data-order='"+order+"']").show();
            init_printer_assign(order, curusr);
        }
    });
    $(".regltabl-prepstock").unbind('click').click(function (){
        var ordercolor = $(this).data('ordercolor');
        var params = new Array();
        params.push({name: 'order_color', value: ordercolor});
        var url = '/printcalendar/stockupdate';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".regltabl-prepstock[data-ordercolor='"+ordercolor+"']").addClass('grey');
                } else {
                    $(".regltabl-prepstock[data-ordercolor='"+ordercolor+"']").removeClass('grey');
                }
            } else {
                show_error(response)
            }
        },'json');
    });
    $(".regltabl-prepplate").unbind('click').click(function (){
        var orderitem = $(this).data('orderitem');
        var params = new Array();
        params.push({name: 'order_item', value: orderitem});
        var url = '/printcalendar/platesupdate';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".regltabl-prepplate[data-orderitem='"+orderitem+"']").addClass('grey');
                } else {
                    $(".regltabl-prepplate[data-orderitem='"+orderitem+"']").removeClass('grey');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".regltabl-prepink").unbind('click').click(function (){
        var ordercolor = $(this).data('ordercolor');
        var params = new Array();
        params.push({name: 'order_color', value: ordercolor});
        var url = '/printcalendar/inkupdate';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".regltabl-prepink[data-ordercolor='"+ordercolor+"']").addClass('grey');
                } else {
                    $(".regltabl-prepink[data-ordercolor='"+ordercolor+"']").removeClass('grey');
                }
            } else {
                show_error(response)
            }
        },'json');
    });
    $(".btnsave.fulfblock").unbind('click').click(function(){
        if ($(this).hasClass('closedblock')) {
        } else {
            var ordercolor = $(this).data('ordercolor');
            var params = new Array();
            params.push({name: 'itemcolor', value: ordercolor});
            params.push({name: 'shipped', value: $("input[name='printval'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({name: 'kepted', value: $("input[name='keptval'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({name: 'misprint', value: $("input[name='misprintval'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({name: 'plates', value: $("input[name='platesval'][data-ordercolor='"+ordercolor+"']").val()});
            var url = '/printcalendar/outcomesave';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    if (parseInt(response.data.refreshinfo)==1) {
                        $(".warning-section").empty().html(response.data.warningview)
                        $(".regular-section").empty().html(response.data.regularview);
                        $(".history-section").empty().html(response.data.historyview);
                    } else {
                        $(".regltabl-tr[data-ordercolor='"+ordercolor+"']").empty().html(response.data.content);
                    }
                    $("#loader").hide();
                    init_dailydetails_manage();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    $(".btnsave.shipblock").unbind('click').click(function (){
        if ($(this).hasClass('closedblock')) {
        } else {
            var ordercolor = $(this).data('ordercolor');
            var params = new Array();
            params.push({name: 'itemcolor', value: ordercolor});
            params.push({name: 'shipqty', value: $("input[name='shipqty'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({name: 'shipdate', value: $("input[name='shipdate'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({name: 'shipmethod', value: $("select[name='shipmethod'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({name: 'trackcode', value: $("input[name='shiptrackcode'][data-ordercolor='"+ordercolor+"']").val()});
            var url='/printcalendar/shiporder';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    if (parseInt(response.data.refreshinfo)==1) {
                        $(".warning-section").empty().html(response.data.warningview)
                        $(".regular-section").empty().html(response.data.regularview);
                        $(".history-section").empty().html(response.data.historyview);
                    } else {
                        $(".regltabl-tr[data-ordercolor='"+ordercolor+"']").empty().html(response.data.content);
                    }
                    $("#loader").hide();
                    init_dailydetails_manage();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    $("div.trackbtn").unbind('click').click(function (){
        var copydat = $(this).data('track');
        var element = document.querySelector("input[name='trackcode'][data-track='"+copydat+"']");
        copyElementToClipboard(element);
        // $(element).show();
    });
}

function init_printer_assign(order, curuser) {
    $("li.assignusr").unbind('click').click(function (){
        var user = $(this).data('user');
        if (parseInt(curuser)==0 && parseInt(user)==0) {
            $(".assign-popup[data-order='"+order+"']").hide();
            $(".userprinter[data-order='"+order+"'][data-user='"+curuser+"']").empty().html('<img src="/img/printscheduler/user-printer.svg">')
            $(".userprinter[data-order='"+order+"'][data-user='"+curuser+"']").removeClass('openassign');
        } else if (parseInt(curuser)==parseInt(user)){
            $(".assign-popup[data-order='"+order+"']").hide();
            $(".userprinter[data-order='"+order+"'][data-user='"+curuser+"']").empty().html('<img src="/img/printscheduler/user-printer.svg">')
            $(".userprinter[data-order='"+order+"'][data-user='"+curuser+"']").removeClass('openassign');
        } else {
            var params = new Array();
            var smallview = 0;
            if ($(".pschedul-leftside").css('display')=='block') {
                smallview = 1;
            }
            params.push({name: 'order', value: order});
            params.push({name: 'user', value: user});
            params.push({name: 'smallview', value: smallview});
            var url = '/printcalendar/assignprintorder';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    if (parseInt(smallview)==0) {
                        $(".maingreyblock.fullinfo").find('div.regular-section').empty().html(response.data.content);
                    } else {
                        $(".maingreyblock-small").find("div.regular-section").empty().html(response.data.content);
                    }
                    $("#loader").hide();
                    init_dailydetails_manage();
                } else {
                    show_error(response);
                    $("#loader").hide();
                }
            },'json');
        }
    });
    // Click on opens assign
}

function copyElementToClipboard(element) {
    // $(element).show();
    $(element).focus();
    $(element).select();
    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Msg '+msg);
    } catch (err) {
        console.log('Oops, unable to copy');
    }
    // $(element).hide();
}

function open_reschedule() {
    var printdate = $("#calendarprintdate").val();
    var params = new Array();
    params.push({name: 'printdate', value: printdate});
    var sortfld = 'print_date';
    if ($(".reschdl-tab.active").length > 0) {
        sortfld = $(".reschdl-tab.active").data('sortfld');
    }
    params.push({name: 'sortfld', value: sortfld});
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

function close_reschedule() {
    var printdate = $("#calendarprintdate").val();
    $("#loader").show();
    $(".btn-reschedular-open").hide();
    $(".btn-reschedular").show();
    $(".pschedul-leftside").hide();
    $(".pschedul-rightside").hide();
    $(".maingreyblock.fullinfo").show();
    $(".history-section").show();
    init_printdate_details(printdate);
    $("#loader").hide();
}