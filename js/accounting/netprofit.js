function init_netprofit_area() {
    init_netprofitpage();
    rebuild_charttable();
    init_expenses_details('ads');
    init_expenses_details('upwork');
    init_expenses_details('w9work');
    init_expenses_details('discretionary');
    init_netprofit_areacontent();
}

function init_netprofitpage() {
    var url='/accounting/netprofitdat';
    var radio = $("#netprofitviewtype").val();
    var params=new Array();
    params.push({name: 'type', value:$("select#but-reportview").val()});
    params.push({name: 'radio', value: radio});
    params.push({name: 'fromweek', value: $("select#weekselectfrom").val()});
    params.push({name: 'untilweek', value: $("select#weekselectuntil").val()});
    params.push({name: 'order_by', value: $("select#netreportsortorder").val()});
    params.push({name: 'limitshow', value :$("input#limitweekshow").val()});
    // params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    params.push({name: 'brand', value: 'ALL'});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.netprofitviewdata").empty().html(response.data.content);
            $("div.netprofit-running").empty().html(response.data.total_view);
            init_netprofit_content();
            $("#loader").hide();
            jQuery.balloon.init();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function rebuild_charttable() {
    var params=new Array();
    params.push({name: 'compareweek', value: $("select.weektotalsviewtype").val()});
    if ($("select.weektotalsviewtype").val()==0) {
        params.push({name: 'weekbgn', value: 0 });
        params.push({name: 'weekend', value: 0 });
    } else {
        params.push({name: 'weekbgn', value: $("select#strweek").val()});
        params.push({name: 'weekend', value: $("select#endweek").val()});
    }
    params.push({name: 'paceincome', value: $("input#projincome").val()});
    params.push({name: 'paceexpense', value: $("input#projexpence").val()});
    params.push({name: 'brand', value: $("#netprofitchartdatabrand").val()});
    var url="/accounting/netprofit_charttabledata";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.weektotalsdataarea").empty().html(response.data.content);
            if ($("div.showhidecuryear").hasClass('hide')) {
            //    $("div.parametervalue.currentyearval").show();
            //    $("div.prognosis.currentyear").show();
            } else {
            //    $("div.parametervalue.currentyearval").hide();
            //    $("div.prognosis.currentyear").hide();
            }
            $("div.expensivesrow").hide();
            init_charttable_content();
        } else {
            show_error(response);
        }
    },'json');
}

function init_expenses_details(expenstype) {
    var params=new Array();
    params.push({name: 'expenstype', value: expenstype});
    params.push({name: 'brand', value: $("#netprofitchartdatabrand").val()});
    var url="/accounting/netprofit_expensetable";
    $.post(url,params,function(response){
        if (response.errors=='') {
            if (expenstype=='ads') {
                $("span#adstotals").empty().html(response.data.totals);
                $(".expensesdata-table-data.ads").empty().html(response.data.tableview);
            } else if (expenstype=='upwork') {
                $("span#upworktotals").empty().html(response.data.totals);
                $(".expensesdata-table-data.upwork").empty().html(response.data.tableview);
            } else if (expenstype=='w9work') {
                $("span#w9worktotals").empty().html(response.data.totals);
                $(".expensesdata-table-data.w9work").empty().html(response.data.tableview);
            } else if (expenstype=='discretionary') {
                $("span#discretionarytotals").empty().html(response.data.totals);
                $(".expensesdata-table-data.discretionary").empty().html(response.data.tableview);
            }
            leftmenu_alignment();
        } else {
            show_error(response);
        }
    },'json');
}

function init_netprofit_content() {
    $(".includeweek").unbind('click').click(function () {
        var profit = $(this).data('profit');
        check_week(profit);
    });
    $("div.weekname.editdata").unbind('click').click(function(){
        var profit=$(this).data('profit');
        edit_profdat(profit);
    });
}

