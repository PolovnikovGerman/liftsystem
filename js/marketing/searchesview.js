function init_searches_view() {
    // Build init display
    init_searches_results();
    // Build weekly results
    view_daily_view();
    // calendar_init();
    $("#custom_bgn").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("#custom_end").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    leftmenu_alignment();
}

function view_daily_view() {
    var params = new Array();
    params.push({name: 'brand', value: $("#searchesbrand").val()});
    params.push({name: 'year', value: $("#searchdayyear").val()});
    params.push({name: 'minyear', value: $("#searchdayyearmin").val()});
    params.push({name: 'maxyear', value: $("#searchdayyearmax").val()});
    var url='/marketing/searches_daily';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (parseInt(response.data.total) > 25) {
                $(".searchesdailydata").removeClass('short');
            } else {
                $(".searchesdailydata").addClass('short');
            }
            $(".searchesdailydata").empty().html(response.data.content);
            $(".dailysearchpaginator").find('div.navigateprev').removeClass('active');
            $(".dailysearchpaginator").find('div.navigatenext').removeClass('active');
            $(".dailysearchpaginator").find('div.navigatelabel').empty().html(response.data.label);
            if (parseInt(response.data.prev)==1) {
                $(".dailysearchpaginator").find('div.navigateprev').addClass('active');
            }
            if (parseInt(response.data.next)==1) {
                $(".dailysearchpaginator").find('div.navigatenext').addClass('active');
            }
            init_searches_manage();
            $("#loader").hide();
            leftmenu_alignment();
            jQuery.balloon.init();
        } else {
            show_error(response);
        }
    },'json');
}

function init_searches_results() {
    // Count
    var params = new Array();
    var dispperiod = $("input[name='searchperiodradio']:checked").val();
    params.push({name: 'display_option', value: $("input[name='searchdisplatradio']:checked").val()});
    params.push({name: 'display_period', value: dispperiod});
    params.push({name: 'brand', value: $("#searchesbrand").val()});
    if (dispperiod=='month') {
        params.push({name: 'month', value: $(".searchmonthsselect").val()});
    } else if (dispperiod=='year') {
        params.push({name: 'year', value: $(".searchyearselect").val()});
    } else if (dispperiod=='custom') {
        params.push({name: 'd_bgn', value: $("#custom_bgn").val()});
        params.push({name: 'd_end', value: $("#custom_end").val()});
    }
    var url='/marketing/searches_count';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#searcheswordtotal").val(parseInt(response.data.keyword));
            $("#searchesiptotal").val(parseInt(response.data.ipaddr));
            $("#searcheswordpage").val(0);
            $("#searchesippage").val(0);
            $(".searcheskeywordshead").find('div.title').empty().html(response.data.keyword_title);
            $(".searchesipaddresshead").find('div.title').empty().html(response.data.ipaddr_title);
            view_keywords_content();
            view_ipaddress_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function view_keywords_content() {
    var params = new Array();
    var dispperiod = $("input[name='searchperiodradio']:checked").val();
    params.push({name: 'display_option', value: $("input[name='searchdisplatradio']:checked").val()});
    params.push({name: 'display_period', value: dispperiod});
    params.push({name: 'brand', value: $("#searchesbrand").val()});
    params.push({name: 'total', value: $("#searcheswordtotal").val()});
    params.push({name: 'page', value: $("#searcheswordpage").val()});
    if (dispperiod=='month') {
        params.push({name: 'month', value: $(".searchmonthsselect").val()});
    } else if (dispperiod=='year') {
        params.push({name: 'year', value: $(".searchyearselect").val()});
    } else if (dispperiod=='custom') {
        params.push({name: 'd_bgn', value: $("#custom_bgn").val()});
        params.push({name: 'd_end', value: $("#custom_end").val()});
    }
    $("#loader").show();
    var url = '/marketing/searches_keywords';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".searcheskeywordsdata").empty().html(response.data.content);
            $(".keywordspaginator").find('div.navigateprev').removeClass('active');
            $(".keywordspaginator").find('div.navigatenext').removeClass('active');
            if (parseInt(response.data.prev)==1) {
                $(".keywordspaginator").find('div.navigateprev').addClass('active');
            }
            if (parseInt(response.data.next)==1) {
                $(".keywordspaginator").find('div.navigatenext').addClass('active');
            }
            $(".keywordspaginator").find('div.navigatelabel').empty().html(response.data.label);
            init_searches_manage();
            $("#loader").hide();
            leftmenu_alignment();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}
