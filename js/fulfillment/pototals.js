/* General view Init */
function init_purchase_orders() {
    change_totalvendors(0);
    initPurchaseOrderPagination();
    $("#curstatus").unbind('change').change(function(){
        search_purchase();
    });
    $("select#showtoplaced").unbind('change').change(function(){
        search_purchase();
    })
    $("select#sortpurch1").unbind('change').change(function(){
        sort_change();
    })
    $("select#vendorfilter").unbind('change').change(function(){
        search_purchase();
    })
    $("#pototal_perpage").unbind('change').change(function(){
        $("#curpagetab3").val(0);
        initPurchaseOrderPagination();
    });
    $("div#pofindit").unbind('click').click(function(){
        search_purchase();
    });
    $("div#pofindclear").unbind('click').click(function(){
        $("input#searchpoinput").val('');
        search_purchase();
    });
    // Click manage
    $("#searchpoinput").keypress(function(event){
        if (event.which == 13) {
            search_purchase();
        }
    });
    // Change Brand
    $("#purchaseordersbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#purchaseordersbrand").val(brand);
        $("#purchaseordersbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#purchaseordersbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#purchaseordersbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_purchase();
        // Vendors totals
        var yearval = $("select.vendpayyear").val();
        change_totalvendors(yearval);
    });
}


/* PO Pagination */
function initPurchaseOrderPagination() {
    // count entries inside the hidden content
    var num_entries = $('#pototal_total').val();
    // var perpage = itemsperpage;
    var perpage = $("#pototal_perpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".purchorder_paginator").empty();
        pagePurchaseOrederCallback(0);
    } else {
        var curpage = $("#pototal_curpage").val();
        // Create content inside pagination element
        $(".purchorder_paginator").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pagePurchaseOrederCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}
/* PO Pagination Callback */
function pagePurchaseOrederCallback(page_index) {
    var data=new Array();
    if ($("select.vendpayyear").length==0) {
        data.push({name:'year_pay', value:0});
    } else {
        data.push({name:'year_pay', value:$("select.vendpayyear").val()});
    }
    data.push({name:'offset', value:page_index});
    data.push({name:'limit', value:$("#pototal_perpage").val()});
    data.push({name:'order_by', value:$("#pototal_orderby").val()});
    data.push({name:'direction', value:$("#pototal_direc").val()});
    data.push({name:'maxval', value:$('#pototal_total').val()});
    data.push({name:'status', value:$("#curstatus").val()});
    data.push({name:'vendor_id', value:$("select#vendorfilter").val()});
    data.push({name:'placedpo', value: $("select#showtoplaced").val()});
    data.push({name:'searchpo', value: $("input#searchpoinput").val()});
    data.push({name:'totalnotplaced', value: $("input#totalnotplacedorders").val()});
    data.push({name:'brand', value: $("#purchaseordersbrand").val()});
    $("#loader").show();
    $.post('/purchaseorders/purchaseorderdat',data,function(response){
        if (response.errors=='') {
            /* Show non placed */
            if (parseInt(response.data.viewnonplace)===1) {
                $("#notplacedordersarea").show();
                $("div.nonplacetablebody").empty().html(response.data.nonplace_content);
                var maxh=parseInt($("div.nonplacetablebody").find("div.section[data-section='"+response.data.maxview+"']").css('height'));
                $("div.nonplacetablebody").find("div.section").css('height', maxh);
                $("div#tableinfotab3").css('max-height','349px');
            } else {
                $("#notplacedordersarea").hide();
                $("div#tableinfotab3").css('max-height','510px');
            }
            $('#tableinfotab3').empty().html(response.data.content);
            // $("div.paymethods").empty().html(response.data.paym);
            // $("div.totalunbilled").empty().html(response.data.unbil);
            $("#pototal_curpage").val(page_index);
            $("div.trpo").each(function(e){
                var heigth=parseInt($(this).css('height'));
                if (heigth>28) {
                    var cssheigth=heigth+'px';
                    $(this).css('line-height', cssheigth);
                    var topelem=parseInt((heigth-22)/2)+'px';
                    $(this).find('div.subaction').css('padding-top',topelem);
                    $(this).find('div.purchase-order-attach-data').css('padding-top',topelem);
                }
            })
            /* Init buttons */
            init_pototalbutons();
            $("#loader").hide();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}
/* Init PO Content */
function init_pototalbutons() {
    $("div.purchase-order-actions-data").children('a').unbind('click').click(function(){
        var order_id=$(this).parent().data('order');
        if (order_id) {
            add_notplacedpo(order_id);
        }
    })
    $("#addnewamount").unbind('click').click(function(){
        add_newamount();
    });
    $("div.subaction.editpodata").unbind('click').click(function(){
        var amount_id=$(this).data('amountid');
        edit_purchorder(amount_id);
    })
    $("div.subaction.delpodata").unbind('click').click(function(){
        var amount_id=$(this).data('amountid');
        delete_charge(amount_id);
    });
    $("div.purchase-order-ordnum-data").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'left center',
            at: 'center right',
        },
        style: {
            classes: 'orderdata_tooltip'
        }
    });
}

/* Add NOT PLACED Order PO */
function add_notplacedpo(order_id) {
    var url="/purchaseorders/purchasenotplaced_add";
    $.post(url, {'order_id': order_id}, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','625px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_poedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Add NEW PO */
function add_newamount() {
    var amountid=0;
    var url="/purchaseorders/purchaseorder_edit";
    $.post(url, {'amount_id': amountid}, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','625px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            // $("div#pop_content").empty().html(response.data.content);
            init_poedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Edit exist PO */
function edit_purchorder(amount_id) {
    var url="/purchaseorders/purchaseorder_edit";
    $.post(url, {'amount_id':amount_id}, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','625px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            // $("div#pop_content").empty().html(response.data.content);
            init_poedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* PO EDIT FUnctions */
/* Common Edit INIT */
function init_poedit() {
    // Change Ship Check
    // Add Order Data
    if ($("input#newpoorder").length>0) {
        // Lock Input fields
        lock_poeditflds(1);
        $("input#newpoorder").unbind('change').change(function(){
            order_purchase_details($(this).val());
        });
    } else {
        lock_poeditflds(0);
    }
    $("input#amount_sum").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('amount_sum', newval);
    });
    $("input#po_shipping").unbind('click').click(function(){
        var value=0;
        if ($(this).prop('checked')==true) {
            value=1;
        }
        show_amountsave();
        save_amntdetails('is_shipping', value);
    });
    $("select#vendor_id").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('vendor_id', newval);
    })
    $("select#method_id").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('method_id', newval);
    });
    $("textarea#change_comment").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('comment', newval);
    });
    $("textarea#po_comment").unbind('change').change(function(){
        var newval=$(this).val();
        show_amountsave();
        save_amntdetails('low_profit', newval);
    });
    $("div.poamount-save").unbind('click').click(function(){
        save_amount();
    });
}

