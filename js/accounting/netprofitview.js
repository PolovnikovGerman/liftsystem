function init_netprofit() {
    google.charts.setOnLoadCallback(drawChart);
    init_expensive_help();
    init_netprofitpage();
    // Init management of Chart Area
    $("select.weektotalsviewtype").unbind('change').change(function(){
        var showdetail=$(this).val();
        if (showdetail==1) {
            $("div.weekselectforcompare").show();
            $("div.baseperiodselectarea").hide();
            $("div.comparedataarea").css('margin-top',0);
        } else {
            $("div.weekselectforcompare").hide();
            $("div.baseperiodselectarea").show();
            $("div.comparedataarea").css('margin-top',16);
        }
        drawChart();
        rebuild_charttable();
    });
    $("select.weekcompareselect").unbind('change').change(function(){
        drawChart();
        rebuild_charttable();
    });
    $("select.selectcompareyears").unbind('change').change(function(){
        rebuild_comparetable();
    });
    // Change Project Base
    $("div.chartdataarea").find("div.inputplace").unbind('click').click(function(){
        if ($(this).hasClass('switchon')){
        } else {
            var pace=$(this).data('pace');
            var baseval=$(this).data('proj');
            $("div.inputplace[data-pace='"+pace+"']").removeClass('switchon').empty().html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
            $(this).addClass('switchon').empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
            if (pace=='income') {
                $("input#projincome").val(baseval);
            } else {
                $("input#projexpence").val(baseval);
            }
            drawChart();
            rebuild_charttable();
            rebuild_comparetable();
        }
    });
    init_charttable_content();
    // Init manage categories
    $("div.managecategory").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'category_type', value:$(this).data('category')});
        var url="/finance/manage_profcategory";
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('userdata');
                $("div#pop_content").empty().html(response.data.content);
                $("a#popupContactClose").unbind('click').click(function(){
                    disablePopup();
                });
                init_manage_categories();
            } else {
                show_error(response)
            }
        },'json');
    });
}

