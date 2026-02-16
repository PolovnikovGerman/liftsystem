var orderid = '';

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
            // // Init Calendar
            var heightcss = parseInt($("#clndrfull-body").css('height'))+35;
            $(".clndrfull-body").css('height', heightcss);
            // var heightreal = parseInt($("#clndrfull-body").height()) + 5;
            // if (heightreal < heightcss) {
            //     $("#clndrfull-body").css('height', heightreal);
            // }
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
        $(".reshedlordr-btn").removeClass('opened');
        if ($(".calendar-full").css('display')=='block') {
            $(".reschedulartabs").hide();
            $(".clndrfull-weeklytotal").show();
            $(".reschdl-infobody").show();
            $(".reschdl-linebody").hide();
        } else {
            $(".reschedulartabs").show();
            $(".clndrfull-weeklytotal").hide();
            $(".reschdl-infobody").hide();
            init_printdate_details($("#calendarprintdate").val());
            $(".reschdl-linebody").show();
        }
        $(".reschdl-body").hide();
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
            $(".reshedlordr-btn").addClass('opened');
            if ($(".calendar-full").css('display')=='block') {
                $(".reschdl-linebody").show();
                $(".clndrfull-weeklytotal").hide();
            } else {
                $(".todayblock").hide();
                $(".simpltodayblock").hide();
                $(".reschdl-linebody").hide();
                init_printdate_details($("#calendarprintdate").val());
            }
            $(".reschedulartabs").show();
            $(".reschdl-body").show();
            $(".reschdl-infobody").hide();
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
        if (limitweek <=1) {
            limitweek = 1;
        }
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
        // params.push({name: 'shortview', value: short});
        var url = '/printcalendar/weekcalendar';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#calendarprintdate").val(printdate);
                $(".pscalendar-week").empty().html(response.data.content);
                $(".pscalendar-daybox[data-printdate='"+printdate+"']").addClass('today');
                $(".psleft-topbar").hide();
                $(".calendar-week").show();
                $(".calendar-full").hide();
                $(".clndrfull-weeklytotal").hide();
                $(".statistics-block").hide();
                $(".reschdl-infobody").hide();
                $(".reschedulartabs").show();
                $(".reschdl-linebody").show();
                init_printdate_details(printdate);
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
    });
    $(".reschdltabl-order").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            var brand = $(this).data('brand');
            show_printschedule_order(order, brand);
        }
    });
    $(".reschditms-order").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            var brand = $(this).data('brand');
            show_printschedule_order(order, brand);
        }
    });
    $(".iconart").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            show_approveddocs(order);
        }
    });
}

// Week Calendar
function init_printdate_details(printdate) {
    var smallview = 0;
    if ($(".reshedlordr-btn").hasClass("opened")) {
        smallview = 1;
    }
    var params = new Array();
    params.push({name: 'printdate', value: printdate});
    params.push({name: 'smallview', value: smallview});
    var url = '/printcalendar/daylidetails';
    $.post(url, params, function (response){
        if (response.errors=='') {
            if (parseInt(smallview)==1) {
                $(".todayblock").hide();
                $(".simpltodayblock").show();
                $("#regularview-short").empty().html(response.data.content);
                $("#historyview-short").empty().html(response.data.historyview);
            } else {
                $(".simpltodayblock").hide()
                $(".todayblock").show();
                $("#regularview-full").empty().html(response.data.content);
                $("#historyview-full").empty().html(response.data.historyview);
            }
            if (parseInt(response.data.warningcnt)==0) {
                $(".warning-section").hide();
                $(".maingrey-close").show();
            } else {
                $(".warning-section").show();
                $(".maingrey-close").hide();
            }
            init_dailydetails_manage()
        } else {
            show_error(response);
        }
    },'json');
}

