function init_profit_orders() {
    // initProfitOrderPagination();
    search_profit_data();
    totalyears();
    $(".profitorder_dateinpt").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'm/d/y'
    });
    $("div.exportdatacall").unbind('click').click(function(){
         prepare_export();
    });
    $("#profitsearch").keypress(function(event){
        if (event.which == 13) {
            search_profit_data();
        }
    });

    $("#find_profit").unbind('click').click(function(){
        search_profit_data();
    });

    $("#clear_profit").unbind('click').click(function(){
        clear_profit_search();
    });

    $(".profitorder_date").unbind('click').click(function(){
        change_profit_sort('o.order_date','profitorder_date');
    })
    $(".profitorder_brand").unbind('click').click(function(){
        change_profit_sort('b.brand_name','profitorder_brand');
    })
    $(".profitorder_numorder").unbind('click').click(function(){
        change_profit_sort('o.order_num','profitorder_numorder');
    })
    $(".profitorder_customer").unbind('click').click(function(){
        change_profit_sort('o.customer_name','profitorder_customer');
    })
    $(".profitorder_revenue").unbind('click').click(function(){
        change_profit_sort('o.revenue','profitorder_revenue');
    })
    $(".profitorder_confirm").unbind('click').click(function () {
        change_profit_sort('o.order_confirmation','profitorder_confirm');
    })
    /* */
    $(".profitorder_profit").unbind('click').click(function(){
        change_profit_sort('o.profit','profitorder_profit');
    })
    $(".profitorder_profitperc").unbind('click').click(function(){
        change_profit_sort('o.profit_perc','profitorder_profitperc');
    })
    $("select#order_filtr").unbind('change').change(function(){
        search_profit_data();
    })
    $("select#perpage_profitorders").unbind('change').change(function(){
        $("#curpagetab1").val(0);
        initProfitOrderPagination();
    });
    // Change type of filter
    $("#profitdatetypechoise1").unbind('click').click(function(){
        $(".selectorderyeardat").prop('disabled',false).addClass('active');
        $(".selectordermonthdat").prop('disabled',false).addClass('active');
        search_profit_data();
        $("#customdatebgn").prop('disabled',true);
        $("#customdateend").prop('disabled',true);
        $(".profitorder_dateinpt").datepicker("option", "disabled", true );
    });
    // Change Year
    $(".selectorderyeardat").unbind('change').change(function () {
        search_profit_data();
    });
    $(".selectordermonthdat").unbind('change').change(function () {
        search_profit_data();
    });
    $(".selectshiplocationdat").unbind('change').change(function(){
        if (parseInt($(".selectshiplocationdat").val())>0) {
            // Add Shipping States
            var url="/accounting/orderprofit_states";
            $.post(url, {'country_id': $(".selectshiplocationdat").val()}, function (response) {
                if (response.errors=='') {
                    $(".selectstatelocation").empty().html(response.data.content);
                    $(".selectstatelocationdat").unbind('change').change(function(){
                        search_profit_data();
                    })
                    search_profit_data();
                } else {
                    show_error(response);
                }
            },'json');
        } else {
            $(".selectstatelocation").empty().html('&nbsp;');
            search_profit_data();
        }
    });
    $(".selectordertypesdat").unbind('change').change(function(){
        search_profit_data();
    })
    $("#profitdatetypechoise2").unbind('click').click(function(){
        $(".selectorderyeardat").prop('disabled',true).removeClass('active');
        $(".selectordermonthdat").prop('disabled',true).removeClass('active');
        $("#customdatebgn").prop('disabled',false);
        $("#customdateend").prop('disabled',false);
        search_profit_data();
    });
    $("#customdatebgn").unbind('change').change(function () {
        search_profit_data();
    });
    $("#customdateend").unbind('change').change(function () {
        search_profit_data();
    });
    // Change Brand
    $("#profitordersbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#profitordersbrand").val(brand);
        $("#profitordersbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#profitordersbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#profitordersbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_profit_data();
        // Years totals
        totalyears();
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
                search_profit_data();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function change_profit_sort(sortname,sortclass) {
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
    initProfitOrderPagination();
}

/* Search Profit */
function clear_profit_search() {
    // Clear inputs, dropdowns
    $("#profitsearch").val('');
    $("#order_filtr").val(0);
    $(".selectorderyeardat").val(0);
    $("#customdatebgn").val('');
    $("#customdateend").val('');
    search_profit_data();
}

function search_profit_data() {
    var params=profile_filter_get();
    /* Recalculate total number */
    var url="/accounting/search_orders";
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
            initProfitOrderPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function initProfitOrderPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totaltab1').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpage_profitorders").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#profitorders_pagination").empty();
        pageProfitOrederCallback(0);
    } else {
        var curpage = $("#curpagetab1").val();
        // Create content inside pagination element
        $("#profitorders_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageProfitOrederCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageProfitOrederCallback(page_index) {
    var params=profile_filter_get();
    params.push({name:'limit', value:$("#perpage_profitorders").val()});
    params.push({name:'offset', value:page_index});
    params.push({name:'order_by', value:$("#orderbytab1").val()});
    params.push({name:'direction', value:$("#directiontab1").val()});
    params.push({name:'maxval', value:$('#totaltab1').val()});
    var url='/accounting/adminprofitorderdat';
    $("#loader").show();
    $.post(url,params,function(response){
        $("#loader").css('display','none');
        if (response.errors=='') {
            $("#curpagetab1").val(page_index);
            $('#tableinfotab1').empty().html(response.data.content);
            try {
                jQuery.balloon.init();
            } catch (e) {
                console.log('Ballone not init');
            }
            init_profitorder_manage();
        } else {
            show_error(response);
        }
    },'json');
}

function init_profitorder_manage() {
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
    $("#addnew").unbind('click').click(function(){
        add_neworder();
    })
    $("a.editcoglnk").unbind('click').click(function(){
        edit_cogval(this);
    })
    $("input.calcship").unbind('click').change(function(){
        change_shipprofit(this);
    })
    $("input.calcccfee").unbind('click').change(function() {
        change_ccfeeprofit(this);
    });
    $("div.profitorder_orderitem_data").unbind('click').unbind('click').click(function(){
        var order_id=$(this).data('orderid');
        edit_item(order_id);
    });
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
    $("div.profitorder_shipping_calc").find('i').unbind('click').click(function(){
        var order=$(this).parent('div.profitorder_shipping_calc').data('order');
        edit_shipping(order);
    });
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

function edit_shipping(order) {
    var url="/accounting/order_changeship";
    $.post(url, {'order_id': order}, function(response){
        if (response.errors=='') {
            $("div#profitord"+order+" div.profitorder_profit_data").empty().html(response.data.profit);
            $("div#profitord"+order+" div.profitorder_profit_data").removeClass('black').removeClass('red').removeClass('orange').removeClass('white').removeClass('green').removeClass('projprof').addClass(response.data.profit_class);
            $("div#profitord"+order+" div.profitorder_profitperc_data").empty().html(response.data.profit_perc);
            $("div#profitord"+order+" div.profitorder_profitperc_data").removeClass('black').removeClass('red').removeClass('orange').removeClass('white').removeClass('green').removeClass('projprof').addClass(response.data.profit_class);
            $("div#profitord"+order+" div.profitorder_shipping_calc").empty().html(response.data.shipinput);
            init_profitorder_manage();
        } else {
            show_error(response);
        }
    },'json');
}

function edit_item(order_id) {
    $.post('/finance/profit_orderitem', {'order_id':order_id}, function(response){
        if (response.errors=='') {
            show_popup('userdata');
            $("div#pop_content").empty().html(response.data.content);
            if (response.data.showother=='1') {
                $("div.order_itemedit_text").show();
            }
            $("select#orderitem_id").searchable();
            $("select#orderitem_id").change(function(){
                var item_id=$("select#orderitem_id").val();
                switch(item_id) {
                    case '-1':
                        $("div.order_itemedit_text").show();
                        $("div.order_itemedit_text label").empty().html($("select#orderitem_id option:selected").text());
                        break;
                    case '-2':
                        $("div.order_itemedit_text").show();
                        $("div.order_itemedit_text label").empty().html($("select#orderitem_id option:selected").text());
                        break;
                    default:
                        $("div.order_itemedit_text").hide();
                        break;
                }
            })
            $("div.order_itemedit_save").click(function(){
                save_orderitem(order_id);
            })
        } else {
            show_error(response);
        }
    }, 'json')
}

function save_orderitem(order_id) {
    var item_id=$("select#orderitem_id").val();
    var order_items=$("textarea.orderitemsvalue").val();
    var url="/finance/profit_ordersaveitem";
    $.post(url, {'order_id':order_id, 'item_id':item_id, 'order_items':order_items}, function(response){
        if (response.errors=='') {
            disablePopup();
            $("div.profitorder_orderitem_data[data-orderid="+order_id+"]").attr('title',response.data.itemname);
        } else {
            show_error(response);
        }
    }, 'json');

}

/* Edit COG */
function edit_cogval(obj) {
    var order_id=obj.id.substr(3);
    $.post('/accounting/editcog', {'order_id':order_id}, function(response){
        if (response.errors=='') {
            show_popup('editcog');
            $("div#pop_content div.editcogform").empty().html(response.data.content);
            $("#savenewcog").click(function(){
                save_newcog();
            })
            $("#popupContactClose").click(function(){
                disablePopup();
            });

        } else {
            show_error(response);
        }
    }, 'json')
}

function save_newcog() {
    var dat = $("#editcogform").serializeArray();
    var url='/finance/save_newcog';
    $.post(url, dat, function(response){
        if (response.errors=='') {
            disablePopup();
            initProfitOrderPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}


/* Delete order */
function delete_order(obj) {
    var orderid=obj.id.substr(3);
    var ordernum=$("#profitord"+orderid+" .profitorder_numorder_data").text();
    if (confirm('Are you sure you want to delete Order # '+ordernum+' ?')) {
        var url="/finance/delete_order";
        $.post(url, {'order_id':orderid, 'datq':datqry}, function(response){
            if (response.errors=='') {
                $("#totalrec").val(response.data.total);
                initProfitOrderPagination();
            } else {
                show_error(response);
            }
        }, 'json');
    }


}

/* Edit Order */
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
            } else {
                navigation_init();
            }
        } else {
            show_error(response);
        }
    },'json');
}

/* Add Order */
function add_neworder() {
    var url = '/accounting/order_brand';
    $.post(url,{}, function (response) {
        if (response.errors=='') {
            $("#pageModalLabel").empty().html('Choose Brand for New Order');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','380px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("button#savebrand").unbind('click').click(function () {
                var brand = $("#neworderbrand").val();
                $("#pageModal").modal('hide');
                add_leadorder(brand);
            })
        } else {
            show_error(response);
        }
    },'json');
}

function add_leadorder(brand) {
    var callpage = 'profitlist';
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: 0});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 1});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_onlineleadorder_edit();
            init_rushpast();
        } else {
            show_error(response);
        }
    },'json');
}