function init_netprofitpage() {
    var url='/finance/netprofitdat';
    var radio = $("div.radio_button input:checked").val();
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
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.content_table_netprofit").empty().html(response.data.content);
            init_netprofit_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function init_netprofit_content() {
    $("div.cell_for_debtincl").unbind('click').click(function(){
        var profit=$(this).data('debincl');
        include_debt(profit);
    });
    $("select#netreportsortorder").unbind('change').change(function () {
        init_netprofitpage();
    })
    if ($("input.allweekschoice").length > 0) {
        // $("input.allweekschoice").ezMark();
        $("select.weekselect").unbind('change').change(function () {
            // Chose a period
            $("input.allweekschoice").prop('checked', false);
            change_run_week();
        });
        $("input.allweekschoice").unbind('change').change(function () {
            // Set From && Until to NULL
            $("select.weekselect").val('');
            change_run_week();
        })
    }
    $("div.cell_gross_profit2.projprof").each(function () {
        $(this).bt({
            ajaxCache: false,
            fill: '#FFFFFF',
            cornerRadius: 10,
            width: 787,
            padding: 10,
            strokeWidth: '2',
            positions: "most",
            strokeStyle: '#000000',
            strokeHeight: '18',
            cssClass: 'orders_tooltip',
            ajaxPath: ["$(this).data('profitid')"]
        });
    });
    $("span.showallweekdata").unbind('click').click(function(){
        $("input#limitweekshow").val(0);
        init_netprofitpage();
    });

    $("div.cell_sales2").bt({
        fill: '#FFFFFF',
        cornerRadius: 10,
        width: 140,
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
    $("div.imbox.shownote").bt({
        ajaxCache: false,
        /* trigger: 'click',  */
        fill: '#FFFFFF',
        cornerRadius: 10,
        width: 578,
        padding: 10,
        strokeWidth: '2',
        positions: "most",
        strokeStyle: '#000000',
        strokeHeight: '18',
        cssClass: 'white_tooltip',
        ajaxPath: ["$(this).data('netnote')"]
    })

    $("div.cell_week2").unbind('click').click(function(){
        var profit=$(this).data('profit');
        edit_profdat(profit);
    });
    $("div.tableheaddatarow").children('div').unbind('click').click(function(){
        var sortfldid=$(this).children('div.tablesort').prop('id');
        var curfld='';
        var sortdir='';
        var sortfld='';
        var sortentity='';
        var parentobj='';
        var sortareacontent='';
        if (sortfldid=='w9categorysort' || sortfldid=='w9amountsort' || sortfldid=='w9amounpercsort') {
            sortentity='w9work';
            curfld=$("input#w9worksortfld").val();
            sortdir=$("input#w9worksortdirec").val();
        } else {
            sortentity='purchase';
            curfld=$("input#purchasesortfld").val();
            sortdir=$("input#purchasesortdirec").val();
        }

        switch (sortfldid) {
            case 'purchasecategorysort':
            case 'w9categorysort':
                sortfld='category_name';
                if (sortfld==curfld) {
                    if (sortdir=='asc') {
                        sortdir='desc';
                    } else {
                        sortdir='asc';
                    }
                } else {
                    sortdir='asc';
                }
                break;
            case 'purchaseamountsort':
            case 'w9amountsort':
                sortfld='amount';
                if (sortfld==curfld) {
                    if (sortdir=='asc') {
                        sortdir='desc';
                    } else {
                        sortdir='asc';
                    }
                } else {
                    sortdir='asc';
                }
                break;
            case 'purchaseamounpercsort':
            case 'w9amounpercsort':
                sortfld='amount_perc';
                if (sortfld==curfld) {
                    if (sortdir=='asc') {
                        sortdir='desc';
                    } else {
                        sortdir='asc';
                    }
                } else {
                    sortdir='asc';
                }
                break;
        }
        // Update hidden field
        if (sortentity=='w9work') {
            $("input#w9worksortfld").val(sortfld);
            $("input#w9worksortdirec").val(sortdir);
        } else {
            $("input#purchasesortfld").val(sortfld);
            $("input#purchasesortdirec").val(sortdir);
        }
        rebuild_w9table();
    });
    $("div.w9purchasetablearea").find('div.category_name.entered').bt({
        ajaxCache: false,
        trigger: 'click',
        fill: '#FFFFFF',
        cornerRadius: 10,
        width: 578,
        padding: 10,
        strokeWidth: '2',
        positions: "most",
        strokeStyle: '#000000',
        strokeHeight: '18',
        cssClass: 'white_tooltip',
        ajaxPath: ["$(this).attr('href')"]
    });
}

function change_run_week() {
    var url='/finance/get_weektotals';
    var params=new Array();
    var fromweek=$("select#weekselectfrom").val();
    var untilweek=$("select#weekselectuntil").val();
    if (fromweek=='' && untilweek=='') {
        $("input.allweekschoice").prop('checked',true);
    }
    params.push({name: 'fromweek', value: fromweek});
    params.push({name: 'untilweek', value: untilweek});
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.color_total").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    },'json');
}

function init_netprofit_manage() {
    $("div.saveweeknote").unbind('click').click(function(){
        save_weeknote();
    })
    $("select#but-detailed").unbind('change').change(function(){
        detail_view();
    })
    $("select#but-reportview").unbind('change').change(function(){
        $("select#netreportsortorder").val('profitdate_desc');
        report_view();
    })
    $("input#amount_profit").unbind('click').click(function() {
        init_netprofitpage();
    });
    $("input#percent_profit").unbind('click').click(function() {
        init_netprofitpage();
    });
    $("select.w9workyears").unbind('change').change(function() {
        rebuild_w9table();
    });
}

function edit_profdat(profit) {
    $("#loader").show();
    // $("div.but-detailed").html('Detailed');
    $("select#but-detailed").val('Detailed');
    var cssblock='block';
    $("div.title_table_netprofit").removeClass('compressed_title');
    $("div.line_table_netprofit").removeClass('comressed_profitrow');
    $("div.content_table_netprofit").removeClass('compressed_title');

    $("div.cell_operating").css('display',cssblock);
    $("div.cell_operating2").css('display',cssblock);
    $("div.cell_payroll").css('display',cssblock);
    $("div.cell_payroll2").css('display',cssblock);
    $("div.cell_advertising").css('display',cssblock);
    $("div.cell_advertising2").css('display',cssblock);
    $("div.cell_projects").css('display',cssblock);
    $("div.cell_projects2").css('display',cssblock);
    $("div.cell_purchases").css('display',cssblock);
    $("div.cell_purchases2").css('display',cssblock);
    var params=new Array();
    params.push({name: 'profit_id', value: profit});
    params.push({name: 'type', value: $("select#but-reportview").val()});
    var url='/finance/netprofitedit';
    $.post(url, params, function(response){
        $("#loader").hide();
        if (response.errors=='') {
            $("div.cell_week2").unbind('click');
            $("div#nerpr"+response.data.weekid).empty().html(response.data.content);
            init_netprofitdetails_edit();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_netprofitdetails_edit() {
    $("a#netprofitdetailsave").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url="/finance/netprofit_details_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                init_netprofitpage();
                if (parseInt(response.data.refresh)===1) {
                    drawChart();
                    rebuild_charttable();
                    // Rebuild W9 Work
                    rebuild_w9table();
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("a#netprofitdetailcancel").unbind('click').click(function(){
        init_netprofitpage();
    });
    $("input.netprofitdetailinpt").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/finance/netprofit_details_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Edit Purchase
    $("div.cell_purchases_edit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/finance/netprofit_purchase';
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('netproofpurchasedata');
                $("div#pop_content").empty().html(response.data.content);
                $("a#popupContactClose").unbind('click').click(function(){
                    disablePopup();
                });
                init_netprofitdetails_popup();
                // init_purchase_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $("div.imbox_edit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/finance/netprofit_purchase';
        // var url='/finance/netprofit_w9work';
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('netproofpurchasedata');
                $("div#pop_content").empty().html(response.data.content);
                $("a#popupContactClose").unbind('click').click(function(){
                    disablePopup();
                });
                init_netprofitdetails_popup();
                // init_w9work_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $("div.cell_w9work_edit").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/finance/netprofit_purchase';
        // var url='/finance/netprofit_w9work';
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('netproofpurchasedata');
                $("div#pop_content").empty().html(response.data.content);
                $("a#popupContactClose").unbind('click').click(function(){
                    disablePopup();
                });
                init_netprofitdetails_popup();
                // init_w9work_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Include / exclude
    $("#editnetdetailsdebtincl").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url='/finance/netprofit_details_debtincl';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#editnetdetailsdebtincl").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        }, 'json');
    });
}

function init_netprofitdetails_popup() {
    // Text area
    $("textarea.weeknoteedit").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("input#detailssession").val()});
        params.push({name: 'fldname', value: 'weeknote'});
        params.push({name: 'newval', value: $("textarea.weeknoteedit").val()});
        var url="/finance/netprofit_details_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Save
    $("div#purchasepopupsavevalue").unbind('click').click(function(response){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url="/finance/netprofit_details_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                disablePopup();
                init_netprofitpage();
                if (parseInt(response.data.refresh)===1) {
                    drawChart();
                    rebuild_charttable();
                    // Rebuild W9 Work
                    rebuild_w9table();
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add new purchase
    // add new purchase
    $("#addnewpurchasedetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        var url="/finance/purchase_newdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netproofpurchasearea").find("div.tablebody[data-content='purchase']").empty().html(response.data.content);
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });

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
            href: '/finance/profit_newcategory',
            data: params,
            onComplete: function() {
                // init_check();
                $.colorbox.resize();
                init_newnetcategory(detail, category_type);
            }
        });
    });
    $("select.purchaselect").unbind('change').change(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'category_type', value: 'Purchase'});
        var url="/finance/purchase_editdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#purchasepopuptotalvalue").empty().html(response.data.total);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Edit Details data
    $("input.purchaseinput").unbind('change').change(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'category_type', value: 'Purchase'});
        var url="/finance/purchase_editdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#purchasepopuptotalvalue").empty().html(response.data.total);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Delete Purchase details
    $("div.detailrow").find("div.deedcell").unbind('click').click(function(){
        var category_type=$(this).data('category');
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'category_type', value: category_type});
        var url="/finance/purchase_deletedetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (category_type=='Purchase') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='purchase']").empty().html(response.data.content);
                    $("div#purchasepopuptotalvalue").empty().html(response.data.total);
                } else {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='w9work']").empty().html(response.data.content);
                    $("div#w9workpopuptotalvalue").empty().html(response.data.total);
                }
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    // W9 Work
    $("#addneww9workdetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        var url="/finance/w9work_newdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.netproofpurchasearea").find("div.tablebody[data-content='w9work']").empty().html(response.data.content);
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.w9workselect").unbind('change').change(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'category_type', value: 'W9'});
        var url="/finance/purchase_editdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#w9workpopuptotalvalue").empty().html(response.data.total);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Edit data
    $("input.w9workinput").unbind('change').change(function(){
        var params=new Array();
        params.push({name:'session', value: $("#detailssession").val()});
        params.push({name: 'detail_id', value: $(this).data('detail')});
        params.push({name: 'fldname', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'category_type', value: 'W9'});
        var url="/finance/purchase_editdetails";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#w9workpopuptotalvalue").empty().html(response.data.total);
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
        var url="/finance/profit_categorysave";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $.colorbox.close();
                // Restore table content
                if (category_type=='Purchase') {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='purchase']").empty().html(response.data.content);
                } else {
                    $("div.netproofpurchasearea").find("div.tablebody[data-content='w9work']").empty().html(response.data.content);
                }
                $("input.descript[data-detail='"+detail+"']").focus();
                init_netprofitdetails_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function detail_view() {
    var type=$("#but-detailed").val();
    var cssblock='';
    if (type=='Condensed') {
        // $("div.but-detailed").html('Condensed');
        $("div.title_table_netprofit").addClass('compressed_title');
        $("div.line_table_netprofit").addClass('comressed_profitrow');
        $("div.content_table_netprofit").addClass('compressed_title');
        cssblock='none';
    } else {
        // $("div.but-detailed").html('Detailed');
        $("div.title_table_netprofit").removeClass('compressed_title');
        $("div.line_table_netprofit").removeClass('comressed_profitrow');
        $("div.content_table_netprofit").removeClass('compressed_title');
        cssblock='block';
    }
    $("div.cell_operating").css('display',cssblock);
    $("div.cell_operating2").css('display',cssblock);
    $("div.cell_payroll").css('display',cssblock);
    $("div.cell_payroll2").css('display',cssblock);
    $("div.cell_advertising").css('display',cssblock);
    $("div.cell_advertising2").css('display',cssblock);
    $("div.cell_projects").css('display',cssblock);
    $("div.cell_projects2").css('display',cssblock);
    $("div.cell_purchases").css('display',cssblock);
    $("div.cell_purchases2").css('display',cssblock);

}
/* Edit notes */
function edit_profitnotes(obj) {
    var type=$("select#but-reportview").val();
    // var objid=obj.id;
    var weekid=obj.id.substr(7);
    var url='/finance/netprofit_weeknote'
    var datqry=new Date().getTime();
    $.post(url, {'week_id':weekid, 'type':type, 'datq':datqry}, function(response){
        if (response.errors=='') {
            show_popup('userdata');
            $("div#pop_content").empty().html(response.data.content);
            $("a#popupContactClose").unbind('click').click(function(){
                disablePopup();
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json')

}
/* */
function save_weeknote() {
    var profit_id=$("input#profit_id").val();
    var weeknote=$("#weeknote").val();
    var url="/finance/save_weeknote";
    var datqry=new Date().getTime();
    $.post(url, {'profit_id':profit_id, 'weeknote':weeknote, 'datq':datqry}, function(response){
        if (response.errors=='') {
            disablePopup();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json')

}

function report_view() {
    var type=$("select#but-reportview").val();
    var url="/finance/netprofit_viewtype";
    $.post(url, {'type':type}, function(response){
        if (response.errors=='') {
            $("div.table_netprofit").empty().html(response.data.content);
            $("div#netprofitweekselect").empty().html(response.data.weekchoice);
            init_netprofitpage();
        } else {
            show_error(response);
        }
    }, 'json');
}

function include_debt(profit) {
    var params=new Array();
    params.push({name: 'profit_id', value: profit});
    params.push({name: 'type', value: $("select#but-reportview").val()});
    var url="/finance/netprofit_debincl"
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.cell_for_debtincl[data-debincl='"+profit+"']").empty().html(response.data.debincl);
            $("div.color_total").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    }, 'json');
}

function _include_debt(obj) {
    var objid=obj.id;
    var newdat;
    if ($("input#"+objid).prop('checked')==true) {
        newdat=1;
    } else {
        newdat=0;
    }
    var weekid=obj.id.substr(10);
    var nettype=$("select#but-reportview").val();
    var url="/finance/netprofit_debincl"
    $.post(url, {'newdat':newdat, 'weekid':weekid,'type':nettype}, function(response){
        if (response.errors=='') {
            $("div.color_total").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    }, 'json');
}
// Rebuild W9 Work Purchase Table
function rebuild_w9table() {
    var params=new Array();
    params.push({name: 'year', value: $(".w9workyears").val()});
    params.push({name: 'w9worksort', value: $("input#w9worksortfld").val()});
    params.push({name: 'w9workdir', value: $("input#w9worksortdirec").val()});
    params.push({name: 'purchasesort', value: $("input#purchasesortfld").val()});
    params.push({name: 'purchasedir', value: $("input#purchasesortdirec").val()});
    var url="/finance/netprofit_w9purchasetable";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.w9purchasetitle").empty().html(response.data.title);
            $("div.w9purchasetablearea").empty().html(response.data.content);
            init_netprofit_content();
        } else {
            show_error(response);
        }
    },'json');
}
// Rebuild Table
function rebuild_charttable() {
    var params=new Array();
    params.push({name: 'compareweek', value: $("select.weektotalsviewtype").val()});
    params.push({name: 'weekbgn', value: $("select#strweek").val()});
    params.push({name: 'weekend', value: $("select#endweek").val()});
    params.push({name: 'paceincome', value: $("input#projincome").val()});
    params.push({name: 'paceexpense', value: $("input#projexpence").val()});
    var url="/finance/netprofit_charttabledata";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.weektotalsdataarea").empty().html(response.data.content);
            if ($("div.showhidecuryear").hasClass('hide')) {
                $("div.parametervalue.currentyearval").show();
                $("div.prognosis.currentyear").show();
            } else {
                $("div.parametervalue.currentyearval").hide();
                $("div.prognosis.currentyear").hide();
            }
            $("div.expensivesrow").hide();
            init_charttable_content();
        } else {
            show_error(response);
        }
    },'json');
}
// Rebuild Compare table
function rebuild_comparetable() {
    var params=new Array();
    params.push({name: 'compareyear', value: $("select.selectcompareyears").val()});
    params.push({name: 'paceincome', value: $("input#projincome").val()});
    params.push({name: 'paceexpense', value: $("input#projexpence").val()});
    var url="/finance/netprofit_comparetabledata";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.comptabledataarea").empty().html(response.data.content);
            if ($("span.exponsivedata").hasClass('show')==true) {
                $(".expensivesrow").hide();
            } else {
                $(".expensivesrow").show();
            }
        } else {
            show_error(response);
        }
    },'json');
}
function init_charttable_content() {
    init_expensive_help();
    $("span.exponsivedata").unbind('click').click(function(){
        var show=1;
        if ($(this).hasClass('hide')) {
            show=0;
        }
        if (show==1) {
            $(this).empty().html('<i class="fa fa-minus-square-o" aria-hidden="true">').removeClass('show').addClass('hide');
            $("div.weektotalsrow.expensivesrow").show();
        } else {
            $(this).empty().html('<i class="fa fa-plus-square-o" aria-hidden="true">').removeClass('hide').addClass('show');
            $("div.weektotalsrow.expensivesrow").hide();
        }
    });
    $("div.parametervalue.showhidecuryear").unbind('click').click(function(){
        if ($(this).hasClass('hide')) {
            $("div.parametervalue.currentyearval").hide();
            $("div.prognosis.currentyear").hide();
            $(this).removeClass('hide').addClass('show').empty().html('[show]');
        } else {
            $("div.parametervalue.currentyearval").show();
            $("div.prognosis.currentyear").show();
            $(this).removeClass('show').addClass('hide').empty().html('[hide]');
        }
    });
}

