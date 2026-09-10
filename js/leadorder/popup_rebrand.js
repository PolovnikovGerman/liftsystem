var boxid;
var url;
var timerId;
var timeout=10000;
var timeoutlock=60000;
var updorders = 0;

function navigation_init() {
    // close
    init_closebutton();
    // new scrolls
    init_leadorderparts_scrolls();
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
    // Clay Preview tabs
    init_claypreview_tabs();
    // Art Locations and proofs
    // init_showartlocs();
    // Edit order
    $(".btnsbox-btnedit").unbind('click').click(function () {
        // edit_currentorder();
    })
}

function init_closebutton() {
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

function init_leadorderparts_scrolls() {
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
}

// Switch Clay / Preview Tabs
function init_claypreview_tabs() {
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
    // Close button
    init_closebutton();
    init_leadorderparts_scrolls();
    init_claypreview_tabs();
    init_orderdata_change();
    init_contacts_change();
}

function init_orderdata_change() {
    $("input.orderdata").unbind('change').change(function() {
        var fldname=$(this).data('field');
        var params=new Array();
        params.push({name: 'entity', value:$(this).data('entity')});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response) {
            if (response.errors=='') {
                // Total due
                // $(".totalduedataviewarea").empty().html(response.data.total_due);
                // Order Total
                $(".ordtotal_price").empty().html(response.data.order_revenue);
                // Tax value
                // $("input.salestaxcost").val(response.data.tax);
                // Items subtotal
                $(".itemsubtotal_price").empty().html(response.data.item_subtotal);
                // Update ship_company
                if (parseInt(response.data.freshship)==1) {
                    $("input.inpt_addressarea[data-fld='ship_company']").val(response.data.shipcompany);
                }
                // Update billing company
                if (parseInt(response.data.freshbill)==1) {
                    $("input.inpt_addressarea[data-field='company']").val(response.data.billcompany);
                }
                // $("input#loctimeout").val(response.data.loctime);
                // if (response.data.ordersystem=='new') {
                //     openbalancemanage(response.data.balanceopen);
                // }
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".btn-update").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'entity', value: 'message'});
        params.push({name: 'fldname', value: 'update'});
        params.push({name: 'newval', value: $('textarea[data-field="update"]').val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');

    });
}

function init_contacts_change() {
    $("input.contactdata").unbind('change').change(function() {
        var fldname=$(this).data('fld');
        var contact=$(this).data('contact');
        var params=new Array();
        params.push({name: 'fldname', value:fldname});
        params.push({name: 'contact', value:contact});
        params.push({name: 'newval', value:$(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_contact";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // if (fldname==='contact_emal') {
                //     if (parseInt(response.data.locstatus)===1) {
                //         $("input.ordecontactchk[data-contact='"+contact+"']").prop('checked',false).prop('disabled',true);
                //     } else {
                //         $("input.ordecontactchk[data-contact='"+contact+"']").prop('disabled',false).prop('checked',true);
                //     }
                // }
                // Update phone by formated value
                if (fldname==='contact_phone') {
                    $("input.contactdata[data-contact='"+contact+"'][data-fld='contact_phone']").val(response.data.contact_phone);
                }
                // Update Shipping address Name
                if (parseInt(response.data.freshship)==1) {
                    $("input.inpt_addressarea[data-fld='ship_contact']").val(response.data.shipcontact);
                }
                // Update billing address Name
                if (parseInt(response.data.freshbill)==1) {
                    $("input.inpt_addressarea[data-field='customer_name']").val(response.data.billcontact);
                }
                // $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.contactdatachk").unbind('change').change(function() {
        var fldname=$(this).data('fld');
        var contact=$(this).data('contact');
        var newval = 0;
        if ($(this).prop('checked')==true) {
            newval = 1;
        }
        var params=new Array();
        params.push({name: 'fldname', value:fldname});
        params.push({name: 'contact', value:contact});
        params.push({name: 'newval', value: newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_contact";
        $.post(url, params, function(response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}

function leadordernewitem() {

}
function init_rushpast() {
    // Edit Rush date in past
    // $("#rushpast").datepicker({
    //     autoclose: true,
    //     todayHighlight: true
    // }).on('changeDate', function (e) {
    //     var newdate = e.format(0, "yyyy-mm-dd");
    //     var params = new Array();
    //     params.push({name: 'newval', value: newdate});
    //     params.push({name: 'ordersession', value: $("input#ordersession").val()});
    //     var url = "/leadorder/change_leadorder_rushpast";
    //     $("#loader").show();
    //     $.post(url, params, function (response) {
    //         if (response.errors == '') {
    //             // $("div.orderdatechange").empty().html(response.data.order_dateview);
    //             $("input#loctimeout").val(response.data.loctime);
    //             // Change rush options
    //             $("div#rushdatalistarea").empty().html(response.data.rushview);
    //             if (parseInt(response.data.cntshipadrr) === 1) {
    //                 $("div.ship_tax_container2[data-shipadr='" + response.data.shipaddress + "']").empty().html(response.data.shipcost);
    //             } else {
    //                 $("div.multishipadresslist").empty().html(response.data.shipcost);
    //             }
    //             $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
    //             init_onlineleadorder_edit();
    //             init_rushpast();
    //             $("#loader").hide();
    //         } else {
    //             $("#loader").hide();
    //             show_error(response);
    //         }
    //     }, 'json');
    // });
    // $("#arrivedatepast").datepicker({
    //     autoclose: true,
    //     todayHighlight: true
    // }).on('changeDate', function (e) {
    //     var newdate = e.format(0, "yyyy-mm-dd");
    //     var params = new Array();
    //     params.push({name: 'newval', value: newdate});
    //     params.push({name: 'ordersession', value: $("input#ordersession").val()});
    //     var url = "/leadorder/change_leadorder_arrivepast";
    //     $("#loader").show();
    //     $.post(url, params, function (response) {
    //         if (response.errors == '') {
    //             $("input#loctimeout").val(response.data.loctime);
    //             // Change rush options
    //             $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
    //             init_onlineleadorder_edit();
    //             init_rushpast();
    //             $("#loader").hide();
    //         } else {
    //             $("#loader").hide();
    //             show_error(response);
    //         }
    //     }, 'json');
    // });
}
