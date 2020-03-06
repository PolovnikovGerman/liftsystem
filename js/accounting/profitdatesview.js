var slidermargin;
$(document).ready(function(){
    slidermargin=parseInt($("div.profitdatatotalarea").css('margin-left'));
})

function init_profit_date() {
    show_curent_calend();
    $(".monthselect").unbind('click').click(function(){
        change_month(this);
    })
    // show_curent_calend();
    // $("div.profitdatatotalarea").css('margin-left','-150px');
    $("div.tab2-cont").unbind('click').click(function(){
        change_year(this);
    });
    init_profitdate_slider();
    $("div.showhidegrowth").unbind('click').click(function(){
        var growview=$("input#showgrowth").val();
        if (growview==0) {
            $("input#showgrowth").val(1);
        } else {
            $("input#showgrowth").val(0);
        }
        change_profitcalend_slider();
    });
    $("div.finance_date_filter").find("select.profitstatview").unbind('change').change(function() {
        show_filter_months();
    });
    $("div.finance_month_filter").find("select.startdate").unbind('change').change(function () {
        filter_profitcalend_slider();
    });
    $("div.finance_month_filter").find("select.enddate").unbind('change').change(function () {
        filter_profitcalend_slider();
    })
}

function change_profitcalend_slider() {
    var viewtype=$("div.finance_date_filter").find("select.filter").val();
    var params=new Array();
    params.push({name: 'showgrowth', value: $("input#showgrowth").val()});
    var url;
    if (viewtype==0) {
        url="/accounting/profitdate_showgrowth";
    } else {
        params.push({name: 'startdate', value: $("select.startdate").val()});
        params.push({name: 'enddate', value: $("select.enddate").val()});
        url="/accounting/filter_profitdate_showgrowth";
    }

    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.showhidegrowth").empty().html(response.data.label);
            // $("input#showgrowth").val(response.data.newval);
            $("div.profitdatatotalarea").empty().html(response.data.content);
            $("div.profitdatatotalarea").css('width',response.data.slider_width).css('margin-left', response.data.margin);
            slidermargin=parseInt($("div.profitdatatotalarea").css('margin-left'));
            init_profitdate_slider();
        } else {
            show_error(response);
        }
    },'json');
}

function filter_profitcalend_slider() {
    var params=new Array();
    params.push({name: 'showgrowth', value: $("input#showgrowth").val()});
    params.push({name: 'startdate', value: $("select.startdate").val()});
    params.push({name: 'enddate', value: $("select.enddate").val()});
    var url="/finance/filter_profitdate_showgrowth";

    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.showhidegrowth").empty().html(response.data.label);
            // $("input#showgrowth").val(response.data.newval);
            $("div.profitdatatotalarea").empty().html(response.data.content);
            $("div.profitdatatotalarea").css('width',response.data.slider_width).css('margin-left', response.data.margin);
            slidermargin=parseInt($("div.profitdatatotalarea").css('margin-left'));
            init_profitdate_slider();
        } else {
            show_error(response);
        }
    },'json');
}

function change_year(obj) {
    var newobj=obj.id;
    var newyear=newobj.substr(4);
    $("input#cur_year").val(newyear);
    /* find current class */
    var activ='';
    var activyear='';
    if ($("div.style-text1").length==0) {
        activ=$("div.style-text3").parent('div').prop('id');
        activyear=activ.substr(4);
    } else {
        activ=$("div.style-text1").parent('div').prop('id');
        activyear=activ.substr(4);
    }

    var maxyear=$("input#maxyear").val();
    /* Change text property */

    if (activyear==maxyear) {
        $("div#"+activ).removeClass('tab1-cont').addClass('tab2-cont');
        $("div#"+activ).children('div').removeClass('style-text1').addClass('style-text2-nonactive');
    } else {
        $("div#"+activ).removeClass('tab3-cont').addClass('tab2-cont');
        $("div#"+activ).children('div').removeClass('style-text3').addClass('style-text2-nonactive');
    }
    /* Add new year class */

    if (newyear==maxyear) {
        $("div#"+newobj).removeClass('tab2-cont').addClass('tab1-cont');
        $("div#"+newobj).children('div').removeClass('style-text2-nonactive').addClass('style-text1');
    } else {
        $("div#"+newobj).removeClass('tab2-cont').addClass('tab3-cont');
        $("div#"+newobj).children('div').removeClass('style-text2-nonactive').addClass('style-text3');
    }
    /* Get new links on months */
    var url="/finance/profitdate_months";
    $.post(url,{'year': newyear},function(response){
        if (response.errors=='') {
            $("div.profitdate_months").empty().html(response.data.content);
            $("input#cur_month").val(response.data.min_month);
            show_curent_calend();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}

function show_curent_calend() {
    var params = new Array();
    params.push({name: 'month', value: $("input#cur_month").val()});
    params.push({name: 'year', value: $("input#cur_year").val()});
    params.push({name: 'brand', value: $("#profitcalendarbrand").val()});
    var url="/accounting/profit_calendar";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#totalsbymonth").empty().html(response.data.monthtotal);
            $("#tableinfotab2").empty().html(response.data.content);
            $(".profitdate-selected-monthname").empty().html(response.data.monthname);
            $("div.cell-dayinfo").each(function(){
                /* $("div#"+$(this).prop('id')).bt({
                    trigger: 'click',
                    ajaxCache: false,
                    width: '762px',
                    ajaxPath: ["$(this).attr('href')"]
                }); */
            });
            // init_profit_date();
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_month(obj) {
    var month=obj.id.substr(5);
    $("input#cur_month").val(month);
    show_curent_calend();

}

function init_profitdate_slider() {
    $(".profitcalend_slidermanage.left").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var offset=130;
            profitdate_slider_move(offset);
        }
    });
    $(".profitcalend_slidermanage.right").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var offset=-130;
            profitdate_slider_move(offset);
        }
    });
    $("div.editgoal").unbind('click').click(function(){
        var year=$(this).data('year');
        edit_goalsvalue(year);
    });
}

