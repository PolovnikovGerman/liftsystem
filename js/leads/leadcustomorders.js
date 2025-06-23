function init_leadcustomorders() {
    search_customorder_data();
    totalyears();
    $(".profitorder_dateinpt").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'm/d/y'
    });
    $("div.exportdatacall").unbind('click').click(function(){
        prepare_custom_export();
    });
    $("#profitsearch").keypress(function(event){
        if (event.which == 13) {
            search_customorder_data();
        }
    });
    $("#find_profit").unbind('click').click(function(){
        search_customorder_data();
    });
    $("#clear_profit").unbind('click').click(function(){
        clear_customorder_search();
    });
    // Change sort
    $(".profitorder_date").unbind('click').click(function(){
        change_customorder_sort('o.order_date','profitorder_date');
    })
    // $(".profitorder_brand").unbind('click').click(function(){
    //     change_customorder_sort('b.brand_name','profitorder_brand');
    // })
    $(".profitorder_numorder").unbind('click').click(function(){
        change_customorder_sort('o.order_num','profitorder_numorder');
    })
    $(".profitorder_customer").unbind('click').click(function(){
        change_customorder_sort('o.customer_name','profitorder_customer');
    })
    $(".profitorder_revenue").unbind('click').click(function(){
        change_customorder_sort('o.revenue','profitorder_revenue');
    })
    $(".profitorder_confirm").unbind('click').click(function () {
        change_customorder_sort('o.order_confirmation','profitorder_confirm');
    })
    $(".profitorder_profit").unbind('click').click(function(){
        change_customorder_sort('o.profit','profitorder_profit');
    })
    $(".profitorder_profitperc").unbind('click').click(function(){
        change_customorder_sort('o.profit_perc','profitorder_profitperc');
    });
    $("select#order_filtr").unbind('change').change(function(){
        search_customorder_data();
    })
    $("select#perpage_profitorders").unbind('change').change(function(){
        $("#curpagetab1").val(0);
        initCustomOrderPagination();
    });
    // Change type of filter
    $("#profitdatetypechoise1").unbind('click').click(function(){
        $(".selectorderyeardat").prop('disabled',false).addClass('active');
        $(".selectordermonthdat").prop('disabled',false).addClass('active');
        search_customorder_data();
        $("#customdatebgn").prop('disabled',true);
        $("#customdateend").prop('disabled',true);
        $(".profitorder_dateinpt").datepicker("option", "disabled", true );
    });
    // Change Year
    $(".selectorderyeardat").unbind('change').change(function () {
        search_customorder_data();
    });
    $(".selectordermonthdat").unbind('change').change(function () {
        search_customorder_data();
    });
    $(".selectshiplocationdat").unbind('change').change(function(){
        if (parseInt($(".selectshiplocationdat").val())>0) {
            // Add Shipping States
            var url="/accounting/orderprofit_states";
            $.post(url, {'country_id': $(".selectshiplocationdat").val()}, function (response) {
                if (response.errors=='') {
                    $(".selectstatelocation").empty().html(response.data.content);
                    $(".selectstatelocationdat").unbind('change').change(function(){
                        search_customorder_data();
                    })
                    search_customorder_data();
                } else {
                    show_error(response);
                }
            },'json');
        } else {
            $(".selectstatelocation").empty().html('&nbsp;');
            search_customorder_data();
        }
    });
    $(".selectordertypesdat").unbind('change').change(function(){
        search_customorder_data();
    })
    $("#profitdatetypechoise2").unbind('click').click(function(){
        $(".selectorderyeardat").prop('disabled',true).removeClass('active');
        $(".selectordermonthdat").prop('disabled',true).removeClass('active');
        $("#customdatebgn").prop('disabled',false);
        $("#customdateend").prop('disabled',false);
        search_customorder_data();
    });
    $("#customdatebgn").unbind('change').change(function () {
        search_customorder_data();
    });
    $("#customdateend").unbind('change').change(function () {
        search_customorder_data();
    });
    // Change include quickbox
    $("div#hidequickboxcheck").unbind('click').click(function(){
        var url="/accounting/exclude_quickbook";
        var params=new Array();
        params.push({name: 'exclude_quickbook', value: $("#quickbookexclude").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#hidequickboxcheck").empty().html(response.data.content);
                $("input#quickbookexclude").val(response.data.newval);
                search_customorder_data();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function search_customorder_data() {
    var params = prepare_customorder_filter();
    var url="/leads/search_custom_orders";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#profittotals_title").empty().html(response.data.totals_head);
            $("#totaltab1").val(response.data.totals);
            $("#curpagetab1").val(0);
            $("#orders-total-row").empty().html(response.data.total_row);
            $("div.profitotaltooltip").qtip({
                content : {
                    text: function(event, api) {
                        $.ajax({
                            // url: href // Use href attribute as URL
                            url: api.elements.target.data('viewsrc') // Use href attribute as URL
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
                    my: 'bottom right',
                    at: 'middle left',
                },
                style: {
                    classes: 'qtip-dark curpoints_tooltip'
                },
                show: {
                    effect: function() { $(this).fadeIn(250); }
                },
                hide: {
                    delay: 200,
                    fixed: true, // <--- add this
                    effect: function() { $(this).fadeOut(250); }
                },
            });
            initCustomOrderPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_customorder_search() {
    // Clear inputs, dropdowns
    $("#profitsearch").val('');
    $("#order_filtr").val(0);
    $(".selectorderyeardat").val(0);
    $("#customdatebgn").val('');
    $("#customdateend").val('');
    search_customorder_data();
}

function prepare_customorder_filter() {
    var search=$("#profitsearch").val();
    var params = new Array();
    params.push({name:'search', value: search});
    params.push({name:'filter', value:$("#order_filtr").val()});
    params.push({name: 'exclude_quickbook', value: $("#quickbookexclude").val()});
    if ($("input#profitdatetypechoise1").prop('checked')==true) {
        params.push({name: 'show_year', value: 1});
        params.push({name: 'year', value: $(".selectorderyeardat").val()});
        params.push({name: 'month', value: $(".selectordermonthdat").val()});
        params.push({name: 'date_bgn', value: 0});
        params.push({name: 'date_end', value: 0});
    } else {
        params.push({name: 'show_year', value: 0});
        params.push({name: 'year', value: 0});
        params.push({name: 'month', value: 0});
        params.push({name: 'date_bgn', value: $("#customdatebgn").val()});
        params.push({name: 'date_end', value: $("#customdateend").val()});
    }
    params.push({name:'shipping_country', value:$(".selectshiplocationdat").val()});
    if ($(".selectstatelocationdat").length>0) {
        params.push({name: 'shipping_state', value: $(".selectstatelocationdat").val()});
    } else {
        params.push({name: 'shipping_state', value: 0 });
    }
    params.push({name: 'order_type',value: $(".selectordertypesdat").val()});
    params.push({name: 'brand', value: $("#profitordersbrand").val()});
    return params;
}

function initCustomOrderPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totaltab1').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpage_profitorders").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#profitorders_pagination").empty();
        pageCustomOrederCallback(0);
    } else {
        var curpage = $("#curpagetab1").val();
        // Create content inside pagination element
        $("#profitorders_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageCustomOrederCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageCustomOrederCallback(page_index) {
    var params = prepare_customorder_filter();
    params.push({name:'limit', value:$("#perpage_profitorders").val()});
    params.push({name:'offset', value:page_index});
    params.push({name:'order_by', value:$("#orderbytab1").val()});
    params.push({name:'direction', value:$("#directiontab1").val()});
    params.push({name:'maxval', value:$('#totaltab1').val()});
    var url='/leads/customordersdat';
    $("#loader").show();
    $.post(url,params,function(response){
        $("#loader").css('display','none');
        if (response.errors=='') {
            $("#curpagetab1").val(page_index);
            leftmenu_alignment();
            $('#tableinfotab1').empty().html(response.data.content);
            try {
                jQuery.balloon.init();
            } catch (e) {
                console.log('Ballone not init');
            }
            init_customorder_manage();
        } else {
            show_error(response);
        }
    },'json');
}

function init_customorder_manage() {
    $("a.cancord").unbind('click').click(function(){
        var order=$(this).data('order');
        cancel_order(order);
    });

    $("a.revertord").unbind('click').click(function(){
        var order=$(this).data('order');
        revert_order(order);
    });

    $("div.profitorder_numorder_data").unbind('click').click(function() {
        var order=$(this).data('order');
        edit_order(order);
    });
    // $("#addnew").unbind('click').click(function(){
    //     var brand = $(this).data('brand');
    //     add_leadorder(brand);
    //     // add_neworder();
    // })
    // $("a.editcoglnk").unbind('click').click(function(){
    //     edit_cogval(this);
    // })
    // $("input.calcship").unbind('click').change(function(){
    //     change_shipprofit(this);
    // })
    // $("input.calcccfee").unbind('click').change(function() {
    //     change_ccfeeprofit(this);
    // });
    // $("div.profitorder_orderitem_data").unbind('click').unbind('click').click(function(){
    //     var order_id=$(this).data('orderid');
    //     edit_item(order_id);
    // });
    $("div.lowprofittitle").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'right center',
            at: 'center left',
        },
        style: {
            classes: 'qtip-dark lowprofit_tooltip'
        }
    });
    // $("div.profitorder_shipping_calc").find('i').unbind('click').click(function(){
    //     var order=$(this).parent('div.profitorder_shipping_calc').data('order');
    //     edit_shipping(order);
    // });
    // Show by hover
    $("div.multicolor").qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('viewsrc') // Use href attribute as URL
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
            my: 'bottom right',
            at: 'middle left',
        },
        style: {
            classes : 'qtip-dark lowprofit_tooltip',
        }
    });
}

function cancel_order(order_id) {
    // var order_id=obj.id.substr(4);
    var ordernum=$("#profitord"+order_id+" .profitorder_numorder_data").text();
    if (confirm('Are you sure you want to cancel Order # '+ordernum+' ?')) {
        var url="/accounting/cancel_order";
        $.post(url, {'order_id': order_id, 'flag':1}, function(response){
            if (response.errors=='') {
                initProfitOrderPagination();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}

function revert_order(order_id) {
    // var order_id=obj.id.substr(4);
    var url="/accounting/cancel_order";
    $.post(url, {'order_id': order_id, 'flag':0}, function(response){
        if (response.errors=='') {
            initProfitOrderPagination();
        } else {
            show_error(response);
        }
    }, 'json');

}

function edit_order(order) {
    var callpage = 'profitlist';
    var brand = $("#profitordersbrand").val();
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(order)==0) {
                init_onlineleadorder_edit();
                init_rushpast();
                if (parseInt($("#ordermapuse").val())==1) {
                    // Init simple Shipping address
                    initShipOrderAutocomplete();
                    if ($("#billorder_line1").length > 0) {
                        initBillOrderAutocomplete();
                    }
                }
            } else {
                if (parseInt(response.data.cancelorder)===1) {
                    $("#artModal").find('div.modal-header').addClass('cancelorder');
                } else {
                    $("#artModal").find('div.modal-header').removeClass('cancelorder');
                }
                navigation_init();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function totalyears() {
    var url = '/leads/customordercnt_total';
    var params = new Array();
    params.push({name: 'brand', value: $("#profitordersbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("div#totalcntorders").empty().html(response.data.content);
            $("div.profitordertotalarea").empty().html(response.data.content);
            $("div.profitordertotalarea").css('width', response.data.slider_width).css('margin-left', response.data.margin);
            /* Hover */
            $(".orders_totals").find('div.totalorder').qtip({
                content: {
                    text: function (event, api) {
                        $.ajax({
                            url: api.elements.target.data('viewsrc') // Use href attribute as URL
                        }).then(function (content) {
                            // Set the tooltip content upon successful retrieval
                            api.set('content.text', content);
                        }, function (xhr, status, error) {
                            // Upon failure... set the tooltip content to error
                            api.set('content.text', status + ': ' + error);
                        });
                        return 'Loading...'; // Set some initial text
                    }
                },
                show: {
                    event: 'click'
                },
                /* position: {
                    my: 'bottom center',
                    at: 'middle center',
                },*/
                style: {
                    classes: 'orderdetails_tooltip'
                },
            });
            slidermargin = parseInt($("div.profitordertotalarea").css('margin-left'));
            init_customorder_slider();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_customorder_slider() {
    $(".profitordercalend_slidermanage.left").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var offset=100;
            profitorder_slider_move(offset);
        }
    });
    $(".profitordercalend_slidermanage.right").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var offset=-100;
            profitorder_slider_move(offset);
        }
    });
}

function prepare_custom_export() {
    var url='/accounting/prepare_orderprofit_export';
    $.post(url,{},function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','480px');
            $("#pageModalLabel").empty().html('Select Field for Export');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#exportflds").unbind('click').click(function(){
                init_prepare_customexport();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function init_prepare_customexport() {
    var params=$("#exportfields").serializeArray();
    var search=$("#profitsearch").val();
    params.push({name:'search', value: search});
    params.push({name:'filter', value:$("#order_filtr").val()});
    if ($("input#profitdatetypechoise1").prop('checked')==true) {
        params.push({name: 'show_year', value: 1});
        params.push({name: 'year', value: $(".selectorderyeardat").val()});
        params.push({name: 'month', value: $(".selectordermonthdat").val()});
        params.push({name: 'date_bgn', value: 0});
        params.push({name: 'date_end', value: 0});
    } else {
        params.push({name: 'show_year', value: 0});
        params.push({name: 'year', value: 0});
        params.push({name: 'month', value: 0});
        params.push({name: 'date_bgn', value: $("#customdatebgn").val()});
        params.push({name: 'date_end', value: $("#customdateend").val()});
    }
    params.push({name:'shipping_country', value:$(".selectshiplocationdat").val()});
    if ($(".selectstatelocationdat").length>0) {
        params.push({name: 'shipping_state', value: $(".selectstatelocationdat").val()});
    } else {
        params.push({name: 'shipping_state', value: 0 });
    }
    params.push({name: 'order_type',value: $(".selectordertypesdat").val()});
    params.push({name: 'brand', value: $("#profitordersbrand").val()});
    params.push({name: 'exclude_quickbook', value: $("#quickbookexclude").val()});
    var url='/leads/customorder_export';
    $.post(url,params, function (response){
        if (response.errors=='') {
            window.open(response.data.url);
            $("#pageModal").modal('hide');
        } else {
            show_error(response);
        }
    },'json');
}

function change_customorder_sort(sortname,sortclass) {
    var cur_sort=$("#orderbytab1").val();
    var cur_dir=$("#directiontab1").val();
    $("div#profitordesview .orders-table-title").children().each(function(i) {
        $(this).removeClass('activesortdesc');
        $(this).removeClass('activesortasc');
    })

    if (cur_sort==sortname) {
        if (cur_dir=='asc') {
            $("#directiontab1").val('desc');
            $("div#profitordesview ."+sortclass).addClass('activesortdesc');
        } else {
            $("#directiontab1").val('asc');
            $("div#profitordesview ."+sortclass).addClass('activesortasc');
        }
    } else {
        $("#directiontab1").val('desc');
        $("#orderbytab1").val(sortname);
        $("div#profitordesview ."+sortclass).addClass('activesortdesc');
    }
    initCustomOrderPagination();
}