function view_ipaddress_content() {
    var params = new Array();
    var dispperiod = $("input[name='searchperiodradio']:checked").val();
    params.push({name: 'display_option', value: $("input[name='searchdisplatradio']:checked").val()});
    params.push({name: 'display_period', value: dispperiod});
    params.push({name: 'brand', value: $("#searchesbrand").val()});
    params.push({name: 'total', value: $("#searchesiptotal").val()});
    params.push({name: 'page', value: $("#searchesippage").val()});
    if (dispperiod=='month') {
        params.push({name: 'month', value: $(".searchmonthsselect").val()});
    } else if (dispperiod=='year') {
        params.push({name: 'year', value: $(".searchyearselect").val()});
    } else if (dispperiod=='custom') {
        params.push({name: 'd_bgn', value: $("#custom_bgn").val()});
        params.push({name: 'd_end', value: $("#custom_end").val()});
    }
    $("#loader").show();
    var url = '/marketing/searches_ipaddress';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".searchesipaddressdata").empty().html(response.data.content);
            $(".ipaddresspaginator").find('div.navigateprev').removeClass('active');
            $(".ipaddresspaginator").find('div.navigatenext').removeClass('active');
            if (parseInt(response.data.prev)==1) {
                $(".ipaddresspaginator").find('div.navigateprev').addClass('active');
            }
            if (parseInt(response.data.next)==1) {
                $(".ipaddresspaginator").find('div.navigatenext').addClass('active');
            }
            $(".ipaddresspaginator").find('div.navigatelabel').empty().html(response.data.label);
            init_searches_manage();
            $("#loader").hide();
            leftmenu_alignment();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_searches_manage() {
    // Display Options
    // All
    $("#searchdisplayall").unbind('click').click(function (){
        $(".displayoptionarea").removeClass('active');
        $(".displayoptionarea[data-option='All']").addClass('active');
        $(".displaycustomresult").hide();
        init_searches_results();
    });
    // Positive
    $("#searchdisplaypositiv").unbind('click').click(function (){
        $(".displayoptionarea").removeClass('active');
        $(".displayoptionarea[data-option='Positiv']").addClass('active');
        $(".displaycustomresult").hide();
        init_searches_results();
    });
    // Negative
    $("#searchdisplaynegativ").unbind('click').click(function (){
        $(".displayoptionarea").removeClass('active');
        $(".displayoptionarea[data-option='Negativ']").addClass('active');
        $(".displaycustomresult").hide();
        init_searches_results();
    })
    // Period
    // Today
    $("#searchtoday").unbind('click').click(function (){
        $(".displayperiodarea").removeClass('active');
        $(".searches_customdates").removeClass('active');
        $(".searchmonthsselect").prop('disabled',true);
        $(".customdateinpt").prop('disabled',true);
        $(".searchyearselect").prop('disabled',true);
        $(".displayperiodarea[data-period='day']").addClass('active');
        $(".displaycustomresult").hide();
        init_searches_results();
    });
    // Week
    $("#searchweek").unbind('click').click(function (){
        $(".displayperiodarea").removeClass('active');
        $(".searches_customdates").removeClass('active');
        $(".searchmonthsselect").prop('disabled',true);
        $(".customdateinpt").prop('disabled',true);
        $(".searchyearselect").prop('disabled',true);
        $(".displayperiodarea[data-period='week']").addClass('active');
        $(".displaycustomresult").hide();
        init_searches_results();
    });
    // Month
    $("#searchmonth").unbind('click').click(function (){
        $(".displayperiodarea").removeClass('active');
        $(".searches_customdates").removeClass('active');
        $(".searchmonthsselect").prop('disabled',false);
        $(".customdateinpt").prop('disabled',true);
        $(".searchyearselect").prop('disabled',true);
        $(".displayperiodarea[data-period='month']").addClass('active');
        $(".displaycustomresult").show();
    })
    // Year
    $("#searchyear").unbind('click').click(function (){
        $(".displayperiodarea").removeClass('active');
        $(".searches_customdates").removeClass('active');
        $(".searchmonthsselect").prop('disabled',true);
        $(".customdateinpt").prop('disabled',true);
        $(".searchyearselect").prop('disabled',false);
        $(".displayperiodarea[data-period='year']").addClass('active');
        $(".displaycustomresult").show();
    });
    // Custom
    $("#searchcustom").unbind('click').click(function (){
        $(".displayperiodarea").removeClass('active');
        $(".searches_customdates").addClass('active');
        $(".searchmonthsselect").prop('disabled',true);
        $(".customdateinpt").prop('disabled',false);
        $(".searchyearselect").prop('disabled',true);
        $(".displaycustomresult").show();
    })
    $(".searchyearselect").unbind('change').change(function (){
        $(".displaycustomresult").show();
    })
    $(".searchmonthsselect").unbind('change').change(function (){
        $(".displaycustomresult").show();
    });
    // Keyword pagination
    // Prev
    $(".keywordspaginator").find("div.navigateprev").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var page = parseInt($("#searcheswordpage").val());
            page = page -1;
            if (page < 0) {
                page = 0;
            }
            $("#searcheswordpage").val(page);
            view_keywords_content();
        }
    })
    // Next
    $(".keywordspaginator").find("div.navigatenext").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var page = parseInt($("#searcheswordpage").val());
            page = page + 1;
            $("#searcheswordpage").val(page);
            view_keywords_content();
        }
    });
    // IP address pagination
    // Prev
    $(".ipaddresspaginator").find('div.navigateprev').unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var page = parseInt($("#searchesippage").val());
            page = page - 1;
            if (page < 0) {
                page = 0;
            }
            $("#searchesippage").val(page);
            view_ipaddress_content();
        }
    });
    // Next
    $(".ipaddresspaginator").find('div.navigatenext').unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var page = parseInt($("#searchesippage").val());
            page = page + 1;
            $("#searchesippage").val(page);
            view_ipaddress_content();
        }
    });
    // Daily results navigate
    // prev
    $(".dailysearchpaginator").find('div.navigateprev').unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var year = parseInt($("#searchdayyear").val());
            var maxyear = parseInt($("#searchdayyearmax").val());
            year = year + 1;
            if (year > maxyear) {
                year = maxyear;
            }
            $("#searchdayyear").val(year);
            view_daily_view();
        }
    });
    // Next
    $(".dailysearchpaginator").find('div.navigatenext').unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var year = parseInt($("#searchdayyear").val());
            var minyear = parseInt($("#searchdayyearmin").val());
            year = year - 1;
            if (year < minyear) {
                year = minyear;
            }
            $("#searchdayyear").val(year);
            view_daily_view();
        }
    });
    // Show results
    $(".displaycustomresult").unbind('click').click(function(){
        $(".displaycustomresult").hide();
        init_searches_results();
    });
}