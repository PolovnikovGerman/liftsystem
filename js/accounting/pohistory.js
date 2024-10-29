function init_pohistory() {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    params.push({name: 'year', value: $("#pohistoryyearview").val()});
    var url = '/purchaseorders/pohistoryyear';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pohcald-tblbody").empty().html(response.data.content);
            $(".pohistinfdaytable").empty().removeClass('active');
            init_pohistory_content();
            if (parseInt($("#pohistoryslider").val())==0) {
                pohistory_build_slider();
            }
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_pohistory_content() {
    $(".pooverviewlink").unbind('click').click(function (){
        $(".pohistorydataview").hide();
        $(".pooverdataview").show();
        init_pooverview();
    });
    $(".yearbox").unbind('click').click(function (){
        var newyear = $(this).data('year');
        $(".yearbox").removeClass('active');
        $(".yearbox[data-year='"+newyear+"']").addClass('active');
        $("#pohistoryyearview").val(newyear);
        init_pohistory();
    });
    $(".historycalendday").unbind('click').click(function(){
        var dayview = $(this).data('dayweek');
        $(".historycalendday").removeClass('active');
        $(".historycalendday[data-dayweek='"+dayview+"']").addClass('active');
        $(".pohistinfdaytable").empty().removeClass('active');
        view_pohistory_details(dayview);
    })
}

function view_pohistory_details(dayview) {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    params.push({name: 'dayview', value: dayview});
    var url = '/purchaseorders/pohistorydetails';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pohistinfdaytable").empty().html(response.data.content).addClass('active');
            init_pohistory_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function pohistory_build_slider() {
    var params = new Array();
    params.push({name: 'brand', value: $("#pototalsbrand").val()});
    var url = '/purchaseorders/pohistoryslider';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".pohistory-slider-area").empty().html(response.data.content);
            if (parseInt(response.data.arrowactive)==1) {
                $(".povendor-arrowright").addClass('active');
                init_pohistory_slider();
            }
            init_pohistory_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_pohistory_slider() {
    $(".povendor-arrowright").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            $(".povendor-arrowleft").addClass('active');
            var rightoffet = parseInt($("#sliderright").val()) + 1;
            $("#sliderright").val(rightoffet);
            // New margin
            var rmargin = parseInt($(".povendor-body").css('margin-right')) + 320;
            if (rmargin >= 0) {
                rmargin = 0;
            }
            var lmargin = parseInt($(".povendor-body").css('margin-left')) - 320;
            $(".povendor-body").css('margin-right', rmargin);
            $(".povendor-body").css('margin-left', lmargin);
            if (rmargin==0) {
                $(".povendor-arrowright").removeClass('active');
            }
            init_pohistory_slider();
        }
    });
    $(".povendor-arrowleft").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            $(".povendor-arrowright").addClass('active');
            var rightoffet = parseInt($("#sliderright").val()) - 1;
            if (rightoffet < 0) {
                rightoffet = 0;
            }
            $("#sliderright").val(rightoffet);
            if (rightoffet == 0) {
                $(".povendor-arrowleft").removeClass('active');
            }
            var rmargin = parseInt($(".povendor-body").css('margin-right')) - 320;
            var lmargin = parseInt($(".povendor-body").css('margin-left')) + 320;
            if (lmargin >= 0) {
                lmargin = 0;
            }
            $(".povendor-body").css('margin-right', rmargin);
            $(".povendor-body").css('margin-left', lmargin);
            init_pohistory_slider();
        }
    });
}