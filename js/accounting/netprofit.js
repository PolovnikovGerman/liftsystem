function init_netprofit_area() {
    init_netprofitpage();
    rebuild_charttable();
    init_expenses_details('ads');
    init_expenses_details('upwork');
    init_expenses_details('w9work');
    init_expenses_details('discretionary');
    init_netprofit_areacontent();
}

function init_netprofitpage() {
    var url='/netprofit/netprofitdat';
    var radio = $("#netprofitviewtype").val();
    var params=new Array();
    params.push({name: 'type', value:$("select#but-reportview").val()});
    params.push({name: 'radio', value: radio});
    params.push({name: 'fromweek', value: $("select#weekselectfrom").val()});
    params.push({name: 'untilweek', value: $("select#weekselectuntil").val()});
    params.push({name: 'order_by', value: $("select#netreportsortorder").val()});
    params.push({name: 'limitshow', value :$("input#limitweekshow").val()});
    // params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    params.push({name: 'brand', value: 'ALL'});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.netprofitviewdata").empty().html(response.data.content);
            $("div.netprofit-running").empty().html(response.data.total_view);
            init_netprofit_content();
            $("#loader").hide();
            jQuery.balloon.init();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function rebuild_charttable() {
    var params=new Array();
    params.push({name: 'compareweek', value: $("select.weektotalsviewtype").val()});
    if ($("select.weektotalsviewtype").val()==0) {
        params.push({name: 'weekbgn', value: 0 });
        params.push({name: 'weekend', value: 0 });
    } else {
        params.push({name: 'weekbgn', value: $("select#strweek").val()});
        params.push({name: 'weekend', value: $("select#endweek").val()});
    }
    params.push({name: 'paceincome', value: $("input#projincome").val()});
    params.push({name: 'paceexpense', value: $("input#projexpence").val()});
    params.push({name: 'brand', value: $("#netprofitchartdatabrand").val()});
    var url="/netprofit/netprofit_charttabledata";
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
    params.push({name: 'brand', value: $("#netprofitchartdatabrand").val()});
    var url="/netprofit/netprofit_expensetable";
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
            leftmenu_alignment();
        } else {
            show_error(response);
        }
    },'json');
}

function init_netprofit_content() {
    $(".includeweek").unbind('click').click(function () {
        var profit = $(this).data('profit');
        check_week(profit);
    });
    $("div.weekname.editdata").unbind('click').click(function(){
        var profit=$(this).data('profit');
        edit_profdat(profit);
    });
}

function check_week(profit) {
    var params=new Array();
    params.push({name: 'profit_id', value: profit});
    // params.push({name: 'brand', value: 'ALL'});
    params.push({name: 'fromweek', value: $("select#weekselectfrom").val()});
    params.push({name: 'untilweek', value: $("select#weekselectuntil").val()});
    params.push({name: 'viewtype', value: $("#netprofitviewtype").val()});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url="/netprofit/netprofit_checkweek"
    $.post(url, params, function(response){
        if (response.errors=='') {
            $(".includeweek[data-profit='"+profit+"']").empty().html(response.data.weekcheck);
            $("div.netprofit-running").empty().html(response.data.total_view);
        } else {
            show_error(response);
        }
    }, 'json');
}

function edit_profdat(profit_id) {
    var params=new Array();
    params.push({name: 'profit_id', value: profit_id});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url='/netprofit/netprofitedit';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.cell_week2").unbind('click');
            $("div.netprofit-table-data[data-profit='"+profit_id+"']").empty().html(response.data.content);
            init_netprofitdetails_edit();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function init_netprofitdetails_edit() {
    $(".but-cancel").unbind('click').click(function () {
        init_netprofitpage();
    });
    $(".but-accept").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
        var url="/netprofit/netprofit_details_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                init_netprofitpage();
                if (parseInt(response.data.refresh)===1) {
                    rebuild_charttable();
                    // Rebuild W9 Work
                    init_expenses_details('ads');
                    init_expenses_details('upwork');
                    init_expenses_details('w9work');
                    init_expenses_details('discretionary');
                    leftmenu_alignment();
                }
            } else {
                show_error(response);
            }
        },'json');
    })

    $("input.netprofitedit").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/netprofit/netprofit_details_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.btneditnetprofit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/netprofit/netprofit_purchase';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModal").find('div.modal-dialog').css('width','1186px');
                $("#pageModalLabel").empty().html(response.data.title);
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Include / exclude
    $(".includeweek_edit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/netprofit/netprofit_weekruncheck';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".includeweek_edit").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        }, 'json');
    });
}