function check_week(profit) {
    var params=new Array();
    params.push({name: 'profit_id', value: profit});
    // params.push({name: 'brand', value: 'ALL'});
    params.push({name: 'fromweek', value: $("select#weekselectfrom").val()});
    params.push({name: 'untilweek', value: $("select#weekselectuntil").val()});
    params.push({name: 'viewtype', value: $("#netprofitviewtype").val()});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url="/accounting/netprofit_checkweek"
    $.post(url, params, function(response){
        if (response.errors=='') {
            $(".includeweek[data-profit='"+profit+"']").empty().html(response.data.weekcheck);
            $("div.netprofit-running").empty().html(response.data.total_view);
        } else {
            show_error(response);
        }
    }, 'json');
}

function edit_profdat(profit_id) {
    var params=new Array();
    params.push({name: 'profit_id', value: profit_id});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url='/accounting/netprofitedit';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.cell_week2").unbind('click');
            $("div.netprofit-table-data[data-profit='"+profit_id+"']").empty().html(response.data.content);
            init_netprofitdetails_edit();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function init_netprofitdetails_edit() {
    $(".but-cancel").unbind('click').click(function () {
        init_netprofitpage();
    });
    $("input.netprofitedit").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/accounting/netprofit_details_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.btneditnetprofit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/accounting/netprofit_purchase';
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('netproofpurchasedata');
                $("div#pop_content").empty().html(response.data.content);
                $("a#popupContactClose").unbind('click').click(function(){
                    disablePopup();
                });
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Include / exclude
    $("#editnetdetailsdebtincl").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/accounting/netprofit_details_debtincl';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#editnetdetailsdebtincl").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        }, 'json');
    });
}

function init_netprofitdetails_popup() {

}

function init_charttable_content() {
    $("span.exponsivedata").unbind('click').click(function(){
        var show=1;
        if ($(this).hasClass('hiden')) {
            show=0;
        }
        if (show==1) {
            $(this).empty().html('<i class="fa fa-minus-square-o" aria-hidden="true">').removeClass('shown').addClass('hiden');
            $("div.weektotalsrow.expensivesrow").show();
        } else {
            $(this).empty().html('<i class="fa fa-plus-square-o" aria-hidden="true">').removeClass('hiden').addClass('shown');
            $("div.weektotalsrow.expensivesrow").hide();
        }
    });
    $("#netprofitchartdatabrand").unbind('change').change(function(){
        rebuild_charttable();
        init_expenses_details('ads');
        init_expenses_details('upwork');
        init_expenses_details('w9work');
        init_expenses_details('discretionary');
    });
    $("select.weektotalsviewtype").unbind('change').change(function () {
        if ($(this).val()==0) {
            $(".netprofitcompareperiodselect").hide();
        } else {
            $(".netprofitcompareperiodselect").show();
        }
        rebuild_charttable();
    });
    $("#strweek").unbind('change').change(function () {
        rebuild_charttable();
    });
    $("#endweek").unbind('change').change(function () {
        rebuild_charttable();
    });
}

function init_netprofit_areacontent() {
    $(".expandnetprofittableview").unbind('click').click(function () {
        $(".netprofitviewdata").css('max-height','546px');
        $(".expandnetprofittableview").hide();
        $(".collapsenetprofittableview").show();
        leftmenu_alignment();
    })
    $(".collapsenetprofittableview").unbind('click').click(function () {
        $(".netprofitviewdata").css('max-height','273px');
        $(".collapsenetprofittableview").hide();
        $(".expandnetprofittableview").show();
        leftmenu_alignment();
    });
    $("div.netprofitheadocheck").unbind('click').click(function () {
        var viewtype = $(this).data('viewtype');
        $(".netprofitheadocheck").removeClass('active');
        $(".netprofitheadocheck").empty().html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
        $(".netprofitheadocheck[data-viewtype='"+viewtype+"']").addClass('active');
        $(".netprofitheadocheck[data-viewtype='"+viewtype+"']").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
        $("#netprofitviewtype").val(viewtype);
        init_netprofitpage();
    });
    $("#netreportsortorder").unbind('change').change(function () {
        init_netprofitpage();
    });
    $("#weekselectfrom").unbind('change').change(function () {
        init_netprofitpage();
    })
    $("#weekselectuntil").unbind('change').change(function () {
        init_netprofitpage();
    });
}