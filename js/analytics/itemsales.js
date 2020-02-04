/*
    Created on : Dec 14, 2015, 5:47:00 PM
    Author     : german
    Purpose : Item Sales Report
*/

function itemsales_reportinit() {
    initItemSalesReportPagination();
    // Init Management
    $("input[name='selectsort']").unbind('change').change(function(){
        var pagenum=$("input#curpageitemsale").val();
        pageItemSalereport(pagenum);
    });
    $("input[name='calcyear']").unbind('change').change(function() {
        /* var pagenum=$("input#curpageitemsale").val();
        pageItemSalereport(pagenum); */
        filter_itemsalereport();
    });
    $("select#itemsalesperpage").unbind('change').change(function(){
        initItemSalesReportPagination();
    });
    // Search
    $("input[name='selectvendor']").unbind('change').change(function(){
        filter_itemsalereport();
    });
    $("input.itemsales_searchdata").keypress(function(event){
        if (event.which == 13) {
            filter_itemsalereport();
        }
    });
    $("div.itemsales_findall").unbind('click').click(function(){
        filter_itemsalereport();
    });
    $("div.itemsales_clear").unbind('click').click(function(){
        $("input.itemsales_searchdata").val('');
        filter_itemsalereport();
    });
    $("input.addlcostinpt").unbind('change').change(function(){
        $("div.saveaddcost").show();
    });
    $("div.saveaddcost").unbind('click').click(function(){
        saveitemsold_addcost();
    });
    $("input.itemsoldalldatachk").unbind('change').change(function(){
        var chkres=0;
        if ($(this).prop('checked')==1) {
            chkres=1;
        }
        var search=$("input.itemsales_searchdata").val();
        var params=new Array();
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'check', value: chkres});
        params.push({name: 'calc_year', value: $("input.calcyear:checked").val()});
        params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
        params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
        params.push({name:'search',value:search});
        params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
        params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});
        // Check Uncheck current page
        var url="/analytics/itemsales_masscheck";
        $.post(url,params, function(response){
            if (response.errors=='') {
                if (chkres==0) {
                    // Hide total, uncheck all items
                    $("div.itemsalesdatarow[data-item='totals']").hide();
                    $("input.itemsoldtotalchk").prop('checked',false);
                } else {
                    $("input.itemsoldtotalchk").prop('checked',true);
                    if (parseInt(response.data.totals)>0) {
                        $("div.itemsalesdatarow[data-item='totals']").empty().html(response.data.totalview).show();
                    }
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Change Vendor cost select
    $("select.vendorcostcalcselect").unbind('change').change(function(){
        filter_itemsalereport();
    });
    $("select.selectrepsoldcalcyear").unbind('change').change(function(){
        change_reportbaseyear();
    })
}

function change_reportbaseyear() {
    var search=$("input.itemsales_searchdata").val();
    var params=new Array();
    params.push({name: 'baseyear', value: $("select.selectrepsoldcalcyear").val()});
    params.push({name:'limit',value:$("#itemsalesperpage").val()});
    params.push({name:'order_by',value:$("input[name='selectsort']:checked").val()});
    params.push({name:'search',value:search});
    params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
    params.push({name:'maxval',value:$('#itemsalestotal').val()});
    params.push({name:'calc_year', value: $("input[name='calcyear']:checked").val()});
    params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});

    var url="/analytics/itemsales_baseyear";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#itemsalesreport").empty().html(response.data.content);
            $("#loader").hide();
            itemsales_reportinit();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

/* Paginaton */
function initItemSalesReportPagination() {
    // count entries inside the hidden content
    var num_entries = $('#itemsalestotal').val();
    // var perpage = itemsperpage;
    var perpage = $("#itemsalesperpage").val();

    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.itemsalespagination").empty();
        pageItemSalereport(0);
    } else {
        var curpage = $("#curpageitemsale").val();
        // Create content inside pagination element
        $("div.itemsalespagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageItemSalereport,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageItemSalereport(page_index) {
    /* Search */
    var search=$("input.itemsales_searchdata").val();
    var params=new Array();
    params.push({name:'limit',value:$("#itemsalesperpage").val()});
    params.push({name:'order_by',value:$("input[name='selectsort']:checked").val()});
    params.push({name:'search',value:search});
    params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
    params.push({name:'maxval',value:$('#itemsalestotal').val()});
    params.push({name:'offset',value:page_index});
    params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
    params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
    params.push({name:'calc_year', value: $("input[name='calcyear']:checked").val()});
    params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});

    $("#loader").show();
    var url='/analytics/itemsalesdata';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div.itemsalesreportdata").empty().html(response.data.content);
            $("input.addlcostinpt").val(response.data.addcost);
            init_itemsales_content();
            $("input#curpageitemsale").val(page_index);
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
}

function init_itemsales_content() {
    $("div.itemsalesdatarow").children('div.imptcost').unbind('click').click(function(){
        var item=$(this).parent('div').data('item');
        var year=$("input.calcyear:checked").val();
        change_importcost(item, year);
    });
    $("div.itemsalesdatarow").children('div.itemnumber').each(function(){
        $(this).bt({
            width: '359px',
            height: '359px',
            filling: '#FFFFFF',
            ajaxPath: ["$(this).data('imgurl')"]
        });
    });
    $("div.itemsalesdatarow").children('div.itemnumber').unbind('click').click(function(){
        var item=$(this).parent('div').data('item');
        show_vendoritem_prices(item);
    })
    // Show popup orders list
    $("div.itemsalesdatarow").children('div.ordqty').unbind('click').click(function(){
        var item=$(this).parent('div').data('item');
        var year=$(this).data('year');
        var url="/reports/sales_year_details";
        $.post(url, {'item': item, 'year': year}, function(response){
            if (response.errors=='') {
                show_popup('salesmonthdetails');
                $("div#pop_content").empty().html(response.data.content);
                if (response.data.countdata<20) {
                    $("div.datarow").find('div.profit_perc').css('width','55');
                    $("div.datatotals").find('div.profit_perc').css('width','54');
                }
                $("a#popupContactClose").unbind('click').click(function(){
                    disablePopup();
                });
            } else {
                show_error(response);
            }
        },'json')
    });
    // Click on check
    $("input.itemsoldtotalchk").unbind('change').change(function(){
        var chkres=0;
        if ($(this).prop('checked')==true) {
            chkres=1;
        }
        var search=$("input.itemsales_searchdata").val();
        var params=new Array();
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'check', value: chkres});
        params.push({name: 'calc_year', value: $("input.calcyear:checked").val()});
        params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
        params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
        params.push({name:'search',value:search});
        params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
        params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});
        var url="/reports/itemsales_checkitem";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (response.data.totals==0) {
                    $("div.itemsalesdatarow[data-item='totals']").hide();
                } else {
                    $("div.itemsalesdatarow[data-item='totals']").empty().html(response.data.totalview).show();
                }
                if (chkres==0) {
                    $("input.itemsoldalldatachk").prop('checked',false);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
}

