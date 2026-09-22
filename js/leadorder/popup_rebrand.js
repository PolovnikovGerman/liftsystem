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
    init_copyaddresses(0);
    init_fulfillment_history();
    // Art Locations , proofs, history
    init_artdata_show();
    // Payment init
    init_payment_links();
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
    if ($("#orddtls_historybox").length>0) {
        new SimpleBar(document.getElementById('orddtls_historybox'), {autoHide: false});
    }
    new SimpleBar(document.getElementById('ordercontacts_table'), {autoHide: false});
    if ($("#order_art_boxes").length>0) {
        new SimpleBar(document.getElementById('order_art_boxes'), {autoHide: false});
    }
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
    if ($("#shiptax_bodyleftmultiple").length > 0) {
        new SimpleBar(document.getElementById('shiptax_bodyleftmultiple'), {autoHide: false});
    }
}

// Switch Clay / Preview Tabs
function init_claypreview_tabs() {
    // Change tab
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
    // Click claymodel
    $(".claymodelname").unbind('click').click(function () {
        var link = $(this).data('link');
        var title = $(this).data('title');
        openai(link, title);
    });
    // Click preview
    $(".previewpicname").unbind('click').click(function () {
        var link = $(this).data('link');
        var title = $(this).data('title');
        openai(link, title);
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

function init_copyaddresses(editmode=0) {
    $("div.copyaddress").unbind('click').click(function(){
        var addresstype = $(this).data('addresstype');
        var addresid = $(this).data('address');
        var element = document.querySelector(".fulladdressview[data-addresstype='"+addresstype+"'][data-address='"+addresid+"']");
        copyOrderToClipboard(element);
        if (parseInt(editmode)==1) {
            if (addresstype=='shipping') {
                $(".inpt_addressarea[name='shipname'][data-address='"+addresid+"']").focus();
            } else {
                $(".inpt_addressarea[name='customer_name'][data-address='"+addresid+"']").focus();
            }
        }
    });
}

function init_fulfillment_history() {
    if ($(".btbox").length > 0) {
        $(".btbox_arrow").unbind('click').click(function(){
            var order = $(this).data('btbox');
            if ($(this).parent().hasClass('btbox_open')) {
                $(".btbox").removeClass('btbox_open');
                $(".btbox_arrow").empty().html('<i class="fa fa-caret-right" aria-hidden="true"></i>');
                $(".btbox_body").removeClass('btbox_open');
            } else {
                $(".btbox").removeClass('btbox_open');
                $(".btbox_arrow").empty().html('<i class="fa fa-caret-right" aria-hidden="true"></i>');
                $(".btbox_body").removeClass('btbox_open');
                $(".btbox[data-btbox='"+order+"']").addClass('btbox_open');
                $(".btbox_arrow[data-btbox='"+order+"']").empty().html('<i class="fa fa-caret-down" aria-hidden="true"></i>');
                $(".btbox_body[data-btbox='"+order+"']").addClass('btbox_open');
                $(".shipaddrs_tab").removeClass('active');
                $(".shipaddrs_body").removeClass('active');
                $(".btbox_body[data-btbox='"+order+"']").find('div.shipaddrs_tab:first').addClass('active');
                $(".btbox_body[data-btbox='"+order+"']").find('div.shipaddrs_body:first').addClass('active');
            }
        });
        $(".shipaddrs_tab").unbind('click').click(function(){
            if ($(this).hasClass('active')) {
            } else {
                var method = $(this).data('method');
                $(".shipaddrs_tab").removeClass('active');
                $(".shipaddrs_body").removeClass('active');
                $(".shipaddrs_body[data-method='"+method+"']").addClass('active');
                $(".shipaddrs_tab[data-method='"+method+"']").addClass('active');
            }
        });
        $(".btbox_icon").unbind('click').click(function(){
            var docurl = $(this).data('url');
            var docname = $(this).data('file');
            var newWin = window.open(docurl,docname,"width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
        });
    }
}

function init_artdata_show() {
    // Link in Update History View
    $(".historydetailsview").unbind('click').click(function(){
        var history = $(this).data('history');
        var params=new Array();
        params.push({name: 'artwork_history_id', value: history});
        var url="/leadordernew/show_update_details";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".historydataview").empty().html(response.data.content).show();
                $(".closehistory").unbind('click').click(function (){
                    $(".historydataview").empty().hide();
                });
            } else {
                show_error(response);
            }
        },'json');
    });
    // Proofs docs source
    $(".uploadproofs").unbind('click').click(function(){
        var profdoc = $(this).data('proofdoc');
        var params=new Array();
        params.push({name: 'proofdoc', value : profdoc});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url = "/leadordernew/showproofdoc";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                openai(response.data.proofdocurl, response.data.proofdocname);
            } else {
                show_error(response);
            }
        }, 'json');
    })
    // Art location - source
    $("div.artbox_filenameorg").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'artloc', value: $(this).data('artloc')});
        params.push({name: 'doctype', value: 'source'});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url='/leadorder/artlocation_view';
        $.post(url, params, function(response){
            if (response.errors == '') {
                if (response.data.arttype=='logo') {
                    openai(response.data.artlocurl, 'Source');
                } else {
                    var a=response.data.viewurls;
                    var numpp=1;
                    var label='';
                    a.forEach(function(entry) {
                        label='AI '+numpp;
                        openai(entry, label);
                        numpp++;
                    });
                }
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Art location - Result file
    $(".artbox_filenamevect.readyfile").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'artloc', value: $(this).data('artloc')});
        params.push({name: 'doctype', value: 'redrawn'});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url='/leadorder/artlocation_view';
        $.post(url, params, function(response){
            if (response.errors == '') {
                if (response.data.arttype=='logo') {
                    openai(response.data.artlocurl, 'AI Redrawn');
                } else {
                    var a=response.data.viewurls;
                    var numpp=1;
                    var label='';
                    a.forEach(function(entry) {
                        label='AI Redrawn '+numpp;
                        openai(entry, label);
                        numpp++;
                    });
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Templates
    // Master
    $(".templatebox_icon").unbind('click').click(function(){
        if ($(this).hasClass('master')) {
            var docurl = $(this).data('url');
            var doclabel = $(this).data('label');
            openai(docurl, doclabel);
        } else {
            var params={'ordersession': $("input#ordersession").val()}
            var url="/leadorder/art_showtemplates";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    if (parseInt(response.data.custom)==1) {
                        $("#artNextModal").find('div.modal-dialog').css('width','665px');
                        $("#artNextModal").find('.modal-title').empty().html('Item Template');
                        $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                        $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                        $("#artNextModal").on('hidden.bs.modal', function (e) {
                            $(document.body).addClass('modal-open');
                        })
                    } else {
                        for (index = 0; index < response.data.templates.length; ++index) {
                            openai(response.data.templates[index]['fileurl'], response.data.templates[index]['filename']);
                        }
                    }
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    })
}

function init_payment_links() {
    $(".balancedue_linkicon").unbind('click').click(function (){
        var element = document.querySelector("#checkoutlink");
        copyOrderToClipboard(element);
        $(element).hide();
        var url = $("#checkoutlink").val();
        var newWindow = window.open(url, 'paymentview');
    });
    $(".balancedue_send").unbind('click').click(function (){
        var url="/leadorder/prepare_checkout_invite";
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".sendcheckoutnotificationcontent").empty().html(response.data.content);
                $(".sendcheckoutnotificationarea").show();
                $(".sendcheckoutnotificationclosewin").unbind('click').click(function(){
                    $(".sendcheckoutnotificationarea").hide();
                });
                $(".invitecheckout_send").unbind('click').click(function (){
                    send_checkout_invite();
                })
            } else {
                show_error(response);
            }
        },'json');
    });
}

function send_checkout_invite() {
    var url="/leadordernew/send_checkout_invite";
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name: 'invite_name', value: $("#invitecheckoutname_to").val()});
    params.push({name: 'invite_email', value: $("#invitecheckoutemail_to").val()});
    if ($("#invitecheckoutemail_cc").length > 0) {
        // params.push({name: 'cc_name', value: $("#invitecheckoutname_cc").val()});
        params.push({name: 'cc_email', value: $("#invitecheckoutemail_cc").val()});
    }
    if ($("#invitecheckoutemail_bcc").length > 0) {
        // params.push({name: 'bcc_name', value: $("#invitecheckoutname_bcc").val()});
        params.push({name: 'bcc_email', value: $("#invitecheckoutemail_bcc").val()});
    }
    params.push({name: 'subject', value: $("#invitecheckoutsubject").val()})
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".sendcheckoutnotificationarea").hide();
            $(".orddtls_historybox").empty().html(response.data.histoyview);
            init_leadorderparts_scrolls();
            init_artdata_show();
        } else {
            show_error(response);
        }
    },'json');
}