/* Lock/Unlock PO Edit fields */
function lock_poeditflds(type) {
    if (type==1) {
        $("input#amount_date").prop('readonly',true);
        $("input#amount_sum").prop('readonly',true);
        $("input#po_shipping").prop('disabled',true);
        $("select#vendor_id").prop('disabled',true);
        $("select#method_id").prop('disabled',true);
        $("textarea#po_comment").prop('readonly',true);
        $("textarea#po_comment").prop('readonly',true);
    } else {
        $("input#amount_date").prop('readonly',false);
        $("input#amount_sum").prop('readonly',false);
        $("input#po_shipping").prop('disabled',false);
        $("select#vendor_id").prop('disabled',false);
        $("select#method_id").prop('disabled',false);
        $("textarea#po_comment").prop('readonly',false);
        $("textarea#po_comment").prop('readonly',false);
        // $("div.poamount-shippinginput").jqTransform();
        // $("input#po_shipping").ezMark();
        $("input#amount_date").datepicker({
            autoclose: true,
            todayHighlight: true,
        }).on("change", function() {
            show_amountsave();
            save_amntdetails('amount_date', $(this).val());
        });
    }
}

/* Check Order # in mode ADD PO */
function order_purchase_details(order_num) {
    var url="/purchaseorders/purchaseorder_details";
    $.post(url, {'order_num':order_num}, function(response){
        if (response.errors=='') {
            $("div#orderdataarea").empty().html(response.data.content);
            lock_poeditflds(0);
            show_amountsave();
        } else {
            show_error(response);
            $('input#newpoorder').val('').focus();
        }
    }, 'json');
}