function profitdate_slider_move(offset) {
    var margin=parseInt($("div.profitdatatotalarea").css('margin-left'));
    var slwidth=parseInt($("div.profitdatatotalarea").css('width'));
    var slshow=parseInt($("div#weekdays-totals").css('max-width'));
    var newmargin=(margin+offset);
    if (newmargin>=0) {
        newmargin=0;
        $(".profitcalend_slidermanage.left").removeClass('active');
    } else {
        $(".profitcalend_slidermanage.left").addClass('active');
    }
    $("div.profitdatatotalarea").animate({marginLeft:newmargin+'px'},'slow');
    // if ((slwidth+newmargin)>=slshow) {
    if ((slwidth+newmargin)>920) {
        $(".profitcalend_slidermanage.right").addClass('active');
    } else {
        $("div.profitdatatotalarea").animate({marginLeft:slidermargin+'px'},'quick');
        $(".profitcalend_slidermanage.right").removeClass('active');
    }
    init_profitdate_slider();
}

function edit_goalsvalue(year) {
    var url="/finance/edit_profitdata_goals";
    $.post(url,{'year': year}, function(response){
        if (response.errors=='') {
            show_popup('profitdategoaledit');
            $("div#pop_content").empty().html(response.data.content);
            $("input.goaleditinput").unbind('change').change(function(){
                var fld=$(this).prop('id');
                var newval=parseFloat($(this).val());
                edit_goalparam(fld, newval);
            });
            $("div.goaleditsave").unbind('click').click(function(){
                save_profitdategoal();
            })
        } else {
            show_error(response);
        }
    },'json');
}

function edit_goalparam(fld, newval) {
    var url="/finance/change_profitdata_goals";
    $.post(url, {'field': fld,'newval': newval}, function(response){
        if (response.errors=='') {
            $("div.goaleditvalue[data-fld='goalavgrevenue']").empty().html(response.data.goalavgrevenue);
            $("div.goaleditvalue[data-fld='goalavgprofit']").empty().html(response.data.goalavgprofit);
            $("div.goaleditvalue[data-fld='goalavgprofitperc']").empty().html(response.data.goalavgprofitperc);
        } else {
            show_error(response);
        }
    },'json');
}

function save_profitdategoal() {
    var url="/finance/save_profitdata_goals";
    var params=new Array();
    params.push({name: 'showgrowth', value: $("input#showgrowth").val()});
    $.post(url,params,function(response){
        if (response.errors=='') {
            disablePopup();
            $("div.profitdatatotalarea").empty().html(response.data.content);
            $("div.profitdatatotalarea").css('width',response.data.slider_width).css('margin-left', response.data.margin);
            init_profitdate_slider();
        } else {
            show_error(response);
        }
    },'json');
}

function show_filter_months() {
    var show = $("select.filter option:selected").val();
    if (show == 1) {
        $("div.finance_month_filter").show();
        filter_profitcalend_slider();
        // $("div.profitmonthgoal").show();
        // $("div.year-totals").hide();
    } else {
        $("div.finance_month_filter").hide();
        change_profitcalend_slider();
        // $("div.profitmonthgoal").hide();
        // $("div.year-totals").show();
    }
}