// Init Lead Order Edit
function init_onlineleadorder_edit() {
    // Close button
    init_closebutton();
    init_leadorderparts_scrolls();
    init_claypreview_tabs();
    init_copyaddresses(1);
    init_fulfillment_history();
    init_artdata_show();
    // Edit
    init_orderdata_change();
    init_contacts_change();
    init_addneworderitem();
}

function init_orderdata_change() {
    $("input.ordercommondata").unbind('change').change(function() {
        var fldname=$(this).data('field');
        var params=new Array();
        params.push({name: 'entity', value: $(this).data('entity')});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadordernew/change_order_commondata";
        $("#loader").show();
        $.post(url, params, function(response) {
            if (response.errors=='') {
                // Update loc time
                update_locperiod(response);
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
        var url="/leadordernew/change_order_commondata";
        $.post(url, params, function(response) {
            if (response.errors=='') {
                update_locperiod(response);
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
                if (fldname==='contact_emal') {
                    if (parseInt(response.data.locstatus)===1) {
                        $("input.contactdatachk[data-contact='"+contact+"']").prop('checked',false).prop('disabled',true);
                    } else {
                        $("input.contactdatachk[data-contact='"+contact+"']").prop('disabled',false).prop('checked',true);
                    }
                }
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
                update_locperiod(response);
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
                update_locperiod(response);
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_addneworderitem() {
    // Add color
    $("span.addnewcolor").unbind('click').click(function () {
        var orderitem_id = $(this).data('orderitem');
        var params = new Array();
        params.push({name: 'item_id', value: $(this).val()});
        params.push({name: 'orderitem_id', value: orderitem_id});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url = "/leadorder/inventoryitem";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".orderitem_inventoryview").empty().html(response.data.content);
                $(".orderitem_inventoryview").show();
                init_inventory_select(orderitem_id);
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // New Item selected
    $('select.addnewitem').change(function () {
        // Save item
        var orderitem_id = $(this).data('orderitem');
        var params = new Array();
        params.push({name: 'item_id', value: $(this).val()});
        params.push({name: 'orderitem_id', value: orderitem_id});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url = "/leadorder/saveneworderitem";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".adddata_color").empty().html(response.data.outcolors);
                // $(".items_content_addprint").css('visibility','visible');
                init_addneworderitem();
                // Open color select
                if (parseInt(response.data.special)==0) {
                    // if (response.data.brand == 'SR') {
                    if (parseInt(response.data.inventoryitem) == 1) {
                        $(".adddata_qty").empty().html(response.data.qty).css('visibility','hidden');
                        $(".adddata_price").empty().html(response.data.price).css('visibility','hidden');
                        $("span.addnewcolor").trigger('click');
                    } else {
                        // Focus on
                        $(".adddata_color").find("select.orderitemcolors").focus();
                        $(".adddata_qty").empty().html(response.data.qty).css('visibility','visible');
                        $(".adddata_price").empty().html(response.data.price).css('visibility','visible');
                        init_addneworderitem();
                    }
                } else {
                    $(".adddata_qty").empty().html(response.data.qty).css('visibility','visible');;
                    $(".adddata_price").empty().html(response.data.price).css('visibility','visible');;
                    // Focus on QTY
                    $(".adddata_qty").find('input.orderitem_qty').focus();
                    $(".items_content_addprint").css('visibility','visible');
                    init_addneworderitem();
                }
                $("input[data-field='mischrg_label1']").val(response.data.mischrg_label1);
                $("input[data-field='mischrg_val1']").val(response.data.mischrg_value1);
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".adddata_qty").find('input.orderitem_qty').unbind('change').change(function () {
        var orderitem_id = $(this).data('orderitem');
        var params = Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'orderitem_id', value: orderitem_id});
        params.push({name: 'paramname', value: 'qty'})
        params.push({name: 'newval', value: $(this).val()});
        var url = "/leadorder/saveneworderitemparam";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".adddata_price").empty().html(response.data.price).css('visibility','visible');
                $(".adddata_price").find('input.orderitem_price').focus();
                $(".adddata_subtotal").css('visibility','visible');
                $(".items_content_addprint").css('visibility','visible');
                init_addneworderitem();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".adddata_price").find('input.orderitem_price').unbind('change').change(function () {
        var orderitem_id = $(this).data('orderitem');
        var params = Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'orderitem_id', value: orderitem_id});
        params.push({name: 'paramname', value: 'price'})
        params.push({name: 'newval', value: $(this).val()});
        var url = "/leadorder/saveneworderitemparam";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                // Init Print Details
                $(".items_content_addprint").trigger('click');
                init_addneworderitem();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".adddata_price").find('input.orderitem_price').on('keydown', function(event){
        let key = (event.keyCode ? event.keyCode : event.which);
        if (key==13) {
            $(".items_content_addprint").trigger('click');
        } else if (key==9) {
            $(".items_content_addprint").trigger('click');
        }
    });
    $(".items_content_addprint").unbind('click').click(function(){
        var orderitem_id = $(this).data('orderitem');
        var params = Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'orderitem_id', value: orderitem_id});
        params.push({name: 'edit', value: 1});
        var url = "/leadordernew/neworderitemimprints";
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Print details
                $(".imprintdetails_popup").empty().html(response.data.imprintview).show();
                init_imprint_details();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Cancel add item
    $(".adddata_cancel").unbind('click').click(function (){
        if (confirm('Cancel Add New Item?')==true) {
            var orderitem_id = $(this).data('orderitem');
            var params = Array();
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            params.push({name: 'orderitem_id', value: orderitem_id});
            var url = "/leadorder/cancelneworderitem";
            $.post(url, params, function (response) {
                if (response.errors == '') {
                    $("div#orderitemdataarea").empty().html(response.data.items_content);
                    if (parseInt(response.data.newitem)==1) {
                        leadordernewitem();
                    } else {
                        $(".addleadorderitem").show();
                    }
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    });
}

// Color for Inventory items
function init_inventory_select(orderitem_id) {
    $(".orderitem_inventoryview_body").find('div.datarow').hover(
        function () {
            $(this).find('div.inventorycolor').addClass('selected');
            $(this).find('div.inventorydatacell').addClass('selected');
        },
        function () {
            $(this).find('div.inventorycolor').removeClass('selected');
            $(this).find('div.inventorydatacell').removeClass('selected');
        }
    );
    $(".orderitem_inventoryview_body").find('div.datarow').unbind('click').click(function(){
        var params = Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'orderitem_id', value: orderitem_id});
        params.push({name: 'paramname', value: 'color'})
        params.push({name: 'newval', value: $(this).data('itemcolor')});
        var url="/leadorder/saveneworderitemparam";
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".adddata_color").empty().html(response.data.outcolors);
                $(".orderitem_inventoryview").hide();
                $(".adddata_qty").css('visibility','visible');
                $(".adddata_qty").find('input.orderitem_qty').focus();
                init_addneworderitem();
            } else {
                show_error(response);
            }
        },'json');
    });
}

// Manage Imprint details
function init_imprint_details() {
    $(".imprintdetailsclose").unbind('click').click(function(){
        $(".imprintdetails_popup").empty().hide();
    });
    $("input.locationactive").unbind('click').click(function(){
        var params=new Array();
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var details=$(this).data('details');
        params.push({name:'newval', value: newval});
        params.push({name:'fldname', value: 'active'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        // Save Params
        change_imprint_details(params);
    });
    $("select.locationtype").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'imprint_type'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        // Save Params
        change_imprint_details(params);
    });
    $("input.imprintrepeatnote").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'repeat_note'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        // Save Params
        change_imprint_details(params);
    });
    $("select.imprintcolorschoice").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'num_colors'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        // Save Params
        change_imprint_details(params);
    });
    $("input.imprintprice").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: $(this).data('fldname')});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        // Save Params
        change_imprint_details(params);
    });
    // Repeat Note
    $("div.repeatdetail.active").unbind('click').click(function(){
        var detail=$(this).data('details');
        edit_imprintnote(detail);
    })
    $("select.imprintlocationchoice").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'location_id'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        // Save Params
        change_imprint_details(params);
    });
    $("input.orderblankchk").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params=new Array();
        params.push({name:'newval', value:newval});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/imprintdetails_blankorder";
        $.post(url, params,function(response){
            if (response.errors=='') {
                if (newval==1) {
                    $("input.locationactive").each(function(){
                        $(this).prop('checked',false);
                        $(this).parent('div').parent('div.imprintlocdata').removeClass('active');
                    });
                    $("select.locationtype").prop('disabled', true);
                    $("div.repeatdetail").removeClass('active');
                    $("select.imprintcolorschoice").prop('disabled',true);
                    $("select.imprintlocationchoice").prop('disabled', true);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // View Location
    // $("div.locattempl.active").qtip({
    //     content: {
    //         text: function(event, api) {
    //             $.ajax({
    //                 url: api.elements.target.data('content') // Use href attribute as URL
    //             }).then(function(content) {
    //                 // Set the tooltip content upon successful retrieval
    //                 api.set('content.text', content);
    //             }, function(xhr, status, error) {
    //                 // Upon failure... set the tooltip content to error
    //                 api.set('content.text', status + ': ' + error);
    //             });
    //             return 'Loading...'; // Set some initial text
    //         }
    //     },
    //     position: {
    //         my: 'bottom right',
    //         at: 'top left',
    //     },
    //     style: 'qtip-light'
    // });

    $("div.revertimprintdetailsdata").unbind('click').click(function(){
        $(".imprintdetails_popup").empty().hide();
    });

    $(".saveimprintdetailsdata").unbind('click').click(function(){
        save_imprint_details();
    });
}

// Save changes in imprint details
function change_imprint_details(params) {
    var url="/leadorder/imprintdetails_change";
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (response.data.fldname=='active') {
                var details=response.data.details;
                var newval=response.data.newval;
                activate_imprint_details(details, newval);
            } else if (response.data.fldname=='num_colors') {
                var details=response.data.details;
                var newval=parseInt(response.data.newval);
                $("input.imprintprice[data-details='"+details+"']").prop('disabled',true);
                $("input.imprintprice[data-details='"+details+"'][data-fldname='extra_cost']").prop('disabled',false);
                // Lock print prices
                if (newval==5) {
                    $("input.imprintprice[data-details='"+details+"'][data-fldname='print_1']").prop('disabled',false);
                    $("input.imprintprice[data-details='"+details+"'][data-fldname='setup_1']").prop('disabled',false);
                } else {
                    for (i=1; i<=newval; i++) {
                        $("input.imprintprice[data-details='"+details+"'][data-fldname='print_"+i+"']").prop('disabled',false);
                        $("input.imprintprice[data-details='"+details+"'][data-fldname='setup_"+i+"']").prop('disabled',false);
                    }
                }
            } else if (response.data.fldname=='imprint_type') {
                if (response.data.newval=='REPEAT') {
                    // $("div.repeatdetail[data-details='"+response.data.details+"']").addClass('active').removeClass('full').addClass(response.data.class);
                    var brand = $("#imprinteitbrand").val();
                    // if (brand!=='SR') {
                    for (i=1; i<=4; i++) {
                        $("input.imprintprice[data-details='"+response.data.details+"'][data-fldname='setup_"+i+"']").val(response.data.setup);
                    }
                    // }
                    $("input.imprintrepeatnote[data-details='"+response.data.details+"']").prop('disabled',false);
                    $("input.imprintrepeatnote[data-details='"+response.data.details+"']").focus();
                } else {
                    // $("div.repeatdetail[data-details='"+response.data.details+"']").removeClass('active').removeClass('full');
                    for (i=1; i<=4; i++) {
                        $("input.imprintprice[data-details='"+response.data.details+"'][data-fldname='setup_"+i+"']").val(response.data.setup);
                    }
                    $("input.imprintrepeatnote[data-details='"+response.data.details+"']").prop('disabled',true);
                }
            }
            init_imprint_details();
            update_locperiod(response);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');
}

/* Activate / deactivate imprint details */
function activate_imprint_details(details, newval) {
    if (newval==1) {
        // Activate location
        $("input.orderblankchk").prop('checked',false);
        $("div.imprintlocdata[data-details='"+details+"']").addClass('active');
        $("select.locationtype[data-details='"+details+"']").prop('disabled',false);
        if ($("select.locationtype[data-details='"+details+"']").val()=='REPEAT') {
            $("input.imprintrepeatnote[data-details='"+details+"']").prop('disabled',false);
        } else {
            $("input.imprintrepeatnote[data-details='"+details+"']").prop('disabled',true);
        }
        $("select.imprintcolorschoice[data-details='"+details+"']").prop('disabled',false);
        $("input.imprintprice[data-details='"+details+"']").prop('disabled',true);
        // Lock print prices
        var colors=$("select.imprintcolorschoice[data-details='"+details+"']").val();
        if (parseInt(colors)==5) {
            $("input.imprintprice[data-details='"+details+"'][data-fldname='print_1']").prop('disabled',false);
            $("input.imprintprice[data-details='"+details+"'][data-fldname='setup_1']").prop('disabled',false);
        } else {
            for (i=1; i<=colors; i++) {
                $("input.imprintprice[data-details='"+details+"'][data-fldname='print_"+i+"']").prop('disabled',false);
                $("input.imprintprice[data-details='"+details+"'][data-fldname='setup_"+i+"']").prop('disabled',false);
            }
        }
        $("select.imprintlocationchoice[data-details='"+details+"']").prop('disabled',false);
        $("input.imprintprice[data-details='"+details+"'][data-fldname='extra_cost']").prop('disabled',false);
    } else {
        // Deactivate location
        $("div.imprintlocdata[data-details='"+details+"']").removeClass('active');
        $("input.imprintrepeatnote[data-details='"+details+"']").prop('disabled',true);
        $("select.locationtype[data-details='"+details+"']").prop('disabled',true);
        $("select.imprintcolorschoice[data-details='"+details+"']").prop('disabled',true);
        $("input.imprintprice[data-details='"+details+"']").prop('disabled',true);
        $("select.imprintlocationchoice[data-details='"+details+"']").prop('disabled',true);
        $("input.imprintprice[data-details='"+details+"'][data-fldname='extra_cost']").prop('disabled',true);
    }
}

/* Edit Repeat Note */
function edit_imprintnote(detail) {
    var params=new Array();
    params.push({name:'imprintsession', value: $("input#imprintsession").val()});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name:'details', value: detail});
    var url = '/leadordernew/edit_repeatnote';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".imprintdetail_repeat_note").empty().html(response.data.content).show();
            init_edit_repeatnote(detail);
        } else {
            show_error(response);
        }
    },'json');
}