/* Show Save Button () */
function show_amountsave() {
    $("div.poamount-save").show();
}
/* Save in session AMOUNT DETAILS */
function save_amntdetails(fldname, newval) {
    // STOPED THEIR
    var url="/purchaseorders/purchaseorder_amountchange";
    $.post(url, {'fld': fldname, 'value':newval}, function(response){
        if (response.errors=='') {
            if (response.data.profit_class) {
                // poprofit-data
                // $("div.profit_class").removeClass()
                $("div.poprofit-data").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profit_class);
                $("div.poprofitperc").removeClass('projprof').removeClass('green').removeClass('red').removeClass('black').removeClass('orange').removeClass('moroon').removeClass('white').addClass(response.data.profit_class);
            }
            if (response.data.profit_perc) {
                $("div.poprofitperc").empty().html(response.data.profit_perc);
            }
            if (response.data.profit) {
                $("div.poprofit-data").empty().html(response.data.profit);
            }
            $("div#lowprofitpercreasonarea").empty().html(response.data.reason);
            $("textarea#po_comment").unbind('change').change(function(){
                var newval=$(this).val();
                show_amountsave();
                save_amntdetails('reason', newval);
            });
            $("textarea#po_comment").unbind('change').change(function(){
                var newval=$(this).val();
                show_amountsave();
                save_amntdetails('reason', newval);
            });
        } else {
            show_error(response);
        }
    },'json');
}
/* Save Amount DATA to DB */
function save_amount() {
    var url="/purchaseorders/purchaseorder_amountsave";
    $("#loader").show();
    var data = new Array();
    data.push({name:'brand', value: $("#purchaseordersbrand").val()});
    $.post(url, data, function(response){
        if (response.errors=='') {
            $("input#pototal_total").val(response.data.totals);
            $("#pageModal").modal('hide');
            initPurchaseOrderPagination();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}


/* Function delete Amount */
function delete_charge(amount_id) {
    var ordernum=$("#purchaseord"+amount_id+" div.purchase-order-ordnum-data").text();
    var amountsum=$("#purchaseord"+amount_id+" div.purchase-order-amount-data").text();
    if (confirm('Are you sure you want to delete amount '+amountsum+' PO '+ordernum+' ?')) {
        var url="/purchaseorders/purchaseorder_delete";
        var params = new Array();
        params.push({name: 'amount_id', value: amount_id});
        params.push({name:'brand', value: $("#purchaseordersbrand").val()});
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#pototal_total").val(response.data.totals);
                initPurchaseOrderPagination();
            } else {
                $("#loader").hide();
                alert(response.errors);
                if(response.data.url !== undefined) {
                    window.location.href=response.data.url;
                }
            }
        },'json');

    }
}


/* Search PO */
function search_purchase() {
    var data=new Array();
    data.push({name:'showtype', value:$("select#curstatus").val()});
    data.push({name:'vendor_id', value:$("select#vendorfilter").val()});
    data.push({name:'placedpo', value: $("select#showtoplaced").val()});
    data.push({name:'searchpo', value: $("input#searchpoinput").val()});
    data.push({name:'brand', value: $("#purchaseordersbrand").val()});
    var url="/purchaseorders/purchaseorder_search";
    $.post(url, data, function(response){
        if (response.errors=='') {
            $("input#pototal_total").val(response.data.total);
            $("#pototal_curpage").val(0);
            initPurchaseOrderPagination();
        }
    }, 'json');
}

/* Change sort of PO */
function sort_change() {
    var orderby='';
    var sort1=$("#sortpurch1").val();

    if (sort1!='') {
        var sort1_ar=sort1.split("-");
        orderby=sort1_ar[0]+' '+sort1_ar[1];
    }
    $("#pototal_orderby").val(orderby);
    initPurchaseOrderPagination();
}
/* Change Result of Total View  */
function init_vendorpayments() {
    $("select.vendpayyear").unbind('change').change(function(){
        var year=$(this).val();
        change_totalvendors(year);
    })
}

/* Show Vendors payments Totals */
function change_totalvendors(year) {
    var url="/purchaseorders/purchase_vendortotals";
    var params = new Array();
    params.push({name: 'year', value: year});
    params.push({name: 'brand', value: $("#purchaseordersbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.paymethods").empty().html(response.data.content);
            init_vendorpayments();
            // Manage Purchase methods
            $(".manage-purchase-methods").unbind('click').click(function () {
                manage_purchase_methods();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function manage_purchase_methods() {
    var url="/purchaseorders/purchasemethods_edit";
    $.post(url,{}, function (response) {
        if (response.errors=='') {
            $("#modalManage").find('div.modal-body').empty().html(response.data.content);
            $("#modalManage").modal({backdrop: 'static', keyboard: false, show: true});
            init_managemethods_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function init_managemethods_edit() {
    $(".purchmethodaction.deactivate").unbind('click').click(function () {
        if (confirm('Deactivate purchase method ?')==true) {
            var params = new Array();
            params.push({name: 'method', value: $(this).data('method')});
            params.push({name: 'action', value: 0});
            var url = '/purchaseorders/purchasemethod_status';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#activemethods").empty().html(response.data.active);
                    $("#inactivemethods").empty().html(response.data.inactive);
                    init_managemethods_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    $(".purchmethodaction.activate").unbind('click').click(function () {
        if (confirm('Activate purchase method ?')==true) {
            var params = new Array();
            params.push({name: 'method', value: $(this).data('method')});
            params.push({name: 'action', value: 1});
            var url = '/purchaseorders/purchasemethod_status';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#activemethods").empty().html(response.data.active);
                    $("#inactivemethods").empty().html(response.data.inactive);
                    init_managemethods_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $(".addmethodbtn").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'method', value: $(".addnewpurchase").val()});
        var url = '/purchaseorders/purchasemethod_new';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#activemethods").empty().html(response.data.active);
                $(".addnewpurchase").val('');
                init_managemethods_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
}