// Rebuild charts
function drawChart() {
    var params=new Array();
    params.push({name: 'compareweek', value: $("select.weektotalsviewtype").val()});
    params.push({name: 'weekbgn', value: $("select#strweek").val()});
    params.push({name: 'weekend', value: $("select#endweek").val()});
    params.push({name: 'paceincome', value: $("input#projincome").val()});
    params.push({name: 'paceexpense', value: $("input#projexpence").val()});
    var url="/finance/netprofit_chartdata";
    $.post(url,params,function(response){
        if (response.errors=='') {
            var datarows = response.data.datarows;
            var percrows = response.data.percrows;
            _build_data_chart(datarows);
            _build_perc_chart(percrows);

        } else {
            show_error(response);
        }
    },'json');
}

function _build_data_chart(datarows) {
    var data = google.visualization.arrayToDataTable(datarows);

    var options = {
        title: 'Gross Profit, Expenses & Net Profit',
        curveType: 'none',
        legend: {position: 'right', textStyle: {color: 'black', fontSize: 11}},

        axes: {
            y: {label: ''}
        },
        chartArea: {
            top: 40,
            backgroundColor: {stroke: "#3eac48", strokeWidth: 2}
        },
        series: {
            0: { color: '#029310'},
            1: { color: '#ff631c'},
            2: { color: '#0000ff'}
        },
        lineWidth: 4
    };
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    chart.draw(data, options);
}

