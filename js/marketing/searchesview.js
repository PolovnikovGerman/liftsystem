function init_searches_view() {
    // Build init display
    init_searches_results();
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
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}
function view_ipaddress_content() {

}

function init_searches_manage() {
    $("#searchdisplayall").unbind('click').click(function (){
        $(".displayoptionarea").removeClass('active');
        $(".displayoptionarea[data-option='All']").addClass('active');
        $(".displaycustomresult").hide();
        init_searches_results();
    });
    // Year
    $("#searchyear").unbind('click').click(function (){
        $(".displayperiodarea").removeClass('active');
        $(".searches_customdates").removeClass('active');
        $(".displaycustomresult").show();
    });
    $(".displaycustomresult").unbind('click').click(function(){
        init_searches_results();
    })
}