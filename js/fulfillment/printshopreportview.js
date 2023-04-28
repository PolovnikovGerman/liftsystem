function init_orderreport_content() {
    leftmenu_alignment();
    init_orderreport_data();
    // var url="/fulfillment/orderreport_head";
    // $.post(url,{},function(response){
    //     if (response.errors=='') {
    //         $("div.printshopcontent").empty().html(response.data.content);
    //
    //     } else {
    //         show_error(response);
    //     }
    // },'json');
    $("#printshopreportbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#printshopreportbrand").val(brand);
        $("#printshopreportbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#printshopreportbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#printshopreportbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_printshoporders();
    });
}

function init_orderreport_data() {
    // count entries inside the hidden content
    var num_entries = $('#orderreptotals').val();
    var perpage = $("#orderrepperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.orderreport_pagination").empty();
        pageOrderReportsCallback(0);
    } else {
        var curpage = $("#orderrepcurpage").val();
        // Create content inside pagination element
        $("div.orderreport_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageOrderReportsCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}


function pageOrderReportsCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value:$("#orderrepperpage").val()});
    params.push({name:'offset', value: page_index});
    params.push({name:'search', value: $("input.reportorder_searchdata").val()});
    params.push({name:'totals', value: $("input#orderreptotals").val()});
    params.push({name: 'report_year', value : $("#report_year").val()});
    params.push({name: 'brand', value: $("#printshopreportbrand").val()});
    var url="/fulfillment/orderreport_data";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#orderreportdataarea").empty().html(response.data.content);
            // Init new content manage
            init_orderreport_page();
            $("#loader").hide();
            jQuery.balloon.init();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_orderreport_page() {
    $("div.editorderreport").unbind('click').click(function(){
        $("div.bt-wrapper").css('visibility','hidden');
        var report=$(this).data('report');
        edit_printshoporder(report);
    });
    $("div.delrecord").unbind('click').click(function(){
        var report=$(this).data('report');
        var repnum=$(this).data('reportnum');
        delete_printshoporder(report, repnum);
    })
    $("i.addnewprintshopincome").unbind('click').click(function(){
        var report=0;
        edit_printshoporder(report);
    });
    $("input.orderreportplatecost").unbind('change').change(function(){
        var fldname=$(this).data('fldname');
        var newval=$(this).val();
        edit_report_addcosts(fldname, newval);
    });
    $("input.orderreportsaved").unbind('change').change(function(){
        var fldname='repaid_cost';
        var newval=$(this).val();
        edit_report_addcosts(fldname, newval);
    });
    $("input.reportorder_searchdata").unbind('keypress').keypress(function(event){
        if (event.which == 13) {
            search_printshoporders();
        }
    });
    $("div.orderreport_findall").unbind('click').click(function(){
        search_printshoporders();
    });
    $("div.orderreport_clear").unbind('click').click(function(){
        $("input.reportorder_searchdata").val('');
        search_printshoporders();
    });
    $('div.orderreport_filter').find("div.labeltxt").unbind('click').click(function(){
        $('div.orderreport_filter').find("div.labeltxt").removeClass('active');
        $(this).addClass('active');
        $("input#report_year").val($(this).data('year'));
        search_printshoporders();
    })
    $("div.orderreporttablebody").find("div.ordernum").qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('profitview') // Use href attribute as URL
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
            at: 'center right',
        },
        style: {
            classes: 'orderprofit_tooltip'
        }
    });
    $("div.orderreporttablebody").find('div.itemname').qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'left center',
            at: 'center right',
        },
        style: {
            classes: 'colordata_tooltip'
        }
    });
    $("div.orderreporttablebody").find('div.itemcolor').qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'left center',
            at: 'center right',
        },
        style: {
            classes: 'colordata_tooltip'
        }
    });
    $("div.orderreport_export").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'search', value: $("input.reportorder_searchdata").val()});
        params.push({name: 'report_year', value : $("#report_year").val()});
        params.push({name: 'brand', value: $("#printshopreportbrand").val()});
        var url="/fulfillment/orderreport_dataexport";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#loader").hide();
                window.open(response.data.url);
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $("div.showorangeplate").unbind('click').click(function () {
        var flagshow = $("input#showorangeplate").val();
        if (flagshow==0) {
            $("input#showorangeplate").val(1);
            $(this).empty().html('<i class="fa fa-chevron-left" aria-hidden="true" title="Hide Orange Plate"></i>');
            $("div.orderreporttablehead").find('div.oranplate').show();
            $("#orderreportsummaryarea").find('div.oranplate').show();
            $("#orderreportdataarea").css('width','1061px');
            $("#orderreportdataarea").find('div.oranplate').show();
        } else {
            $("input#showorangeplate").val(0);
            $(this).empty().html('<i class="fa fa-chevron-right" aria-hidden="true" title="Show Orange Plate"></i>');
            $("div.orderreporttablehead").find('div.oranplate').hide();
            $("#orderreportsummaryarea").find('div.oranplate').hide();
            $("#orderreportdataarea").css('width','1031px');
            $("#orderreportdataarea").find('div.oranplate').hide();
        }
    });
}