function _build_perc_chart(datarows) {
    var data = google.visualization.arrayToDataTable(datarows);

    var options = {
        title: 'Profit Efficiency',
        curveType: 'none',
        legend: {position: 'right', textStyle: {color: 'black', fontSize: 10.5}},

        axes: {
            y: {label: '%'}
        },
        chartArea: {
            top: 40,
            backgroundColor: {stroke: "#3eac48", strokeWidth: 2}
        },
        series: {
            0: { color: '#029310'},
            1: { color: '#ff631c'},
            2: { color: '#0000ff'}
        },
        lineWidth: 4
    };
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart_effinciency'));
    chart.draw(data, options);

}

function init_manage_categories() {
    $("div.netprofitcategoryeditarea").find('div.datarow > div.deedcell').unbind('click').click(function(){
        // Edit
        var category_id=$(this).data('category');
        var params=new Array();
        params.push({name: 'category_id', value: category_id});
        var url="/finance/profcategory_edit";
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
        var url="/finance/profcategory_edit";
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
        var url="/finance/profcategory_save";
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
        var url="/finance/profcategory_cancel";
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

function init_expensive_help() {
    // Show Expencive Help
    $("div.helpexpensive").bt({
        fill: '#FFFFFF',
        cornerRadius: 10,
        width: 90,
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
}