function init_dailydetails_manage() {
    $("input[name='shipdate']").datepicker({
        // format : 'mm/dd/yy',
        autoclose: true,
        todayHighlight: true
    });
    $("input[name='printdate']").datepicker({
        // format : 'mm/dd/yy',
        autoclose: true,
        todayHighlight: true
    });
    // Close Week data - show full calendar
    $(".maingrey-close").unbind('click').click(function () {
        var year = $("#printcaledyear").val();
        init_printcalendar(year);
        init_printstatistic();
        init_reshedule_totals(year);
        $(".calendar-week").hide();
        $(".simpltodayblock").hide();
        $(".todayblock").hide();
        $(".psleft-topbar").show();
        $(".calendar-full").show();
        if ($(".reshedlordr-btn").hasClass('opened')) {
            $(".clndrfull-weeklytotal").hide();
            $(".reschdl-infobody").hide();
            $(".reschdl-linebody").show();
            $(".reschedulartabs").show();
        } else {
            $(".clndrfull-weeklytotal").show();
            $(".reschdl-infobody").show();
            $(".reschdl-linebody").hide();
            $(".reschedulartabs").hide();
        }
        $(".statistics-block").show();
    });
    // Close Week data - show full calendar
    $(".warning-close").unbind('click').click(function () {
        var year = $("#printcaledyear").val();
        init_printcalendar(year);
        init_printstatistic();
        init_reshedule_totals(year);
        $(".calendar-week").hide();
        $(".simpltodayblock").hide();
        $(".todayblock").hide();
        $(".psleft-topbar").show();
        $(".calendar-full").show();
        if ($(".reshedlordr-btn").hasClass('opened')) {
            $(".clndrfull-weeklytotal").hide();
            $(".reschdl-infobody").hide();
            $(".reschdl-linebody").show();
            $(".reschedulartabs").show();
        } else {
            $(".clndrfull-weeklytotal").show();
            $(".reschdl-infobody").show();
            $(".reschdl-linebody").hide();
            $(".reschedulartabs").hide();
        }
        $(".statistics-block").show();
    });
    // Select date from week calendar
    $(".pscalendar-daybox").unbind('click').click(function () {
        if ($(this).hasClass('today')) {
        } else {
            var printdate = $(this).data('printdate');
            $(".pscalendar-daybox").removeClass('today');
            $(".pscalendar-daybox[data-printdate='" + printdate + "']").addClass('today');
            init_printdate_details(printdate);
            $("#calendarprintdate").val(printdate);
        }
    });
    // Move to previous week
    $(".pscalendar-arrowsleft").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'week', value: $("#printcalendarcurweek").val()})
        params.push({name: 'direct', value: 'prev'});
        var url = '/printcalendar/weekcalendarmove';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".pscalendar-week").empty().html(response.data.content);
                init_dailydetails_manage();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Move to next week
    $(".pscalendar-arrowsright").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'week', value: $("#printcalendarcurweek").val()})
        params.push({name: 'direct', value: 'next'});
        var url = '/printcalendar/weekcalendarmove';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".pscalendar-week").empty().html(response.data.content);
                init_dailydetails_manage();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Assign user / hide
    $(".userprinter").unbind('click').click(function () {
        var order = $(this).data('order');
        if ($(this).hasClass('openassign')) {
            $(this).empty().html('<img src="/img/printscheduler/user-printer.svg">');
            $(".assign-popup[data-order='" + order + "']").hide();
            $(this).removeClass('openassign');
        } else {
            var curusr = $(this).data('user');
            $(".assign-popup").hide();
            $(".userprinter").empty().html('<img src="/img/printscheduler/user-printer.svg">');
            $(".userprinter").removeClass('openassign');
            $(this).empty().html('<img src="/img/printscheduler/user-printer-white.svg">');
            $(this).addClass('openassign');
            $(".assign-popup[data-order='" + order + "']").show();
            init_printer_assign(order, curusr);
        }
    });
    // Stock button
    $(".regltabl-prepstock").unbind('click').click(function () {
        var ordercolor = $(this).data('ordercolor');
        var params = new Array();
        params.push({name: 'order_color', value: ordercolor});
        var url = '/printcalendar/stockupdate';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                if (parseInt(response.data.newval) == 1) {
                    $(".regltabl-prepstock[data-ordercolor='" + ordercolor + "']").addClass('grey');
                } else {
                    $(".regltabl-prepstock[data-ordercolor='" + ordercolor + "']").removeClass('grey');
                }
            } else {
                show_error(response)
            }
        }, 'json');
    });
    // Plate button
    $(".regltabl-prepplate").unbind('click').click(function () {
        var orderitem = $(this).data('orderitem');
        var params = new Array();
        params.push({name: 'order_item', value: orderitem});
        var url = '/printcalendar/platesupdate';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                if (parseInt(response.data.newval) == 1) {
                    $(".regltabl-prepplate[data-orderitem='" + orderitem + "']").addClass('grey');
                } else {
                    $(".regltabl-prepplate[data-orderitem='" + orderitem + "']").removeClass('grey');
                }
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Ink button
    $(".regltabl-prepink").unbind('click').click(function () {
        var ordercolor = $(this).data('ordercolor');
        var params = new Array();
        params.push({name: 'order_color', value: ordercolor});
        var url = '/printcalendar/inkupdate';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                if (parseInt(response.data.newval) == 1) {
                    $(".regltabl-prepink[data-ordercolor='" + ordercolor + "']").addClass('grey');
                } else {
                    $(".regltabl-prepink[data-ordercolor='" + ordercolor + "']").removeClass('grey');
                }
            } else {
                show_error(response)
            }
        }, 'json');
    });
    // Save Fullfilment / Printing
    $(".btnsave.fulfblock").unbind('click').click(function () {
        console.log('Save Color '+$(this).data('ordercolor'));
        if ($(this).hasClass('closedblock')) {
        } else {
            var ordercolor = $(this).data('ordercolor');
            var params = new Array();
            params.push({name: 'itemcolor', value: ordercolor});
            params.push({
                name: 'podate',
                value: $("input[name='printdate'][data-ordercolor='" + ordercolor + "']").val()
            });
            params.push({
                name: 'shipped',
                value: $("input[name='printval'][data-ordercolor='" + ordercolor + "']").val()
            });
            params.push({
                name: 'kepted',
                value: $("input[name='keptval'][data-ordercolor='" + ordercolor + "']").val()
            });
            params.push({
                name: 'misprint',
                value: $("input[name='misprintval'][data-ordercolor='" + ordercolor + "']").val()
            });
            // params.push({name: 'plates', value: $("input[name='platesval'][data-ordercolor='"+ordercolor+"']").val()});
            params.push({
                name: 'plates',
                value: $("select[name='platesval'][data-ordercolor='" + ordercolor + "']").val()
            });
            var url = '/printcalendar/outcomesave';
            $("#loader").show();
            $.post(url, params, function (response) {
                if (response.errors == '') {
                    if (parseInt(response.data.refreshinfo) == 1) {
                        $(".warning-section").empty().html(response.data.warningview)
                        $(".regular-section").empty().html(response.data.regularview);
                        $("#historyview-full").empty().html(response.data.historyview);
                    } else {
                        $(".regltabl-tr[data-ordercolor='" + ordercolor + "']").empty().html(response.data.content);
                    }
                    if (parseInt(response.data.warningcnt) == 0) {
                        $(".warning-section").hide();
                        $(".maingrey-close").show();
                    } else {
                        $(".warning-section").show();
                        $(".maingrey-close").hide();
                    }
                    $("#loader").hide();
                    init_dailydetails_manage();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            }, 'json');
        }
    });
    // Save shipping
    $(".btnsave.shipblock").unbind('click').click(function () {
        if ($(this).hasClass('closedblock')) {
        } else {
            var ordercolor = $(this).data('ordercolor');
            var params = new Array();
            params.push({name: 'itemcolor', value: ordercolor});
            params.push({
                name: 'shipqty',
                value: $("input[name='shipqty'][data-ordercolor='" + ordercolor + "']").val()
            });
            params.push({
                name: 'shipdate',
                value: $("input[name='shipdate'][data-ordercolor='" + ordercolor + "']").val()
            });
            params.push({
                name: 'shipmethod',
                value: $("select[name='shipmethod'][data-ordercolor='" + ordercolor + "']").val()
            });
            params.push({
                name: 'trackcode',
                value: $("input[name='shiptrackcode'][data-ordercolor='" + ordercolor + "']").val()
            });
            var url = '/printcalendar/shiporder';
            $("#loader").show();
            $.post(url, params, function (response) {
                if (response.errors == '') {
                    if (parseInt(response.data.refreshinfo) == 1) {
                        $(".warning-section").empty().html(response.data.warningview)
                        $(".regular-section").empty().html(response.data.regularview);
                        $("#historyview-full").empty().html(response.data.historyview);
                    } else {
                        $(".regltabl-tr[data-ordercolor='" + ordercolor + "']").empty().html(response.data.content);
                    }
                    if (parseInt(response.data.warningcnt) == 0) {
                        $(".warning-section").hide();
                        $(".maingrey-close").show();
                    } else {
                        $(".warning-section").show();
                        $(".maingrey-close").hide();
                    }
                    $("#loader").hide();
                    init_dailydetails_manage();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            }, 'json');
        }
    });
    // Copy Track # to clipboard
    $("div.trackbtn").unbind('click').click(function () {
        var copydat = $(this).data('track');
        var element = document.querySelector("input[name='trackcode'][data-track='" + copydat + "']");
        copyElementToClipboard(element);
        // $(element).show();
    });
    // Open Order data
    $(".warntabl-order").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            var brand = $(this).data('brand');
            show_printschedule_order(order, brand);
        }
    });
    $(".regltabl-order").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            var brand = $(this).data('brand');
            show_printschedule_order(order, brand);
        }
    });
    $(".histrtabl-order").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            var brand = $(this).data('brand');
            show_printschedule_order(order, brand);
        }
    });
    // Open approved Doc
    $(".iconart").unbind('click').click(function (){
        var order = $(this).data('order');
        if (typeof order === "undefined") {
        } else {
            show_approveddocs(order);
        }
    });
}

