function init_netprofit_area() {
    init_netprofitpage();
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
            $("div.content_table_netprofit").empty().html(response.data.content);
            // init_netprofit_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}