function init_netprofitdetails_popup() {
    // Close btn
    // Save
    $("div#purchasepopupsavevalue").unbind('click').click(function(response){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
        var url="/netprofit/netprofit_details_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModal").modal('hide');
                init_netprofitpage();
                if (parseInt(response.data.refresh)===1) {
                    rebuild_charttable();
                    // Rebuild W9 Work
                    // rebuild_w9table();
                    init_expenses_details('ads');
                    init_expenses_details('upwork');
                    init_expenses_details('w9work');
                    init_expenses_details('discretionary');
                    leftmenu_alignment();
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // New category
    $("div.newcategoryaddbtn").unbind('click').click(function(){
        var params=new Array();
        var detail=$(this).data('detail');
        var category_type=$(this).data('detailtype');
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name:'details', value: $(this).data('detail')});
        params.push({name:'category', value: category_type});
        $.colorbox({
            opacity: .7,
            transition: 'fade',
            ajax: true,
            width:440,
            href: '/netprofit/profit_newcategory',
            data: params,
            onComplete: function() {
                // init_check();
                $.colorbox.resize();
                init_newnetcategory(detail, category_type);
            }
        });
    });
    // Delete Purchase details
    $("div.detailrow").find("div.deedcell").unbind('click').click(function(){
        var category_type=$(this).data('category');
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'category_type', value: category_type});
        var url="/netprofit/purchase_deletedetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (category_type=='Purchase') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='purchase']").empty().html(response.data.content);
                    $("div#purchasepopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='W9') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='w9work']").empty().html(response.data.content);
                    $("div#w9workpopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='Upwork') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='upwork']").empty().html(response.data.content);
                    $("div#upworkpopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='Ads') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='ads']").empty().html(response.data.content);
                    $("div#adspopuptotalvalue").empty().html(response.data.total);
                }
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Change category
    $("select.purchaseselect").unbind('change').change(function(){
        var category_type = $(this).data('detailtype');
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'category_type', value: category_type});
        var url="/netprofit/purchase_editdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (category_type=='Purchase') {
                    $("div#purchasepopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='W9') {
                    $("div#w9workpopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='Upwork') {
                    $("div#upworkpopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='Ads') {
                    $("div#adspopuptotalvalue").empty().html(response.data.total);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Edit Details data
    $("input.purchaseinput").unbind('change').change(function(){
        var category_type = $(this).data('detailtype');
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'category_type', value: $(this).data('detailtype')});
        var url="/netprofit/purchase_editdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (category_type=='Purchase') {
                    $("div#purchasepopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='W9') {
                    $("div#w9workpopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='Upwork') {
                    $("div#upworkpopuptotalvalue").empty().html(response.data.total);
                } else if (category_type=='Ads') {
                    $("div#adspopuptotalvalue").empty().html(response.data.total);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // add new purchase
    $("#addnewpurchasedetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'expense', value: 'Purchase'});
        var url="/netprofit/netprofit_newdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netproofpurchasearea").find("div.tablebody[data-content='purchase']").empty().html(response.data.content);
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    // add W9 Work
    $("#addneww9workdetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'expense', value: 'W9'});
        var url="/netprofit/netprofit_newdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netproofpurchasearea").find("div.tablebody[data-content='w9work']").empty().html(response.data.content);
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    // add Upwork
    $("#addnewupworkdetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'expense', value: 'Upwork'});
        var url="/netprofit/netprofit_newdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netproofpurchasearea").find("div.tablebody[data-content='upwork']").empty().html(response.data.content);
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Ads
    $("#addnewadsdetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'expense', value: 'Ads'});
        var url="/netprofit/netprofit_newdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netproofpurchasearea").find("div.tablebody[data-content='ads']").empty().html(response.data.content);
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_newnetcategory(detail, category_type) {
    $("div.newcategoryname_save").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail', value: detail});
        params.push({name: 'category', value: $("input#newcategoryvalue").val()});
        params.push({name: 'category_type', value: category_type});
        var url="/netprofit/profit_categorysave";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $.colorbox.close();
                // Restore table content
                if (category_type=='Purchase') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='purchase']").empty().html(response.data.content);
                } else if (category_type=='W9') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='w9work']").empty().html(response.data.content);
                } else if (category_type=='Upwork') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='upwork']").empty().html(response.data.content);
                } else if (category_type=='Ads') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='ads']").empty().html(response.data.content);
                }
                $("input.descript[data-detail='"+detail+"']").focus();
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
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
    $("#netprofitchartdatabrand").unbind('change').change(function(){
        rebuild_charttable();
        init_expenses_details('ads');
        init_expenses_details('upwork');
        init_expenses_details('w9work');
        init_expenses_details('discretionary');
    });
    $("select.weektotalsviewtype").unbind('change').change(function () {
        if ($(this).val()==0) {
            $(".netprofitcompareperiodselect").hide();
        } else {
            $(".netprofitcompareperiodselect").show();
        }
        rebuild_charttable();
    });
    $("#strweek").unbind('change').change(function () {
        rebuild_charttable();
    });
    $("#endweek").unbind('change').change(function () {
        rebuild_charttable();
    });
}

