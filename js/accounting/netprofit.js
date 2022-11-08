function init_netprofit_area() {
    init_netprofitpage();
    rebuild_charttable();
    init_expenses_details('ads');
    init_expenses_details('upwork');
    init_expenses_details('w9work');
    init_expenses_details('discretionary');
}

function init_netprofitpage() {
    var url='/accounting/netprofitdat';
    var radio = $("#netprofitviewtype").val();
    var params=new Array();
    params.push({name: 'type', value:$("select#but-reportview").val()});
    params.push({name: 'radio', value: radio});
    if ($("input.allweekschoice").length>0) {
        params.push({name: 'fromweek', value: $("select#weekselectfrom").val()});
        params.push({name: 'untilweek', value: $("select#weekselectuntil").val()});
    } else {
        params.push({name: 'fromweek', value: ''});
        params.push({name: 'untilweek', value: ''});
    }
    params.push({name: 'order_by', value: $("select#netreportsortorder").val()});
    params.push({name: 'limitshow', value :$("input#limitweekshow").val()});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.netprofitviewdata").empty().html(response.data.content);
            $("div.netprofit-running").empty().html(response.data.total_view);
            // init_netprofit_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function rebuild_charttable() {
    var params=new Array();
    params.push({name: 'compareweek', value: $("select.weektotalsviewtype").val()});
    params.push({name: 'weekbgn', value: $("select#strweek").val()});
    params.push({name: 'weekend', value: $("select#endweek").val()});
    params.push({name: 'paceincome', value: $("input#projincome").val()});
    params.push({name: 'paceexpense', value: $("input#projexpence").val()});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
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
        } else {
            show_error(response);
        }
    },'json');
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
}