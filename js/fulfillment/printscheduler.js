function init_printscheduler_content() {
    init_printscheduler_past();
    init_printscheduler_current();
    leftmenu_alignment();
}

function init_printscheduler_past() {
    var params = new Array();
    params.push({name: 'brand', value: $("#printschbrand").val()});
    params.push({name: 'calendar', value: 'past'});
    var url = '/printscheduler/get_calendar';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pastdue-body").empty().html(response.data.content);
            $("#loader").hide();
            init_pastdueorders_content();
        } else {
            show_error(response);
            $("#loader").hide();
        }
    },'json');
}

function init_printscheduler_current() {
    var params = new Array();
    params.push({name: 'brand', value: $("#printschbrand").val()});
    params.push({name: 'calendar', value: 'ontime'});
    var url = '/printscheduler/get_calendar';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#printschcurrentbody").empty().html(response.data.content);
            $("#loader").hide();
            init_ontimeorders_content();
        } else {
            show_error(response);
            $("#loader").hide();
        }
    },'json');
}

function init_pastdueorders_content() {
    $("#printschpastorderview").unbind('click').click(function (){
       var open = 1;
       if ($("#printschpastopen").val()==1) {
           open = 0;
       }
       if (open==0) {
           $(".pastdue-body").hide();
           $(".pastdue").removeClass('open');
           $("#printschpastorderview").empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-down-white.svg">');
           $("#printschpastopen").val(0);
       } else {
           $(".pastdue-body").show();
           $(".pastdue").addClass('open');
           $("#printschpastorderview").empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-up-white.svg">');
           $("#printschpastopen").val(1);
       }
    });
}

function init_ontimeorders_content() {
    $("#printschcurrentorderview").unbind('click').click(function (){
        var open = 1;
        if ($("#printschontimeopen").val()==1) {
            open = 0;
        }
        if (open==0) {
            $("#printschcurrentbody").hide();
            $(".current").removeClass('open');
            $("#printschcurrentorderview").empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-down-white.svg">');
            $("#printschontimeopen").val(0);
        } else {
            $("#printschcurrentbody").show();
            $(".current").addClass('open');
            $("#printschcurrentorderview").empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-up-white.svg">');
            $("#printschontimeopen").val(1);
        }
    });
    $(".day-name-arrow").unbind('click').click(function (){
        var printdate = $(this).data('orderday');
        var open = 1;
        if ($(this).hasClass('open')) {
            open = 0;
        }
        if (open==0) {
            $(".day-block-open[data-orderday='"+printdate+"']").addClass('hide');
            $(".current-table[data-orderday='"+printdate+"']").hide();
            $(".day-name-arrow[data-orderday='"+printdate+"']").removeClass('open').addClass('closed').empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-down-white.svg">');
        } else {
            $(".day-block-open[data-orderday='"+printdate+"']").removeClass('hide');
            $(".current-table[data-orderday='"+printdate+"']").show();
            $(".day-name-arrow[data-orderday='"+printdate+"']").removeClass('closed').addClass('open').empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-up-white.svg">');
        }
    });
    $(".day-arrow-open").unbind('click').click(function (){
        var printdate = $(this).data('orderday');
        var params = new Array();
        params.push({name: 'printdate', value: printdate});
        params.push({name: 'brand', value: $("#printschbrand").val()});
        var url = '/printscheduler/dayscheduler';
        $("#loader").show();
        $.post(url, params, function (response){
           if (response.errors=='') {
               $(".right-block").empty().html(response.data.content);
               $(".day-block-open").addClass('hide');
               $(".day-block-open[data-orderday='"+printdate+"']").removeClass('hide').addClass('active');
               $(".current-table").hide();
               $(".day-name-arrow").removeClass('open').addClass('closed').empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-down-white.svg">');
               $(".day-block-open[data-orderday='"+printdate+"']").find('div.day-arrow-open').empty().html('<img class="long-arrow-right" src="/img/printscheduler/long-arrow-right-white.svg">');
               $(".current-table[data-orderday='"+printdate+"']").show();
               $(".day-name-arrow[data-orderday='"+printdate+"']").removeClass('closed').addClass('open').empty().html('<img class="chevron-up" src="/img/printscheduler/chevron-up-white.svg">');
               $("#loader").hide();
               $(".day-block-open.hide").find('div.day-arrow-open').unbind('click');
               $(".day-block-open.active").find('div.day-arrow-open').unbind('click').click(function (){
                   $(".right-block").empty();
                   init_printscheduler_current();
                   leftmenu_alignment();
               })
               init_printscheduler_dayview();
           }  else {
               show_error(response);
               $("#loader").hide();
           }
        },'json');
    });
}

function init_printscheduler_dayview() {
    $(".stock-done-checkbox").unbind('change').change(function (){
        var order = $(this).data('order');
        var params = new Array();
        params.push({name: 'order', value: order});
        params.push({name: 'brand', value: $("#printschbrand").val()});
        var url = '/printscheduler/stockdonecheck';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".ready-print-block").empty().html(response.data.content);
                $("#loader").hide();
                init_printscheduler_dayview();
            } else {
                show_error(response)
                $("#loader").hide();
            }
        },'json');
    });
    $(".ic-assign").unbind('click').click(function (){
        var order = $(this).data('order');
        $(".assign-popup[data-order='"+order+"']").show();
        init_assignprint(order);
    });
}

function init_assignprint(order) {
    $("li.assignusr").unbind('click').click(function (){
        var user = $(this).data('user');
        var params = new Array();
        params.push({name: 'order', value: order});
        params.push({name: 'user', value: user});
        params.push({name: 'brand', value: $("#printschbrand").val()});
        var url = '/printscheduler/assignprintorder';
        $("#loader").show();
        $.post(url, params, function (response){
            
        },'json');
    });
}