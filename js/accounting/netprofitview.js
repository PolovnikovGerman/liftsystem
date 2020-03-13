function init_netprofit_area() {
    google.load('visualization', '1.0', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    console.log('STATRT');
    init_netprofit();
    // Change Brand
    $("#netprofitviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#netprofitviewbrand").val(brand);
        $("#netprofitviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#netprofitviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#netprofitviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_netprofit();
    });
}


function init_netprofit() {
    init_netprofitpage();
    init_expensive_help();
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
        var url="/accounting/manage_profcategory";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModal").find('div.modal-dialog').css('width','470px');
                $("#pageModalLabel").empty().html('Edit Categories');
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal('show');
                init_manage_categories();
            } else {
                show_error(response)
            }
        },'json');
    });
}

function init_netprofitpage() {
    console.log('SHOW CONTENT');
    var url='/accounting/netprofitdat';
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
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
    $("div.cell_gross_profit2.projprof").qtip({
        content : {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('profitid') // Use href attribute as URL
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to error
                    api.set('content.text', status + ': ' + error);
                });
                return 'Loading...'; // Set some initial text
            }
        },
        position: {
            my: 'left center',
            at: 'middle right',
        },
        style: {
            classes: 'profitorders_tooltip'
        },
    });
    $("span.showallweekdata").unbind('click').click(function(){
        $("input#limitweekshow").val(0);
        init_netprofitpage();
    });
    $("div.cell_sales2").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'white_tooltip paymonitor_ordernum_tooltip'
        }
    });
    $("div.imbox.shownote").qtip({
        content : {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('netnote') // Use href attribute as URL
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to error
                    api.set('content.text', status + ': ' + error);
                });
                return 'Loading...'; // Set some initial text
            }
        },
        position: {
            my: 'right center',
            at: 'middle left',
        },
        style: {
            classes: 'shownote_tooltip'
        },
    });
    $("div.cell_week2.editdata").unbind('click').click(function(){
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
    $("div.w9purchasetablearea").find('div.category_name.entered').qtip({
        content : {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('content') // Use href attribute as URL
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to error
                    api.set('content.text', status + ': ' + error);
                });
                return 'Loading...'; // Set some initial text
            }
        },
        position: {
            my: 'right center',
            at: 'middle left',
        },
        show: {
            event: 'click'
        },
        style: {
            classes: 'shownote_tooltip'
        },
    })
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

function change_run_week() {
    var url='/accounting/get_weektotals';
    var params=new Array();
    var fromweek=$("select#weekselectfrom").val();
    var untilweek=$("select#weekselectuntil").val();
    if (fromweek=='' && untilweek=='') {
        $("input.allweekschoice").prop('checked',true);
    }
    params.push({name: 'fromweek', value: fromweek});
    params.push({name: 'untilweek', value: untilweek});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.color_total").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    },'json');
}

function init_netprofit_manage() {
}

function edit_profdat(profit) {
    $("#loader").show();
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url='/accounting/netprofitedit';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.cell_week2").unbind('click');
            $("div#nerpr"+response.data.weekid).empty().html(response.data.content);
            init_netprofitdetails_edit();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function init_netprofitdetails_edit() {
    $("a#netprofitdetailsave").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#detailssession").val()});
        params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
        var url="/accounting/netprofit_details_save";
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
        var url="/accounting/netprofit_details_change";
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
        var url='/accounting/netprofit_purchase';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModal").find('div.modal-dialog').css('width','966px');
                $("#pageModalLabel").empty().html(response.data.title);
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal('show');
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
        var url="/accounting/netprofit_details_change";
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
        params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
        var url="/accounting/netprofit_details_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModal").modal('hide');
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
        var url="/accounting/purchase_newdetails";
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
            href: '/accounting/profit_newcategory',
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
        var url="/accounting/purchase_editdetails";
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
        var url="/accounting/purchase_editdetails";
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
        var url="/accounting/purchase_deletedetails";
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
        var url="/accounting/w9work_newdetails";
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
        var url="/accounting/purchase_editdetails";
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
        var url="/accounting/purchase_editdetails";
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
        var url="/accounting/profit_categorysave";
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
    var url='/accounting/netprofit_weeknote'
    var params = new Array();
    params.push({name: 'week_id', value: weekid});
    params.push({name: 'type', value: type});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','470px');
            $("#pageModalLabel").empty().html('Edit Note');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal('show');
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
    var url="/accounting/save_weeknote";
    var params = new Array();
    params.push({name: 'profit_id', value :profit_id});
    params.push({name: 'weeknote', value :weeknote});
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
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
    var url="/accounting/netprofit_viewtype";
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url="/accounting/netprofit_debincl"
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.cell_for_debtincl[data-debincl='"+profit+"']").empty().html(response.data.debincl);
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url="/accounting/netprofit_w9purchasetable";
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url="/accounting/netprofit_charttabledata";
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
    params.push({name: 'brand', value: $("#netprofitviewbrand").val()});
    var url="/accounting/netprofit_comparetabledata";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.comptabledataarea").empty().html(response.data.content);
            if ($("span.exponsivedata").hasClass('shown')==true) {
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
    var url="/accounting/netprofit_chartdata";
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
        var url="/accounting/profcategory_edit";
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
        var url="/accounting/profcategory_edit";
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
        var url="/accounting/profcategory_save";
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
        var url="/accounting/profcategory_cancel";
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
    $("div.helpexpensive").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'white_tooltip paymonitor_ordernum_tooltip'
        }
    });
}