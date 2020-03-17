function init_paymonitor() {
    initPaymonitPagination();
    $("#addpayfilter").unbind('change').change(function(){
        show_paidsord();
    })
    $(".chkinvinput").unbind('click').click(function(){
        invite(this);
    })
    $(".chkpaid").unbind('click').click(function(){
        paybatch(this);
    })
    $("#find_paymonitor").unbind('click').click(function(){
        search_paymonitor();
    })
    $("#clear_paymonitor").unbind('click').click(function(){
        $("#monitorsearch").val('');
        search_paymonitor();
    })
    $(".add_payment").unbind('click').click(function(){
        edit_payment(this);
    })
    $("#monitorsearch").keypress(function(event){
        if (event.which == 13) {
            search_paymonitor();
        }
    });
    $("div.edit_ordernote").unbind('click').click(function(){
        edit_ordernote(this);
    })
    $("div.saveordernote").unbind('click').click(function(){
        save_ordernote();
    })
    $("div.paymonitorsort").unbind('click').click(function(){
        change_paymonitsort(this);
    })
    $("div.attachview").unbind('click').click(function(){
        view_orderattach(this);
    })
    $("div.refund").unbind('click').click(function(){
        refund_sum(this);
    })
    $("select#perpageopeninvoice").unbind('change').change(function(){
        $('#curpagetab4').val(0);
        initPaymonitPagination();
    });
    // Change Brand
    $("#paymentmonitorbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#paymentmonitorbrand").val(brand);
        $("#paymentmonitorbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#paymentmonitorbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#paymentmonitorbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_paymonitor();
    });
}