function init_netprofit_areacontent() {
    $(".expandnetprofittableview").unbind('click').click(function () {
        $(".netprofitviewdata").css('max-height','546px');
        $(".expandnetprofittableview").hide();
        $(".collapsenetprofittableview").show();
        leftmenu_alignment();
    })
    $(".collapsenetprofittableview").unbind('click').click(function () {
        $(".netprofitviewdata").css('max-height','273px');
        $(".collapsenetprofittableview").hide();
        $(".expandnetprofittableview").show();
        leftmenu_alignment();
    });
    $("div.netprofitheadocheck").unbind('click').click(function () {
        var viewtype = $(this).data('viewtype');
        $(".netprofitheadocheck").removeClass('active');
        $(".netprofitheadocheck").empty().html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
        $(".netprofitheadocheck[data-viewtype='"+viewtype+"']").addClass('active');
        $(".netprofitheadocheck[data-viewtype='"+viewtype+"']").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
        $("#netprofitviewtype").val(viewtype);
        init_netprofitpage();
    });
    $("#netreportsortorder").unbind('change').change(function () {
        init_netprofitpage();
    });
    $("#weekselectfrom").unbind('change').change(function () {
        init_netprofitpage();
    })
    $("#weekselectuntil").unbind('change').change(function () {
        init_netprofitpage();
    });
    // Manage categories
    $("div.expensesdata_managecategories").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'category_type', value:$(this).data('category')});
        var url="/netprofit/manage_profcategory";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModal").find('div.modal-dialog').css('width','470px');
                $("#pageModalLabel").empty().html('Edit Categories');
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_manage_categories();
            } else {
                show_error(response)
            }
        },'json');
    });
}

function init_manage_categories() {
    $("div.netprofitcategoryeditarea").find('div.datarow > div.deedcell').unbind('click').click(function(){
        // Edit
        var category_id=$(this).data('category');
        var params=new Array();
        params.push({name: 'category_id', value: category_id});
        var url="/netprofit/profcategory_edit";
        $.post(url, params, function(response){
            if (response.errors==0) {
                $("div.netprofitcategoryeditarea").find('div.datarow > div.deedcell').unbind('click');
                $("#addnewcategoryprofit").unbind('click');
                $("div.netprofitcategoryeditarea").find("div.datarow[data-category='"+category_id+"']").empty().html(response.data.content);
                manage_profit_categories();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("#addnewcategoryprofit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'category_id', value: 0});
        var url="/netprofit/profcategory_edit";
        $.post(url, params, function(response){
            if (response.errors==0) {
                $("div.netprofitcategoryeditarea").find('div.datarow > div.deedcell').unbind('click');
                $("#addnewcategoryprofit").unbind('click');
                $("div.netprofitcategoryeditarea").find('div.tablebody').prepend('<div class="datarow">'+response.data.content+'</div>');
                manage_profit_categories();
            } else {
                show_error(response);
            }
        },'json');
    });
    // manage_profit_categories();
}

function manage_profit_categories() {
    $("div.saveeditnetprofitcategory").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'category_id', value: $(this).data('category')});
        params.push({name: 'category_name', value: $("input#profitcategorynameinpt").val()});
        params.push({name: 'category_type', value: $("input#netprofitcategorytype").val()});
        var url="/netprofit/profcategory_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netprofitcategoryeditarea").find('div.tablebody').empty().html(response.data.content);
                init_manage_categories();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.canceleditnetprofitcategory").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'category_type', value: $("input#netprofitcategorytype").val()});
        var url="/netprofit/profcategory_cancel";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netprofitcategoryeditarea").find('div.tablebody').empty().html(response.data.content);
                init_manage_categories();
            } else {
                show_error(response);
            }
        },'json');
    });
}