// Assign Printer to Order
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
            if ($(".reshedlordr-btn").hasClass('opened')) {
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
                        $(".regular-table").empty().html(response.data.content);
                        // $(".maingreyblock.fullinfo").find('div.regular-section').empty().html(response.data.content);
                    } else {
                        $(".regular-table").empty().html(response.data.content);
                        // $(".maingreyblock-small").find("div.regular-section").empty().html(response.data.content);
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

// Copy Track # to clipboard
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


// Drag & Drop
function dragstartHandler(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    orderid = ev.target.id;
}

function dragoverHandler(ev) {
    ev.preventDefault();
}

function dropHandler(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    var parentElement = ev.target.closest('.leftsideviewarea');
    var newdate = '';
    var incomeblock = '';
    if (parentElement) {
        newdate = parentElement.id.replace('printdate_','')
        incomeblock = 'left';
    } else {
        parentElement = ev.target.closest('.rightsideviewarea');
        if (parentElement) {
            newdate = parentElement.id.replace('printday_','');
            incomeblock = 'right';
        } else {
            parentElement = ev.target.closest('.psctable-td');
            if (parentElement) {
                newdate = parentElement.id.replace('caledarbox_','');
                incomeblock = 'fullcalendar';
            }
        }
    }
    if (incomeblock) {
        var moveorder = '';
        var outcomeblock = '';
        if (orderid.substring(0,10)=='shedulord_') {
            outcomeblock = 'right';
            moveorder = orderid.replace('shedulord_','');
        } else {
            outcomeblock = 'left';
            moveorder = orderid.replace('printord_','');
        }
        // // Send changes to Scheduler
        var params = new Array();
        params.push({name: 'print_date', value: newdate});
        params.push({name: 'order_id', value: moveorder});
        params.push({name: 'incomeblock', value: incomeblock});
        params.push({name: 'outcomeblock', value: outcomeblock});
        var url = '/printcalendar/ordernewdate';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (incomeblock==outcomeblock) {
                    $("div[data-printdata='"+newdate+"']").append(document.getElementById(data))
                } else {
                    if (incomeblock=='right') {
                        // Update Re Schedule body
                        if (parseInt(response.data.late)==1) {
                            $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.income);
                        } else {
                            $(".dayschedulearea[data-printdata='"+response.data.incomedate+"']").empty().html(response.data.income);
                        }
                        // $("#printshortunassignarea").empty().html(response.data.unassign);
                        // $("#printshortassignarea").empty().html(response.data.assign);
                        // Left part - warning
                        $(".warning-section").empty().html(response.data.warnings);
                        if (parseInt(response.data.warningscnt)==0) {
                            $(".warning-section").hide();
                            $(".maingrey-close").show();
                        } else {
                            $(".warning-section").show();
                            $(".maingrey-close").hide();
                        }
                        // Left part - common orders
                        $(".regular-section").empty().html(response.data.outcome);
                        // Week Totals
                        $(".pscalendar-daybox[data-printdate='"+response.data.outdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.orders);
                        $(".pscalendar-daybox[data-printdate='"+response.data.outdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.prints);
                        // Day Info
                        $(".maingrey-infobox").find('div.maingreyinfo-prints').find('span').empty().html(response.data.prints);
                        $(".maingrey-infobox").find('div.maingreyinfo-items').find('span').empty().html(response.data.items);
                        $(".maingrey-infobox").find('div.maingreyinfo-orders').find('span').empty().html(response.data.orders);
                    } else if(incomeblock=='left') {
                        // Left part - warning
                        $(".warning-section").empty().html(response.data.warnings);
                        if (parseInt(response.data.warningscnt)==0) {
                            $(".warning-section").hide();
                            $(".maingrey-close").show();
                        } else {
                            $(".warning-section").show();
                            $(".maingrey-close").hide();
                        }
                        // Left part - common orders
                        $(".regular-section").empty().html(response.data.income);
                        // Right part - list of orders on Re Schedule
                        if (parseInt(response.data.late)==1) {
                            $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.outcome);
                        } else {
                            $(".dayschedulearea[data-printdata='"+response.data.outdate+"']").empty().html(response.data.outcome);
                        }
                        // Update Calendar
                        $(".pscalendar-daybox[data-printdate='"+newdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.orders);
                        $(".pscalendar-daybox[data-printdate='"+newdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.prints);
                        $(".maingrey-infobox").find('div.maingreyinfo-prints').find('span').empty().html(response.data.prints);
                        $(".maingrey-infobox").find('div.maingreyinfo-items').find('span').empty().html(response.data.items);
                        $(".maingrey-infobox").find('div.maingreyinfo-orders').find('span').empty().html(response.data.orders);
                    } else {
                        // Full Calendar
                        $(".psctable-td[data-printdate='"+newdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.dayorders);
                        $(".psctable-td[data-printdate='"+newdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.dayprints);
                        // Totals
                        $(".summaryweek[data-weeknum='"+response.data.week+"']").find('div.totalboxtprinted-numbers[data-fld="prints"]').empty().html(response.data.total_prints);
                        $(".summaryweek[data-weeknum='"+response.data.week+"']").find('div.totalboxtprinted-numbers[data-fld="items"]').empty().html(response.data.total_items);
                        // Right part - list of orders on Re Schedule
                        if (parseInt(response.data.late)==1) {
                            $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.outcome);
                        } else {
                            $(".dayschedulearea[data-printdata='"+response.data.outdate+"']").empty().html(response.data.outcome);
                        }
                    }
                }
                orderid='';
                $.flash(response.data.message, {timeout: 5000});
            } else {
                // Show error
            }
        },'json');
    } else {
        // Income block empty
        console.log('Income block empty');
    }
}

function show_printschedule_order(order, brand) {
    var callpage = 'printscheduler';
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(response.data.cancelorder)===1) {
                $("#artModal").find('div.modal-header').addClass('cancelorder');
            } else {
                $("#artModal").find('div.modal-header').removeClass('cancelorder');
            }
            $("#editbuttonarea").css('visibility','hidden');
            $(".block_4").css('visibility','hidden');
            navigation_init();
        } else {
            show_error(response);
        }
    },'json');
}

function show_approveddocs(order) {
    var url="/printcalendar/order_showapprove";
    var params = new Array();
    params.push({name: 'order', value: order});
    $.post(url, params, function (response){
        if (response.errors=='') {
            var numdocs = parseInt(response.data.numdocs);
            if (numdocs==0) {
                alert('Order '+response.data.ordernum+' is blank');
            } else {
                for (let i = 0; i < numdocs; i++) {
                    var link = response.data.links[i];
                    window.open(link, response.data.source[i], 'width=600, height=800,toolbar=1')
                }
            }
        } else {
            show_error(response);
        }
    },'json');
}