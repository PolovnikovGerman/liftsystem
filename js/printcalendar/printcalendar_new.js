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
    })
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
    // $(".btn-reschedular").unbind('click').click(function () {
    //     open_reschedule();
    // });
    // $(".btn-reschedular-open").unbind('click').click(function () {
    //     close_reschedule();
    // });
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
    $(".btnsave.fulfblock").unbind('click').click(function () {
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
                        $(".history-section").empty().html(response.data.historyview);
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
                        $(".history-section").empty().html(response.data.historyview);
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
    $("div.trackbtn").unbind('click').click(function () {
        var copydat = $(this).data('track');
        var element = document.querySelector("input[name='trackcode'][data-track='" + copydat + "']");
        copyElementToClipboard(element);
        // $(element).show();
    });
}


// Drag & Drop
function dragstartHandler(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    orderid = ev.target.id;
    console.log('Order '+orderid);
}

function dragoverHandler(ev) {
    ev.preventDefault();
}

function dropHandler(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    // ev.target.appendChild(document.getElementById(data));
    var parentElement = ev.target.closest('.leftsideviewarea');
    var newdate = '';
    var incomeblock = '';
    if (parentElement) {
        console.log('Parent '+parentElement.id+'!');
        newdate = parentElement.id.replace('printdate_','')
        incomeblock = 'left';
    } else {
        parentElement = ev.target.closest('.rightsideviewarea');
        if (parentElement) {
            console.log('Parent '+parentElement.id+'!');
            newdate = parentElement.id.replace('printday_','');
            incomeblock = 'right';
        } else {
            parentElement = ev.target.closest('.psctable-td');
            if (parentElement) {
                console.log('Parent '+parentElement.id+'!');
                newdate = parentElement.id.replace('caledarbox_','');
                console.log('Full Calendar Date '+newdate);
                incomeblock = 'fullcalendar';
            }
        }
    }
    if (incomeblock) {
        // console.log('Add Element '+ev.target.id+'!');
        console.log('Income Block '+incomeblock);
        // var moveorder = '';
        // var outcomeblock = '';
        // if (orderid.substring(0,10)=='shedulord_') {
        //     outcomeblock = 'right';
        //     moveorder = orderid.replace('shedulord_','');
        // } else {
        //     outcomeblock = 'left';
        //     moveorder = orderid.replace('printord_','');
        // }
        // // Send changes to Scheduler
        // var params = new Array();
        // params.push({name: 'print_date', value: newdate});
        // params.push({name: 'order_id', value: moveorder});
        // params.push({name: 'incomeblock', value: incomeblock});
        // params.push({name: 'outcomeblock', value: outcomeblock});
        // var url = '/printcalendar/ordernewdate';
        // $.post(url, params, function (response){
        //     if (response.errors=='') {
        //         if (incomeblock==outcomeblock) {
        //             $("div[data-printdata='"+newdate+"']").append(document.getElementById(data))
        //         } else {
        //             if (incomeblock=='right') {
        //                 if (parseInt(response.data.late)==1) {
        //                     $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.income);
        //                 } else {
        //                     $(".dayschedulearea[data-printdata='"+response.data.incomedate+"']").empty().html(response.data.income);
        //                 }
        //                 $("#printshortunassignarea").empty().html(response.data.unassign);
        //                 $("#printshortassignarea").empty().html(response.data.assign);
        //                 $(".pscalendar-daybox[data-printdate='"+response.data.outdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.orders);
        //                 $(".pscalendar-daybox[data-printdate='"+response.data.outdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.prints);
        //             } else {
        //                 // if ($("#printshortregularviewarea").length==0) {
        //                 //    $(".regular-section").html(response.data.todaytemplate);
        //                 // }
        //                 $(".warning-section").empty().html(response.data.warnings);
        //                 if (parseInt(response.data.warningscnt)==0) {
        //                     $(".warning-section").hide();
        //                     $(".maingrey-close").show();
        //                 } else {
        //                     $(".warning-section").show();
        //                     $(".maingrey-close").hide();
        //                 }
        //                 $(".regular-section").empty().html(response.data.income);
        //                 if (parseInt(response.data.late)==1) {
        //                     $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.outcome);
        //                 } else {
        //                     $(".dayschedulearea[data-printdata='"+response.data.outdate+"']").empty().html(response.data.outcome);
        //                 }
        //                 // Update Calendar
        //                 $(".pscalendar-daybox[data-printdate='"+newdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.orders);
        //                 $(".pscalendar-daybox[data-printdate='"+newdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.prints);
        //                 orderid='';
        //             }
        //         }
        //         $.flash(response.data.message, {timeout: 5000});
        //     } else {
        //         // Show error
        //     }
        // },'json');
    } else {
        // Income block empty
        console.log('Income block empty');
    }
}