var boxid;
var url;
var timerId;
var timeout=10000;
var timeoutlock=60000;
var updorders = 0;

function navigation_init() {
    // close
    $(".neworder_close").unbind("click").click(function () {
        $("#modalLeadOrder").modal("hide");
        if ($("input#currentpage").length>0) {
            var callpage=$("input#currentpage").val();
            if (callpage=='artorderlist') {
                $("#orderlist").show();
                if (parseInt(updorders)==0) {
                } else {
                    // init_orders();
                }
            } else if (callpage=='art_tasks') {
                $("#taskview").show();
                if (parseInt(updorders)==0) {
                } else {
                    init_tasks_management();
                    init_tasks_page();
                }
            } else if (callpage=='orderslist') {
                // Orders list
                if (parseInt(updorders)==0) {
                } else {
                    // search_leadorders();
                }
            } else if (callpage=='profitlist') {
                if (parseInt(updorders)==0) {
                } else {
                    // search_profit_data();
                }
            } else if (callpage=='accrecive') {
                if (parseInt(updorders)==0) {
                } else {
                    init_accounts_receivable();
                }
            } else if (callpage=='pooverview') {
                // PO Overview
                if (parseInt(updorders)==0) {
                } else {
                    init_pooverview();
                }
            }
        }
    });
    // Art history - change scroll
    new SimpleBar(document.getElementById('orddtls_historybox'), {autoHide: false});
    new SimpleBar(document.getElementById('ordercontacts_table'), {autoHide: false});
    new SimpleBar(document.getElementById('orderitemsarea'), {autoHide: false});
    new SimpleBar(document.getElementById('order_art_boxes'), {autoHide: false});
    new SimpleBar(document.getElementById('trackcodesarea'), {autoHide: false});
    $('div.claymodels_optn_box').each(function () {
        boxid = $(this).attr('id');
        new SimpleBar(document.getElementById(boxid), {autoHide: false});
    });
    $('div.previewpict_optn_box').each(function () {
        boxid = $(this).attr('id');
        new SimpleBar(document.getElementById(boxid), {autoHide: false});
    });
    $(".artproofs_optn").find('div.optn_box').each(function () {
        boxid = $(this).attr('id');
        new SimpleBar(document.getElementById(boxid), {autoHide: false});
    })
    // Orders Navigations
    $(".btnsbox-button.prevorder").unbind('click').click(function () {
        if ($(this).hasClass("unactive")) {
        } else {
            var order = $(this).data('order');
            order_navigate(order);
        }
    });
    $(".btnsbox-button.nxtorder").unbind('click').click(function () {
        if ($(this).hasClass("unactive")) {
        } else {
            var order = $(this).data('order');
            order_navigate(order);
        }
    });
    // Show PDF invoice
    $(".btnsbox-button.viewpdf").unbind('click').click(function(){
        if ($(this).hasClass("unactive")) {
        } else {
            var params=new Array();
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            var url="/leadorder/prepare_invoice";
            $.post(url,params, function(response){
                if (response.errors=='') {
                    var newWin = window.open(response.data.docurl,"Invoice","width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
                } else {
                    show_error(response)
                }
            },'json');
        }
    });
    $(".btnsbox-button.sendpdf").unbind('click').click(function(){
        if ($(this).hasClass('unactive')) {
        } else {
            prepare_send_invoice();
        }
    });
    $(".leadorderclaytab").unbind('click').click(function () {
        if ($(this).hasClass("active")) {
        } else {
            $(".leadorderpreviewtab").removeClass("active");
            $(".leadorderpreviewcontent").removeClass("active");
            $(".leadorderclaytab").addClass("active");
            $(".leadorderclaycontent").addClass("active");
        }
    });
    $(".leadorderpreviewtab").unbind('click').click(function () {
        if ($(this).hasClass("active")) {
        } else {
            $(".leadorderclaytab").removeClass("active");
            $(".leadorderclaycontent").removeClass("active");
            $(".leadorderpreviewtab").addClass("active");
            $(".leadorderpreviewcontent").addClass("active");
        }
    })
    // Art Locations and proofs
    // init_showartlocs();
    // Edit order
    $(".btnsbox-btnedit").unbind('click').click(function () {
        // edit_currentorder();
    })
}
// Change order view by click on prev / next
function order_navigate(order) {
    var params=new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name: 'brand', value: $("#leadorderbrand").val()});
    params.push({name: 'page', value: $("#currentpage").val()});
    params.push({name: 'edit', value: 0});
    var url="/leadorder/leadorder_change_new";
    $.post(url, params, function(response) {
        if (response.errors=='') {
            $("#modalLeadOrder").find('div.modal-header').empty().html(response.data.header);
            $("#modalLeadOrder").find('div.modal-body').empty().html(response.data.content);
            $("#modalLeadOrder").modal({backdrop: 'static', keyboard: false, show: true});
            // if (parseInt(response.data.cancelorder)===1) {
            //     $("#artModal").find('div.modal-header').addClass('cancelorder');
            // } else {
            //     $("#artModal").find('div.modal-header').removeClass('cancelorder');
            // }
            navigation_init();
        } else {
            show_error(response);
        }
    },'json');
}
// Prepare Send Invoice - change email, add bcc, etc
function prepare_send_invoice() {
    var url="/leadorder/leadorderinv_prepare";
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url,params,function(response){
        if (response.errors=='') {
            // $("#artNextModal").css('z-index','1060');
            // $("#artNextModal").find('div.modal-dialog').css('width','400px');
            // $("#artNextModal").find('.modal-title').empty().html('Send Email Message');
            // $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            // $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            // $("#artNextModal").on('hidden.bs.modal', function (e) {
            //     $(document.body).addClass('modal-open');
            // })
            $("#sendnotification_body").empty().html(response.data.content);
            $("#sendnotification").show();
            $("div.addbccapprove").click(function(){
                var bcctype=$(this).data('applybcc');
                if (bcctype=='hidden') {
                    $(this).data('applybcc','show').empty().html('hide bcc');
                    $("div#emailbccdata").show();
                    // $("textarea.aprovemail_message").css('height','222');
                } else {
                    $(this).data('applybcc','hidden').empty().html('add bcc');
                    $("div#emailbccdata").hide();
                    // $("textarea.aprovemail_message").css('height','241');
                }
            });
            $("div.approvemail_send").unbind('click').click(function(){
                send_invoicemail()
            });
            $("div.sendnotification_close").unbind('click').click(function(){
                $("#sendnotification_body").empty();
                $("#sendnotification").hide();
            })
        } else {
            show_error(response);
        }
    },'json');
}

