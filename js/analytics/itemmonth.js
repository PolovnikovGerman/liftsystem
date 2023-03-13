function itemmonth_reportinit() {
    initItemMonthPagination();
    // Change Brand
    $("#itemmonthreporttopmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#itemmonthreportbrand").val(brand);
        $("#itemmonthreporttopmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#itemmonthreporttopmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#itemmonthreporttopmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        filter_itemmonthreport();
    });
    // change Per Page
    $("select#itemmonthperpage").unbind('change').change(function(){
        initItemMonthPagination();
    });
    // Change Sort FLD
    $("select.sortselect").unbind('change').change(function(){
        var curpage=$("input#curpageitemmonth").val();
        pageItemMonthreport(curpage);
    })
    // Search
    $("input.itemmonth_searchdata").keypress(function (event) {
        if (event.which == 13) {
            filter_itemmonthreport();
        }
    });
    $("div.itemmonth_findall").unbind('click').click(function () {
        filter_itemmonthreport();
    });
    $("div.itemmonth_clear").unbind('click').click(function () {
        $("input.itemmonth_searchdata").val('');
        filter_itemmonthreport();
    });

}

function initItemMonthPagination() {
    // count entries inside the hidden content
    var num_entries = $('#itemmonthtotal').val();
    // var perpage = itemsperpage;
    var perpage = $("#itemmonthperpage").val();

    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.itemmonthpagination").empty();
        pageItemMonthreport(0);
    } else {
        var curpage = $("#curpageitemmonth").val();
        // Create content inside pagination element
        $("div.itemmonthpagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageItemMonthreport,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

// Page Callback
function pageItemMonthreport(page_index) {
    /* Search */
    var search=$("input.itemmonth_searchdata").val();
    var params=new Array();
    params.push({name:'limit',value:$("#itemmonthperpage").val()});
    params.push({name:'order_year',value:$("#itemmonthsortyear").val()});
    params.push({name:'order_fld', value: $("#itemmonthsortfld").val()});
    params.push({name:'maxval',value:$('#itemmonthtotal').val()});
    params.push({name:'search',value:search});
    params.push({name:'offset',value:page_index});
    params.push({name:'current_year', value: $("input#itemmonthcurrentyear").val()});
    params.push({name:'start_year', value: $("input#itemmonthstartyear").val()});
    params.push({name: 'brand', value: $("#itemmonthreportbrand").val()});
    $("#loader").show();
    var url='/analytics/itemmonthsalesdata';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div.itemmonth_data").empty().html(response.data.content);
            itemmonth_content_init();
            leftmenu_alignment();
            $("input#curpageitemmonth").val(page_index);
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
}

function itemmonth_content_init() {
    $("div.monthdata.datacell").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'month', value: $(this).data('month')});
        params.push({name: 'year', value: $(this).data('year')});
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name:'brand', value: $("#itemmonthreportbrand").val()});
        var url="/analytics/sales_month_details";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModalLabel").empty().html('Sales Month Details');
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").find('div.modal-dialog').css('width','685px');
                $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.venditemshowprice").unbind('click').click(function(){
        var item=$(this).data('item');
        show_vendoritem_prices(item);
    });

}

function filter_itemmonthreport() {
    var search=$("input.itemmonth_searchdata").val();
    var params=new Array();
    params.push({name:'search',value:search});
    params.push({name:'current_year', value: $("input#itemmonthcurrentyear").val()});
    params.push({name:'start_year', value: $("input#itemmonthstartyear").val()});
    params.push({name:'brand', value: $("#itemmonthreportbrand").val()});
    $("#loader").show();
    var url='/analytics/itemmonthsearch';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#itemmonthtotal").val(response.data.totals);
            $("input#curpageitemmonth").val(0);
            initItemMonthPagination();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');

}