function edit_report_addcosts(fldname, newval) {
    var params=new Array();
    params.push({name: 'fldname', value: fldname});
    params.push({name: 'newval', value: newval});
    var url="/fulfillment/orderreport_addcost";
    $.post(url, params, function(response){
        if (response.errors=='') {
        } else {
            show_error(response);
        }
    },'json');
}

function edit_printshoporder(report) {
    // Unbind Click
    $("div.editorderreport").unbind('click');
    // Unbind Add
    $("i.addnewprintshopincome").unbind('click');

    var url="/fulfillment/orderreport_edit";
    var params=new Array();
    params.push({name:'printshop_income_id', value: report});
    params.push({name:'showorange', value: $("#showorangeplate").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $(".qtip").qtip('hide');
            if (report==0) {
                $(".orderreporttablebody").scrollTop(-55555);
                $("div#orderreportdataarea").prepend("<div class='datarow' data-report=0>"+response.data.content+'</div>');
            } else {
                $("div#orderreportdataarea").find("div.datarow[data-report='"+report+"']").empty().html(response.data.content);
            }
            init_printshiporder_edit(report);
        } else {
            show_error(response);
        }
    },'json');
}

function init_printshiporder_edit(report) {
    $("input.psorderdate").datepicker();
    $("input.psorderinput").unbind('change').change(function(){
        var fldname=$(this).data('fldname');
        var url="/fulfillment/orderreport_change";
        var params=new Array();
        params.push({name: 'sessionid', value: $("input#sessionid").val()});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.datarow[data-report='"+report+"']").find('div.misprintproc').empty().html(response.data.misprint_proc);
                $("div.datarow[data-report='"+report+"']").find('div.totalqty').empty().html(response.data.total_qty);
                $("div.datarow[data-report='"+report+"']").find('div.itemscost').empty().html(response.data.costitem);
                $("div.datarow[data-report='"+report+"']").find('div.totalplate').empty().html(response.data.totalplates);
                $("div.datarow[data-report='"+report+"']").find('div.platecost').empty().html(response.data.platescost);
                $("div.datarow[data-report='"+report+"']").find('div.totalcost').empty().html(response.data.itemstotalcost);
                $("div.datarow[data-report='"+report+"']").find('div.totaladdlcost').empty().html(response.data.extraitem);
                $("div.datarow[data-report='"+report+"']").find('div.misprintcost').empty().html(response.data.misprintcost);
                if (fldname=='order_num') {
                    $("div.datarow[data-report='"+report+"']").find('div.customer').empty().html(response.data.customer);
                }
            } else {
                show_error(response);
                if (typeof(response.data.oldval) != 'undefined') {
                    $("input[data-fldname='"+fldname+"']").val(response.data.oldval).focus();

                }
            }
        },'json');
    });
    $("select.psorderselect").unbind('change').change(function(){
        var fldname=$(this).data('fldname');
        var url="/fulfillment/orderreport_change";
        var params=new Array();
        params.push({name: 'sessionid', value: $("input#sessionid").val()});
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (fldname=='inventory_item_id') {
                    $("div.datarow[data-report='"+report+"']").find('div.itemcolor').empty().html(response.data.colorlist);
                    init_printshiporder_edit(report);
                }
                $("div.datarow[data-report='"+report+"']").find('div.costea').empty().html(response.data.price);
                $("div.datarow[data-report='"+report+"']").find('div.addlcost').empty().html(response.data.extracost);
                $("div.datarow[data-report='"+report+"']").find('div.totalea').empty().html(response.data.totalea);
                // Commons
                $("div.datarow[data-report='"+report+"']").find('div.misprintproc').empty().html(response.data.misprint_proc);
                $("div.datarow[data-report='"+report+"']").find('div.totalqty').empty().html(response.data.total_qty);
                $("div.datarow[data-report='"+report+"']").find('div.itemscost').empty().html(response.data.costitem);
                $("div.datarow[data-report='"+report+"']").find('div.totalplate').empty().html(response.data.totalplates);
                $("div.datarow[data-report='"+report+"']").find('div.platecost').empty().html(response.data.platescost);
                $("div.datarow[data-report='"+report+"']").find('div.totalcost').empty().html(response.data.itemstotalcost);
                $("div.datarow[data-report='"+report+"']").find('div.misprintcost').empty().html(response.data.misprintcost);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Save data
    $("i.saveorderdata").unbind('click').click(function(){
        var url="/fulfillment/orderreport_save";
        var params=new Array();
        params.push({name: 'sessionid', value: $("input#sessionid").val()});
        params.push({name:'search', value: $("input.reportorder_searchdata").val()});
        params.push({name: 'report_year', value : $("#report_year").val()});
        params.push({name: 'brand', value: $("#printshopreportbrand").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $('#orderreptotals').val(response.data.totals);
                $("div#orderreportsummaryarea").empty().html(response.data.summary_view);
                init_orderreport_data();
                $("#neworderprofitview").hide().empty();
                // $("#neworderprofitview").empty().html(response.data.newprofit_view).show();
                // setTimeout(function(){
                //     $("#neworderprofitview").hide().empty();
                // }, 4000);

            } else {
                show_error(response);
            }
        },'json');
    });
    // Cancel save
    $("i.canceleditorderdata").unbind('click').click(function(){
        init_orderreport_data();
    })
}

function search_printshoporders() {
    var params=new Array();
    params.push({name:'search', value: $("input.reportorder_searchdata").val()});
    params.push({name: 'report_year', value: $("#report_year").val()});
    params.push({name: 'brand', value: $("#printshopreportbrand").val()});
    var url="/fulfillment/orderreport_search";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('#orderreptotals').val(response.data.totals);
            $("div#orderreportsummaryarea").empty().html(response.data.summary_view);
            init_orderreport_data();
        } else {
            show_error(response);
        }
    },'json');
}

function delete_printshoporder(report, repnum) {
    if (confirm('Are you sure you want to delete PO '+repnum+' ?')) {
        var url="/fulfillment/orderreport_remove";
        var params=new Array();
        params.push({name:'search', value: $("input.reportorder_searchdata").val()});
        params.push({name: 'report_year', value: $("#report_year").val()});
        params.push({name: 'brand', value: $("#printshopreportbrand").val()});
        params.push({name:'printshop_income_id', value: report});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $('#orderreptotals').val(response.data.totals);
                $("div#orderreportsummaryarea").empty().html(response.data.summary_view);
                init_orderreport_data();
            } else {
                show_error(response);
            }
        },'json');
    }
}