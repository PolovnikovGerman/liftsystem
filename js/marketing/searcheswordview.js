function init_searchkeyword_content() {
    // Change Brand
    $("#searchkeywordbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#searchkeywordbrand").val(brand);
        $("#searchkeywordbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#searchkeywordbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#searchkeywordbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        show_keywords_result();
    });
    $("#today_keywords").unbind('click').click(function(){
        show_today_keywords();
    });
    $("#week_keywords").unbind('click').click(function(){
        show_week_keywords();
    });
    $("#month_keywords").unbind('click').click(function(){
        show_month_keywords();
    });
    $("#custom_keywords").unbind('click').click(function(){
        keywordsearch_customrange();
    });
    $("#showcustomrange_keywords").unbind('click').click(function(){
        keywords_show_custom();
    });
    $("#dbgn_keywords").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("#dend_keywords").datepicker({
        autoclose: true,
        todayHighlight: true
    });

    $("#allresults").unbind('click').click(function(){
        show_keywords_result();
    })
    $("#positive").unbind('click').click(function(){
        show_keywords_result();
    })
    $("#negative").live('click').click(function(){
        show_keywords_result();
    });
    show_today_keywords();
}

function show_today_keywords() {
    $("#datarangeview_keywords").css('visibility','hidden');
    var result=-1;
    if ($("#positive").prop('checked')) {
        result=1;
    }
    if ($("#negative").prop('checked')) {
        result=0;
    }
    var params = new Array();
    params.push({name: 'result', value: result});
    params.push({name: 'period', value: 'today'});
    params.push({name: 'brand', value: $("#searchkeywordbrand").val()});
    var url='/marketing/searchkeyworddata';
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            var contwidth=343*response.data.num_cols;
            $("#keywordsearchcontent").empty().html(response.data.content).css('width',contwidth);
        } else {
            show_error(response);
        }
    }, 'json');
}

function show_week_keywords() {
    $("#datarangeview_keywords").css('visibility','hidden');
    var result=-1;
    if ($("#positive").prop('checked')) {
        result=1;
    } else if ($("#negative").prop('checked')) {
        result=0;
    }
    var params = new Array();
    params.push({name: 'result', value: result});
    params.push({name: 'period', value: 'week'});
    params.push({name: 'brand', value: $("#searchkeywordbrand").val()});
    var url='/marketing/searchkeyworddata';
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            var contwidth=343*response.data.num_cols;
            $("#keywordsearchcontent").empty().html(response.data.content).css('width',contwidth);
        } else {
            show_error(response);
        }
    }, 'json');
}

function show_month_keywords() {
    $("#datarangeview_keywords").css('visibility','hidden'); // visible
    var result=-1;
    if ($("#positive").prop('checked')) {
        result=1;
    } else if ($("#negative").prop('checked')) {
        result=0;
    }
    var params = new Array();
    params.push({name: 'result', value: result});
    params.push({name: 'period', value: 'month'});
    params.push({name: 'brand', value: $("#searchkeywordbrand").val()});
    var url='/marketing/searchkeyworddata';
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            var contwidth=343*response.data.num_cols;
            $("#keywordsearchcontent").empty().html(response.data.content).css('width',contwidth);
        } else {
            show_error(response);
        }
    }, 'json');
}

function keywordsearch_customrange() {
    $("#datarangeview_keywords").css('visibility','visible');
    $("#dbgn_keywords").val('');
    $("#dend_keywords").val('');
}

function keywords_show_custom() {
    var result=-1;
    if ($("#positive").prop('checked')) {
        result=1;
    } else if ($("#negative").prop('checked')) {
        result=0;
    }
    var params = new Array();
    params.push({name: 'result', value: result});
    params.push({name: 'period', value: 'custom'});
    params.push({name: 'd_bgn', value: $("#dbgn_keywords").val()});
    params.push({name: 'd_end', value: $("#dend_keywords").val()});
    params.push({name: 'brand', value: $("#searchkeywordbrand").val()});
    var url='/marketing/searchkeyworddata';
    $.post(url, params, function(response){
        if (response.errors=='') {
            // calculate width of div
            var contwidth=343*response.data.num_cols;
            $("#keywordsearchcontent").empty().html(response.data.content).css('width',contwidth);
        } else {
            show_error(response);
        }
    }, 'json');
}

function show_keywords_result() {
    if ($("#today_keywords").prop('checked')) {
        show_today_keywords();
    } else if ($("#week_keywords").prop('checked')) {
        show_week_keywords();
    } else if ($("#month_keywords").prop('checked')) {
        show_month_keywords();
    } else if ($("#custom_keywords").prop('checked')) {
        keywords_show_custom();
    }
}