function inline_edit_init() {
    $("input#order_date").datepicker();
    $("input#shipping_date").datepicker();
    $("select#orderitemval").searchable();
    $("input#revenue").change(function(){
        change_profit(this);
    });
    $("input#shipping").change(function(){
        change_profit(this);
    });
    $("input#is_shipping").change(function(){
        change_profit(this);
    })
    $("input#tax").change(function(){
        change_profit(this);
    });
    $("a.editord").unbind('click');
    $("a#addnew").unbind('click');
    $("div.profitorder_orderitem_data").unbind('click');
    $("a.cancblnk").unbind('click').click(function(){
        initProfitOrderPagination();
    });
    /*
    $("div.profitorder_orderitem_dataedit").click(function(){
        edit_current_item();
    })
    */
    $("select#orderitemval").unbind('change').change(function(){
        var item_id=$(this).val();
        var orderitem=$("select#orderitemval :selected").text();
        $("input#order_items").val(orderitem);
        if (parseInt(item_id)<0) {
            edit_current_item(orderitem);
        }
    });
    $("a.aprblnk").unbind('dblclick');
    $("a.aprblnk").click(function(){
        saveorderprofit();
    });
}

function edit_current_item(label) {
    var params=new Array();
    params.push({name: label, value: label});
    params.push({name: 'order_items', value: $("input#order_items").val()});
    var url="/finance/orderprofit_currentitem";
    $.post(url, params, function(response){
        if (response.errors=='') {
            show_popup('userdata');
            $("div#pop_content").empty().html(response.data.content);
            $("#popupContactClose").unbind('click').click(function(){
                disablePopup();
            })
            $("div.order_itemedit_save").click(function(){
                var itemdet=$("textarea.orderitemsvalue").val();
                $("input#order_items").val(itemdet);
                disablePopup();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function change_profit(obj) {
    var curobj=obj.id;
    var curval=$("#"+curobj).val();
    /* Get Other Costs - revenue, etc */
    var revenue=$("input#revenue").val();
    var ship=$("input#shipping").val();
    var tax=$("input#tax").val();
    var others=$("input#other_cost").val();
    var cogval=$("input#order_cog").val();
    var is_shipping=0;
    if ($("input#is_shipping").prop('checked')) {
        is_shipping=1;
    }

    var url="/finance/checkprofit";
    $.post(url,{'curobj':curobj,'curval':curval,'revenue':revenue,'ship':ship,'others':others,'tax':tax,'cogval':cogval,'is_shipping':is_shipping},function(response){
        if (response.errors!='') {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
            var oldval=$("input#old"+curobj).val();
            $("input#"+curobj).val(oldval);
        } else {
            $("input#old"+curobj).val(curval);
            $("input#profit").val(response.data.profit);
            $("input#profit_perc").val(response.data.profit_perc);
        }
    },'json');
}

function saveorderprofit() {
    var dat=$("#orderedit").serializeArray();
    var year=$("select#select_year").val();
    dat.push({name: "totals_year", value: year});
    // dat.push({name: 'item_id', value: $("input#item_id").val()});
    dat.push({name: 'order_items', value: $("input#order_items").val()});
    var url="/finance/admin_ordersave";
    $("#loader").show();
    $.post(url, dat, function(data){
        if (data.errors!='') {
            $("#loader").hide();
            alert(data.errors)
            if(data.data.url !== undefined) {
                window.location.href=data.data.url;
            }
        } else {
            $("#totaltab1").val(data.data.total);
            if (data.data.order_content!='') {
                $("div#totalcntorders").empty().html(data.data.order_content);
                // $("div.totalorder").each(function(){
                //     $(this).bt({
                //         trigger: 'click',
                //         width: '463px',
                //         ajaxCache: false,
                //         positions: ['top'],
                //         ajaxPath: ["$(this).attr('href')"]
                //     });
                // })
            }
            initProfitOrderPagination();
        }
    }, 'json');

}
function change_shipprofit(obj) {
    // console.log(obj.id.substr(5));
    var objid=obj.id;
    var newval='';
    if ($("#"+objid).prop('checked')==true) {
        newval=1;
    } else {
        newval=0;
    }
    var order_id=objid.substr(5);
    var url="/finance/order_changeship";
    $.post(url, {'order_id':order_id,'shipincl':newval, 'dat':datqry}, function(response){
        if (response.errors=='') {
            $("div#profitord"+order_id+" div.profitorder_profit_data").empty().html(response.data.profit);
            $("div#profitord"+order_id+" div.profitorder_profit_data").removeClass('black').removeClass('red').removeClass('orange').removeClass('white').removeClass('green').removeClass('projprof').addClass(response.data.profit_class);
            $("div#profitord"+order_id+" div.profitorder_profitperc_data").empty().html(response.data.profit_perc);
            $("div#profitord"+order_id+" div.profitorder_profitperc_data").removeClass('black').removeClass('red').removeClass('orange').removeClass('white').removeClass('green').removeClass('projprof').addClass(response.data.profit_class);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (newval==1) {
                    $("#"+objid).prop('checked',false);
                } else {
                    $("#"+objid).prop('checked',true);
                }
            }
        }
    }, 'json');

}

function change_ccfeeprofit(obj) {

    var objid=obj.id;
    var newval='';
    if ($("#"+objid).prop('checked')==true) {
        newval=1;
    } else {
        newval=0;
    }

    var order_id=objid.substr(5);
    var url="/finance/order_changeccfee";
    $.post(url, {'order_id':order_id,'ccfee':newval, 'dat':datqry}, function(response){
        if (response.errors=='') {
            $("div#profitord"+order_id+" div.profitorder_profit_data").empty().html(response.data.profit);
            $("div#profitord"+order_id+" div.profitorder_profit_data").removeClass('black').removeClass('red').removeClass('orange').removeClass('white').removeClass('green').removeClass('projprof').addClass(response.data.profit_class);
            $("div#profitord"+order_id+" div.profitorder_profitperc_data").empty().html(response.data.profit_perc);
            $("div#profitord"+order_id+" div.profitorder_profitperc_data").removeClass('black').removeClass('red').removeClass('orange').removeClass('white').removeClass('green').removeClass('projprof').addClass(response.data.profit_class);
            $("#"+objid).attr('title',response.data.ccfee);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (newval==1) {
                    $("#"+objid).prop('checked',false);
                } else {
                    $("#"+objid).prop('checked',true);
                }
            }
        }
    }, 'json');
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

function profile_filter_get() {
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

function prepare_export() {
    var url='/accounting/prepare_orderprofit_export';
    $.post(url,{},function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','480px');
            $("#pageModalLabel").empty().html('Select Field for Export');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#exportflds").unbind('click').click(function(){
                init_prepare_export();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function init_prepare_export() {
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
    var url='/accounting/orderprofit_export';
    $.post(url,params, function (response){
        if (response.errors=='') {
            window.open(response.data.url);
            $("#pageModal").modal('hide');
        } else {
            show_error(response);
        }
    },'json');
}

function show_bottomview() {
    $("div#ordertotalscntarea").show();
}
function hide_bottomview() {
    $("div#ordertotalscntarea").hide();
}

/* Change bottom orders  */
function totalyears() {
    var url='/accounting/ordercnt_total';
    var params = new Array();
    params.push({name: 'brand', value: $("#profitordersbrand").val()});
    $.post(url,params, function(response){
        if (response.errors=='') {
            $("div#totalcntorders").empty().html(response.data.content);
            $("div.profitordertotalarea").empty().html(response.data.content);
            $("div.profitordertotalarea").css('width',response.data.slider_width).css('margin-left', response.data.margin);
            /* Hover */
            $(".orders_totals").find('div.totalorder').qtip({
                content : {
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
            slidermargin=parseInt($("div.profitordertotalarea").css('margin-left'));
            init_profitorder_slider();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_profitorder_slider() {
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

function profitorder_slider_move(offset) {
    var margin=parseInt($("div.profitordertotalarea").css('margin-left'));
    var slwidth=parseInt($("div.profitordertotalarea").css('width'));
    var slshow=parseInt($("div#weekdays-totals").css('max-width'));
    var newmargin=(margin+offset);
    if (newmargin>=0) {
        newmargin=0;
        $(".profitordercalend_slidermanage.left").removeClass('active');
    } else {
        $(".profitordercalend_slidermanage.left").addClass('active');
    }
    $("div.profitordertotalarea").animate({marginLeft:newmargin+'px'},'slow');
    // if ((slwidth+newmargin)>=slshow) {
    if ((slwidth+newmargin)>920) {
        $(".profitordercalend_slidermanage.right").addClass('active');
    } else {
        $("div.profitordertotalarea").animate({marginLeft:slidermargin+'px'},'quick');
        $(".profitordercalend_slidermanage.right").removeClass('active');
    }
    init_profitorder_slider();
}