// Send Invoice Mail
function send_invoicemail() {
    var order_id=$("div.approvemail_send").data('order');
    var params=new Array();
    params.push({name:'order_id',value:order_id});
    params.push({name:'from',value: $("input#approvemail_from").val()});
    params.push({name:'customer',value:$("input#approvemail_to").val()});
    params.push({name:'subject',value:$("input#approvemail_subj").val()});
    params.push({name:'message', value:$("textarea.aprovemail_message").val()});
    var bcctype=$("div.addbccapprove").data('applybcc');
    var bccmail='';
    if (bcctype=='show') {
        bccmail=$("input#approvemail_copy").val();
    }
    params.push({name:'cc', value:bccmail});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/sendinvoice";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("#sendnotification_body").empty();
            $("#sendnotification").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

// Init Lead Order Edit
function init_onlineleadorder_edit() {
    $(".neworder_close").unbind("click").click(function () {
        $("#modalLeadOrder").modal("hide");
        if ($("input#currentpage").length>0) {
            var callpage=$("input#currentpage").val();
            if (callpage=='artorderlist') {
                $("#orderlist").show();
                if (parseInt(updorders)==0) {
                } else {
                    // init_orders();
                }
            } else if (callpage=='art_tasks') {
                $("#taskview").show();
                if (parseInt(updorders)==0) {
                } else {
                    init_tasks_management();
                    init_tasks_page();
                }
            } else if (callpage=='orderslist') {
                // Orders list
                if (parseInt(updorders)==0) {
                } else {
                    // search_leadorders();
                }
            } else if (callpage=='profitlist') {
                if (parseInt(updorders)==0) {
                } else {
                    // search_profit_data();
                }
            } else if (callpage=='accrecive') {
                if (parseInt(updorders)==0) {
                } else {
                    init_accounts_receivable();
                }
            } else if (callpage=='pooverview') {
                // PO Overview
                if (parseInt(updorders)==0) {
                } else {
                    init_pooverview();
                }
            }
        }
    });

}