function init_edit_repeatnote(detail) {
    $("div.order_itemedit_save").unbind('click').click(function(){
        var note=$("input#repeatnotevalue").val();
        if (note=='') {
            alert('Enter Repeat Note');
        } else {
            var params=new Array();
            params.push({name:'detail_id', value: detail});
            params.push({name:'imprintsession', value: $("input#imprintsession").val()});
            params.push({name:'ordersession', value: $("input#ordersession").val()});
            params.push({name:'repeat_note',  value: note});
            var url="/leadorder/repeatnote_save";
            $.post(url,params, function(response){
                if (response.errors=='') {
                    $(".imprintdetail_repeat_note").empty().hide();
                    $("div.repeatdetail[data-details='"+detail+"']").addClass('full');
                    init_imprint_details();
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
}

/* Save Imprint details */
function save_imprint_details() {
    var url='/leadordernew/save_imprintdetails';
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name:'imprintsession', value: $("input#imprintsession").val()});
    $.post(url, params , function(response){
        if (response.errors=='') {
            // $("#artNextModal").modal('hide');
            $(".imprintdetails_popup").empty().hide();
            $("#orderitemsarea").empty().html(response.data.itemsview);
            $(".ordtotal_price").empty().html(response.data.order_revenue);
            $(".itemsubtotal_price").empty().html(response.data.item_subtotal);
            $(".balancedueblock").empty().html(response.data.total_due);

            init_leadorderparts_scrolls();
            update_locperiod(response);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');
}


function leadordernewitem() {
    $('select.addnewitem').select2({
        dropdownParent: $('#modalLeadOrder'),
        matcher: matchStart,
    });
    $('select.addnewitem').focus(function(){
        $(".addnewitem").select2('open');
        $("input.select2-search__field").focus();
    });
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

function update_locperiod(response) {
    if ($("input#loctimeout").length > 0) {
        $("input#loctimeout").val(response.data.loctime);
    }
}

function copyOrderToClipboard(element) {
    $(element).show();
    $(element).focus();
    $(element).select();
    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Msg '+msg);
    } catch (err) {
        console.log('Oops, unable to copy');
    }
    $(element).hide();
}