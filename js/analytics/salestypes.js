function salestype_report_init() {
    $("div.salesgoaledit").unbind('click').click(function(){
        var goal=$(this).data('goal');
        edit_salestype_goal(goal);
    });
    $("div.salesmonthview").unbind('click').click(function(){
        var month=$(this).data('month');
        var year=$(this).data('year');
        var saletype=$(this).data('saletype');
        show_monthsalesdetails(month, year, saletype);
    });
    $("div.yeardata").find("div.magnifying").qtip({

    });
    $("div.yeardata").find("div.magnifying").bt({
        ajaxCache: false,
        fill: '#FFFFFF',
        /* trigger: 'click', */
        height: '270px',
        width: '359px',
        ajaxPath: ["$(this).data('diffurl')"]
    });
    $("div.yeardata").find("div.total").bt({
        /* trigger: 'click', */
        fill: '#FFFFFF',
        cornerRadius: 10,
        width: 217,
        padding: 10,
        strokeWidth: '2',
        positions: "most",
        strokeStyle: '#000000',
        strokeHeight: '18',
        cssClass: 'white_tooltip',
        cssStyles: {
            color: '#000000'
        }
    });
    // Show / hide to see reps for custom shaped
    $("div.rowshowhide").unbind('click').click(function(){
        var show=0;
        if ($(this).hasClass('showdata')==true) {
            show=1;
            $(this).empty().html('<i class="fa fa-minus-circle" aria-hidden="true"></i>');
        } else {
            $(this).empty().html('<i class="fa fa-plus-circle" aria-hidden="true"></i>');
        }
        var year=$(this).data('year');
        if (show==1) {
            $("div.rowshowhide[data-year='"+year+"']").removeClass('showdata').addClass('hidedata');
        } else {
            $("div.rowshowhide[data-year='"+year+"']").removeClass('hidedata').addClass('showdata');
        }

        if (show==1) {
            $("div.yeardata[data-year='"+year+"']").addClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.total').addClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.total').find('div.datayear').show();
            $("div.yeardata[data-year='"+year+"']").find('div.totaldiff').addClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.totaldiff').find('div.datayear').show();
            $("div.yeardata[data-year='"+year+"']").find('div.map').find('div.datayear').show();
            $("div.yeardata[data-year='"+year+"']").find('div.monthdataarea').addClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.monthdataarea').find('div.month').addClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.monthdataarea').find('div.month').find('div.datayear').show();
            $("div.pacehitdata[data-year='"+year+"']").addClass('customs');
            $("div.pacehitdata[data-year='"+year+"']").find('div.datayear').show();
            $("div.pacehitdiffdata[data-year='"+year+"']").addClass('customs');
            $("div.pacehitdiffdata[data-year='"+year+"']").find('div.datayear').show();
        } else {
            $("div.yeardata[data-year='"+year+"']").removeClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.total').removeClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.total').find('div.datayear').hide();
            $("div.yeardata[data-year='"+year+"']").find('div.totaldiff').removeClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.totaldiff').find('div.datayear').hide();
            $("div.yeardata[data-year='"+year+"']").find('div.map').find('div.datayear').hide();
            $("div.yeardata[data-year='"+year+"']").find('div.monthdataarea').removeClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.monthdataarea').find('div.month').removeClass('customs');
            $("div.yeardata[data-year='"+year+"']").find('div.monthdataarea').find('div.month').find('div.datayear').hide();
            $("div.pacehitdata[data-year='"+year+"']").removeClass('customs');
            $("div.pacehitdata[data-year='"+year+"']").find('div.datayear').hide();
            $("div.pacehitdiffdata[data-year='"+year+"']").removeClass('customs');
            $("div.pacehitdiffdata[data-year='"+year+"']").find('div.datayear').hide();
        }
        salestype_report_init();
    });
    $("select.yearselect").unbind('change').change(function(){
        var type = $(this).data('type');
        var profit=$(this).data('profit');
        show_difference(type, profit);
    });
    $("div.quaterdata").find("span.quaterlabel").bt({
        ajaxCache: false,
        fill: '#FFFFFF',
        /* trigger: 'click', */
        height: '270px',
        width: '259px',
        ajaxPath: ["$(this).data('calcurl')"]
    });
}

function edit_salestype_goal(goal) {
    var url="/reports/salesgoal_editform";
    $.post(url, {'goal': goal}, function(response){
        if (response.errors=='') {
            show_popup('profitdategoaledit');
            $("div#pop_content").empty().html(response.data.content);
            $("a#popupContactClose").unbind('click').click(function(){
                disablePopup();
            })
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
    var url="/reports/salesgoal_changeparam";
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
    var url="/reports/salesgoal_save";
    $.post(url,{},function(response){
        if (response.errors=='') {
            $("div#"+response.data.area).empty().html(response.data.content);
            disablePopup();
            salestype_report_init();
        } else {
            show_error(response);
        }
    },'json');
}

// Show month details
function show_monthsalesdetails(month, year, saletype) {
    var params=new Array();
    params.push({name: 'month', value: month});
    params.push({name: 'year', value: year});
    params.push({name: 'saletype', value: saletype});
    var url="/reports/sales_month_details";
    $.post(url, params, function(response){
        if (response.errors=='') {
            show_popup('salesmonthdetails');
            $("div#pop_content").empty().html(response.data.content);
            if (response.data.countdata<20) {
                $("div.dataarea").children('div.datarow').css('width','620px');
            }
            $("a#popupContactClose").unbind('click').click(function(){
                disablePopup();
            });
        } else {
            show_error(response);
        }
    },'json');
}

// Show differemce
function show_difference(type, profit) {
    var compare =parseInt($("select[data-type='"+type+"'][data-year='compare']").val());
    var compareto =parseInt($("select[data-type='"+type+"'][data-year='to']").val());
    if (compareto<=compare) {
        alert('Error in select years for compare')
    } else {
        var params=new Array();
        params.push({'name': 'type', value: type});
        params.push({name: 'profit', value: profit});
        params.push({'name': 'compare', value: compare});
        params.push({'name': 'to', value: compareto});
        var url='/reports/salestype_showdifference';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".differences_area[data-type='"+type+"']").empty().html(response.data.content);
                // Init select
                $("select.yearselect").unbind('change').change(function(){
                    var type = $(this).data('type');
                    var profit=$(this).data('profit');
                    show_difference(type, profit);
                });
            } else {
                show_error(response);
            }
        },'json');
    }
}