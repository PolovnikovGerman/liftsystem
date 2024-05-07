function show_ipaddress_result() {
    if ($("#ipaddres_today").prop('checked')==true) {
        show_today_ipaddress();
    }
    if ($("#ipaddres_week").prop('checked')==true) {
        show_week_ipaddress();
    }
    if ($("#ipaddres_month").prop('checked')==true) {
        show_month_ipaddress();
    }
    if ($("#ipaddres_custom").prop('checked')==true) {
        ipaddress_show_custom();
    }
    init_searchipaddres_content();
}

function init_searchipaddres_content() {
    // Change Brand
    $("#ipaddres_today").unbind('click').click(function(){
        show_today_ipaddress();
    });
    $("#ipaddres_week").unbind('click').click(function(){
        show_week_ipaddress();
    });
    $("#ipaddres_month").unbind('click').click(function(){
        show_month_ipaddress();
    });
    $("#ipaddres_custom").unbind('click').click(function(){
        ipaddressearch_customrange();
    });
    $("#showcustomrange_ipaddress").unbind('click').click(function(){
        ipaddress_show_custom();
    });
    $("#dbgn_ipaddres").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("#dend_ipaddres").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    // show_today_ipaddress();
}

function show_today_ipaddress() {
    $("#datarangeview_ipaddress").css('visibility','hidden');
    var params = new Array();
    params.push({name: 'period', value: 'today'});
    params.push({name: 'brand', value: $("#searchipaddrbrand").val()});
    var url='/marketing/searchipaddresdata';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            // var contwidth=343*response.data.num_cols;
            $("#ipaddressearchcontent").empty().html(response.data.content); // .css('width',contwidth);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function show_week_ipaddress() {
    $("#datarangeview_ipaddress").css('visibility','hidden');
    var params = new Array();
    params.push({name: 'period', value: 'week'});
    params.push({name: 'brand', value: $("#searchipaddrbrand").val()});
    var url='/marketing/searchipaddresdata';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            // var contwidth=343*response.data.num_cols;
            $("#ipaddressearchcontent").empty().html(response.data.content); // .css('width',contwidth);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function show_month_ipaddress() {
    $("#datarangeview_ipaddress").css('visibility','hidden');
    var params = new Array();
    params.push({name: 'period', value: 'month'});
    params.push({name: 'brand', value: $("#searchipaddrbrand").val()});
    var url='/marketing/searchipaddresdata';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            // var contwidth=343*response.data.num_cols;
            $("#ipaddressearchcontent").empty().html(response.data.content); // .css('width',contwidth);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function ipaddressearch_customrange() {
    $("#datarangeview_ipaddress").css('visibility','visible');
    // $("#dbgn_ipaddres").val('');
    // $("#dend_ipaddres").val('');
}

function ipaddress_show_custom() {
    var params = new Array();
    params.push({name: 'period', value: 'custom'});
    params.push({name: 'd_bgn', value: $("#dbgn_ipaddres").val()});
    params.push({name: 'd_end', value: $("#dend_ipaddres").val()});
    params.push({name: 'brand', value: $("#searchipaddrbrand").val()});
    var url='/marketing/searchipaddresdata';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            // var contwidth=343*response.data.num_cols;
            $("#ipaddressearchcontent").empty().html(response.data.content); // .css('width',contwidth);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}