function view_orderattach(obj) {
    var order_id=obj.id.substr(7);
    var url="/accounting/order_viewattach";
    $.post(url, {'order_id':order_id}, function(response){
        if (response.errors=='') {
            var point;
            var winname;
            var url;
            for (var key in response.data.attachments) {
                point = response.data.attachments[key];
                url=point.doc_link;
                winname=point.doc_name;
                open(url,winname,'width=800,height=600');
            }
            // $("div#pop_content").empty().html(response.data.content);
        } else {
            alert(response.errors)
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function edit_ordernote(obj) {
    var order_id=obj.id.substr(7);
    var url="/accounting/order_note";
    $.post(url, {'order_id':order_id}, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','470px');
            $("#pageModalLabel").empty().html('Order Note');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal('show');
            $("div.saveordernote").unbind('click').click(function(){
                save_ordernote();
            });
        } else {
            alert(response.errors)
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');

}

function initPaymonitPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totaltab4').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpageopeninvoice").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#paymentmonitor_pagination").empty();
        pagePaymonitCallback(0);
    } else {
        var curpage = $("#curpagetab4").val();
        // Create content inside pagination element
        $("#paymentmonitor_pagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pagePaymonitCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pagePaymonitCallback(page_index) {
    var params = new Array();
    params.push({name: 'search', value: $("#monitorsearch").val()});
    params.push({name: 'paid', value: $("#addpayfilter").val()});
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpageopeninvoice").val()});
    params.push({name: 'order_by', value: $("#orderbytab4").val()});
    params.push({name: 'direction', value: $("#directiontab4").val()});
    params.push({name: 'maxval', value: $('#totaltab4').val()});
    params.push({name: 'brand', value: $("#paymentmonitorbrand").val()});
    var url = '/accounting/adminpaymonitordat';
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $('#tableinfotab4').empty().html(response.data.content);
            $("div.total_notinvoiced").empty().html('Total Not Invoiced ' + response.data.not_invoiced);
            $("div.total_notpaid").empty().html('Partial Paid Orders ' + response.data.not_paid);
            $("div.total_notinvoiced_qty").empty().html('Qty Not Invoiced ' + response.data.qty_inv);
            $("div.total_notpaid_qty").empty().html('Qty Partial Paid ' + response.data.qty_paid);
            $("#curpagetab4").val(page_index);
            $("#loader").hide();
            profit_init();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function search_paymonitor() {
    /* Search */
    var url='/accounting/calc_monitor';
    var params = new Array();
    params.push({name: 'paid', value: $("#addpayfilter").val()});
    params.push({name: 'search', value: $("#monitorsearch").val()});
    params.push({name: 'brand', value: $("#paymentmonitorbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('#totaltab4').val(response.data.totals);
            initPaymonitPagination();
        } else {
            alert(response.errors)
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function show_paidsord() {
    /* Recalculate TOTAL recs for monitor */
    var params = new Array();
    params.push({name: 'brand', value: $("#paymentmonitorbrand").val()});
    params.push({name: 'paid', value: $("#addpayfilter").val()});
    params.push({name: 'search', value: $("#monitorsearch").val()});
    var url='/accounting/calc_monitor';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('#totaltab4').val(response.data.totals);
            initPaymonitPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function refund_sum(obj) {
    var objid=obj.id;
    var receiv=0;
    if ($("#"+objid).prop('checked')==true) {
        receiv=1;
    }
    var order_id=objid.substr(6);
    /* send new value */
    var url='/accounting/paybatch';
    $.post(url, {'order_id':order_id}, function(response){
        if (response.errors=='') {
            show_popup('batchesview');
            $("div#pop_content").empty().html(response.data.content);
            /* Init Calend */
            $("input#batchselect").datepicker({
                onSelect: function(date) {
                    show_batchdate(date);
                },
                maxDate: "+0D"
            });
            $("div.batchpaynotelnk").click(function(){
                show_note();
            })
            $("select#paymethod").change(function(){
                change_paymethod();
            });
            $("a#savebatch").click(function(){
                save_refund();
            })
            $("a#popupContactClose").unbind('click').click(function(){
                close_batchpay();
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (receiv==1) {
                    $("#"+objid).prop('checked',false);
                } else {
                    $("#"+objid).prop('checked',true);
                }

            }
        }
    },'json');
}

function paybatch(obj) {
    var objid=obj.id;
    var receiv=0;
    if ($("#"+objid).prop('checked')==true) {
        receiv=1;
    }
    var order_id=objid.substr(4);
    /* send new value */
    var url='/accounting/paybatch';
    var params = new Array();
    params.push({name: 'order_id', value :order_id});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','535px');
            $("#pageModalLabel").empty().html('Would you like to add a payment to the batch?');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal('show');
            /* Init Calend */
            $("input#batchselect").datepicker({
                autoclose: true,
                endDate: "0d",
            });
            $("input#batchselect").unbind('change').change(function() {
                var newdate=$(this).val();
                show_batchdate(newdate);
            })
            $("div.batchpaynotelnk").click(function(){
                show_note();
            })
            $("select#paymethod").change(function(){
                change_paymethod();
            });
            $("#savebatch").click(function(){
                save_batch();
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (receiv==1) {
                    $("#"+objid).prop('checked',false);
                } else {
                    $("#"+objid).prop('checked',true);
                }

            }
        }
    },'json');
}

function show_note() {
    var shown=$("div.batchpaynote_content").css('display');
    if (shown=='none') {
        $("div.batchdaytabledat").css('display','none');
        $("div.batchpaynote_content").css('display','block');
        $("div.batchpaynote").empty().html('Click here to hide note');
    } else {
        $("div.batchpaynote_content").css('display','none');
        $("div.batchdaytabledat").css('display','block');
        $("div.batchpaynote").empty().html('Click here to write note');
    }
}

function show_batchdate(date) {
    var url="/accounting/batchdetailview";
    var paymeth=$("select#paymethod").val();
    $.post(url, {'date':date,'paymethod':paymeth}, function(response){
        if (response.errors=='') {
            $("div.savebatch").css('display','block');
            $("div.batchpoptable").empty().html(response.data.content);
            $("div.batchpopmentresults_value").empty().html(response.data.dayresults);
            $("div.bathpaydatedue").empty().html(response.data.dateinpt).css('display','block');
            $("input#datedue").val(response.data.datedue);
            if (response.data.edit_option=='1') {
                $("input#datdue").datepicker({
                    autoclose: true,
                });
                $("input#datdue").unbind('change').change(function () {
                    var newdate = $(this).val();
                    change_due(newdate);
                });
            }
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function change_paymethod() {
    var paymethod=$("select#paymethod").val();
    var batch_date=$("input#batchselect").val();
    if (batch_date!='') {
        var url="/accounting/batch_paymethod";
        $.post(url, {'date':batch_date,'paymethod':paymethod}, function(response){
            if (response.errors=='') {
                $("div#pop_content div.bathpaydatedue").empty().html(response.data.dateinpt).css('display','block');
                $("input#datedue").val(response.data.datedue);
                if (response.data.edit_option=='1') {
                    $("input#datdue").datepicker({
                        autoclose: true,
                    });
                    $("input#datdue").unbind('change').change(function () {
                        var newdate = $(this).val();
                        change_due(newdate);
                    });
                }
            } else {
                alert(response.errors);
                if(response.data.url !== undefined) {
                    window.location.href=response.data.url;
                }
            }
        }, 'json');
    }
}

function change_due(date) {
    $.post('/accounting/change_datedue',{'date':date},function(response){
        if (response.errors=='') {
            $("input#datedue").val(response.data.datedue);
            $("input#datdue").val(response.data.datedueformat);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}
function save_refund() {
    var date=$("input#batchselect").val();
    var paymethod=$("select#paymethod").val();
    var amount=$("input#amount").val();
    var order_id=$("input#order_id").val();
    var batch_note=$("textarea#batch_note").val();
    var datedue=$("input#datedue").val();
    var url="/accounting/saverefund"
    $.post(url, {'date':date, 'paymethod':paymethod, 'amount':amount,'order_id':order_id,'batch_note':batch_note,'datedue':datedue}, function(response){
        if (response.errors=='') {
            $("div.batchselectpay").css('display','none');
            $("div.batchselectunit").css('display','none');
            $("div.batchpaynote_content").css('display','none');
            $("div#pop_content div.savebatch").css('display','none');
            $("div#pop_content div.batchpopmentresults_value").empty().html(response.data.dayresults);
            $("div#pop_content div.batchpoptable").empty().html(response.data.content);
            $("div#pop_content div.batchdaytabledat").css('max-height','262px').css('display','block');
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_batch() {
    var url="/accounting/savebatch"
    var params = new Array();
    params.push({name: 'date', value: $("input#batchselect").val()});
    params.push({name: 'paymethod', value: $("select#paymethod").val()});
    params.push({name: 'amount', value: $("input#amount").val()});
    params.push({name: 'order_id', value: $("input#order_id").val()});
    params.push({name: 'batch_note', value: $("textarea#batch_note").val()});
    params.push({name: 'datedue', value: $("input#datedue").val()});
    params.push({name: 'brand', value: $("#paymentmonitorbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.batchselectpay").css('display','none');
            $("div.batchselectunit").css('display','none');
            $("div.batchpaynote_content").css('display','none');
            $("div.savebatch").css('display','none');
            $("div.batchpopmentresults_value").empty().html(response.data.dayresults);
            $("div.batchpoptable").empty().html(response.data.content);
            $("div.batchdaytabledat").css('max-height','262px').css('display','block');
        } else {
            show_error(response);
        }
    }, 'json');
}
// ????
function close_batchpay() {
    var order_id=$("input#order_id").val();
    var cancel=$("input#is_canceled").val();
    var url="/finance/close_batchpayadd";
    $.post(url, {'order_id':order_id,'canceled':cancel}, function(response){
        if (response.errors=='') {
            if (cancel=='1') {
                disablePopup();
                initPaymonitPagination();
            } else {
                $("div#paymon"+order_id).empty().html(response.data.content);
                /* $("div#paymon"+order_id+" a.paynotinvoice"); */
                $("div.total_notinvoiced").empty().html('Total Not Invoiced '+response.data.not_invoiced);
                $("div.total_notpaid").empty().html('Partial Paid Orders '+response.data.not_paid);
                $("div.total_notinvoiced_qty").empty().html('Qty Not Invoiced '+response.data.qty_inv);
                $("div.total_notpaid_qty").empty().html('Qty Partial Paid '+response.data.qty_paid);
                profit_init();

                disablePopup();
            }
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function invite(obj) {
    var objid=obj.id;
    var order_id=objid.substr(7);
    /* send new value */
    var chkinv=$("input#"+objid).is(':checked');
    var valinv=0;
    if (chkinv) {
        valinv=1;
    }
    var params = new Array();
    params.push({name: 'order_id', value: order_id});
    params.push({name: 'is_invited', value: valinv});
    params.push({name: 'brand', value: $("#paymentmonitorbrand").val()});
    var url='/accounting/inviteorder';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div#paymon"+order_id).empty().html(response.data.content);
            $("div.total_notinvoiced").empty().html('Total Not Invoiced '+response.data.not_invoiced);
            $("div.total_notpaid").empty().html('Partial Paid Orders '+response.data.not_paid);
            $("div.total_notinvoiced_qty").empty().html('Qty Not Invoiced '+response.data.qty_inv);
            $("div.total_notpaid_qty").empty().html('Qty Partial Paid '+response.data.qty_paid);
            profit_init();
        } else {
            show_error(response);
        }
        return false;
    }, 'json');
}

function edit_payment(obj) {
    var order_id=obj.id.substr(10);
    $.post('/accounting/customer_payment', {'order_id':order_id}, function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','245px');
            $("#pageModalLabel").empty().html('Edit Payment');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal('show');

            // show_popup('editpayment');
            // $("div#pop_content div.editcogform").empty().html(response.data.content);
            $("#savecustpaid").click(function(){
                save_payment();
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json')
}

function save_payment() {
    var dat=$("form#editpaymenform").serializeArray();
    dat.push({name: 'brand', value: $("#paymentmonitorbrand").val()});
    var url="/accounting/save_custompay";
    $.post(url,dat,function(response){
        if (response.errors=='') {
            disablePopup();
            initPaymonitPagination();
            $("div.total_notinvoiced").empty().html('Total Not Invoiced '+response.data.not_invoiced);
            $("div.total_notpaid").empty().html('Partial Paid '+response.data.not_paid);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}

function profit_init() {
    $("div.paymonitor-numorder-dat.greenprof").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'green_tooltip paymonitor_ordernum_tooltip'
        }
    });
    $("div.paymonitor-numorder-dat.whiteprof").qtip({
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
    $("div.paymonitor-numorder-dat.redprof").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'red_tooltip paymonitor_ordernum_tooltip'
        }
    });
    $("div.paymonitor-numorder-dat.orangeprof").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'orange_tooltip paymonitor_ordernum_tooltip'
        }
    });
    $("div.paymonitor-numorder-dat.blackprof").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'black_tooltip paymonitor_ordernum_tooltip'
        }
    });
    $("div.paymonitor-numorder-dat.deepblueprof").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'deepblue_tooltip paymonitor_ordernum_tooltip'
        }
    });
    $("div.paymonitor-customer-dat").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom center',
            at: 'center top',
        },
        style: {
            classes: 'paymonitor_customer_tooltip'
        }
    });
    $("img.monitorapproved").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'left center',
            at: 'center right',
        },
        style: {
            classes: 'paymonitor_attachments_tooltip'
        }
    })
    // $("img.ordernotedata").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 220,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "top",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    // $("img.monitorapproved").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 120,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "top",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    // $("div.paymonitor-codedat").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 120,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "top",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // });
    $("div.paymonitor-numorder-dat").unbind('click').click(function(){
        var order=$(this).data('order');
        edit_paymonitororder(order);
    });
    $("#addpayfilter").unbind('change').change(function(){
        show_paidsord();
    })
    $(".chkinvinput").unbind('click').click(function(){
        invite(this);
    })
    $(".chkpaid").unbind('click').click(function(){
        paybatch(this);
    })
    $("#find_ord").unbind('click').click(function(){
        search_paymonitor();
    })
    $("#clear_ord").unbind('click').click(function(){
        $("#monitorsearch").val('');
        search_paymonitor();
    })
    $(".add_payment").unbind('click').click(function(){
        edit_payment(this);
    })
    $("#monitorsearch").keypress(function(event){
        if (event.which == 13) {
            search_paymonitor();
        }
    });
    $("div.edit_ordernote").unbind('click').click(function(){
        edit_ordernote(this);
    })
    $("div.paymonitorsort").unbind('click').click(function(){
        change_paymonitsort(this);
    })
    $("div.attachview").unbind('click').click(function(){
        view_orderattach(this);
    })
    $("div.refund").unbind('click').click(function(){
        refund_sum(this);
    })
    $("select#perpageopeninvoice").unbind('change').change(function(){
        $('#curpagetab4').val(0);
        initPaymonitPagination();
    });
}

function edit_paymonitororder(order) {
    var callpage = 'paymonitor';
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: $("#profitordersbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/>');
            $("#artModal").modal('show');
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');

    // // var orderid=obj.id.substr(3);
    // var total=$("#totalrec").val()
    // var url="/finance/orderprofitdata";
    // $.post(url, {'order':order,'page': 'paymonitor'}, function(response){
    //     if (response.errors=='') {
    //         show_popup('leadorderdetailspopup');
    //         $("#pop_content").empty().html(response.data.content);
    //         init_onlineleadorder_edit();
    //     } else {
    //         show_error(response);
    //     }
    // }, 'json');

}

function save_ordernote() {
    var url="/accounting/save_ordernote";
    var order_id = $("form#ordernoteedit input#order_id").val();
    var params = new Array();
    params.push({name: 'order_id', value: order_id});
    params.push({name: 'order_note', value: $("form#ordernoteedit #order_note").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
            $("div#paymon"+order_id).empty().html(response.data.content);
            profit_init();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function change_paymonitsort(obj) {
    var divid=obj.id;
    var newsort='';
    var newdirect='';
    var oldsort=$("#orderbytab4").val();
    var olddirec=$("#directiontab4").val();
    if (divid=='monitorsort_order') {
        newsort='order_num';
    } else if (divid=='monitorsort_revenue') {
        newsort='revenue';
    }
    /* Remove active sort class */
    $("div.paymonitor-title div.paymsortactive div.sortcalclnk").empty();
    $("div.paymonitor-title div.paymsortactive").removeClass('paymsortactive');
    $("#"+divid).addClass('paymsortactive');
    if (newsort==oldsort) {
        if (olddirec=='asc') {
            newdirect='desc';
            $("#"+divid+" div.sortcalclnk").html('<img src="/img/sort_down.png" alt="Sort">');
        } else {
            newdirect='asc';
            $("#"+divid+" div.sortcalclnk").html('<img src="/img/sort_up.png" alt="Sort">');
        }
    } else {
        newdirect='desc';
        $("#"+divid+" div.sortcalclnk").html('<img src="/img/sort_down.png" alt="Sort">');
    }
    $("#orderbytab4").val(newsort);
    $("#directiontab4").val(newdirect);
    initPaymonitPagination();
}