function change_importcost(item, year) {
    var params=new Array();
    params.push({name: 'item', value: item});
    params.push({name: 'year', value: year});
    var url="/reports/itemsalesedit";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.itemsalesdatarow").children('div.imptcost').unbind('click');
            $("div.itemsalesdatarow[data-item='"+item+"']").children('div.imptcost').empty().html(response.data.editcontent);
            $("div.itemsalesdatarow[data-item='"+item+"']").children('div.imptcog').empty().html(response.data.savecontent);
            $("div.itemsalesdatarow[data-item='"+item+"']").children('div.imptprofit').empty().html(response.data.cancelcontent);
            $("div.itemsalesform_save").unbind('click').click(function(){
                save_importcost(item, year);
            });
            $("div.itemsalesform_revert").unbind('click').click(function(){
                revert_importcost(item, year);
            });
        } else {
            show_error(response);
        }
    },'json');
}

function revert_importcost(item, year) {
    var params=new Array();
    params.push({name: 'item', value: item});
    params.push({name: 'year', value: year});
    params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
    params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
    params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});
    var url="/reports/itemsalesgetrow";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.itemsalesdatarow[data-item='"+item+"']").empty().html(response.data.content);
            init_itemsales_content();
        } else {
            show_error(response);
        }
    },'json');

}

function save_importcost(item, year) {
    var imptcost=parseFloat($("input.itemsalesimptcostval").val());
    var params=new Array();
    var search=$("input.itemsales_searchdata").val();
    params.push({name: 'item', value: item});
    params.push({name: 'calc_year', value: year});
    params.push({name: 'cost', value: imptcost});
    params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
    params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
    params.push({name:'search',value:search});
    params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
    params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});
    var url="/reports/itemsalessave";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.itemsalesdatarow[data-item='"+item+"']").empty().html(response.data.content);
            init_itemsales_content();
            if (parseInt(response.data.totals)==0) {
                $("div.itemsalesdatarow[data-item='totals']").hide();
            } else {
                $("div.itemsalesdatarow[data-item='totals']").empty().html(response.data.totalview).show();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function filter_itemsalereport() {
    var params=new Array();
    var search=$("input.itemsales_searchdata").val();
    params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
    params.push({name:'search',value:search});
    params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
    params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
    params.push({name:'calc_year', value: $("input[name='calcyear']:checked").val()});
    params.push({name:'vendor_cost', value: $("select.vendorcostcalcselect").val()});
    $("#loader").show();
    var url='/reports/itemsalessearch';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#itemsalestotal").val(response.data.totals);
            $("input#curpageitemsale").val(0);
            if (parseInt(response.data.chktotals)==0) {
                $("div.itemsalesdatarow[data-item='totals']").hide();
            } else {
                $("div.itemsalesdatarow[data-item='totals']").empty().html(response.data.totalview).show();
            }
            initItemSalesReportPagination();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function saveitemsold_addcost() {
    var params=new Array();
    params.push({name: 'addcost', value: parseFloat($("input.addlcostinpt").val())});
    var search=$("input.itemsales_searchdata").val();
    params.push({name:'vendor',value:$("input[name='selectvendor']:checked").val()});
    params.push({name:'search',value:search});
    params.push({name:'current_year', value: $("input#itemsalecurrentyear").val()});
    params.push({name:'prev_year', value: $("input#itemsaleprevyear").val()});
    params.push({name:'calc_year', value: $("input[name='calcyear']:checked").val()});
    var url="/reports/itemsalesaddcost";
    $.post(url,params, function(response){
        if (response.errors=='') {
            if (parseInt(response.data.totals)==0) {
                $("div.itemsalesdatarow[data-item='totals']").hide();
            } else {
                $("div.itemsalesdatarow[data-item='totals']").empty().html(response.data.totalview).show();
            }
            $("div.saveaddcost").hide();
            var pagenum=$("input#curpageitemsale").val();
            pageItemSalereport(pagenum);
        }
    },'json');
}