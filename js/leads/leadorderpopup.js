var url;
var timerId;
var timeout=10000;
var timeoutlock=60000;
function navigation_init() {
    $(".bt-wrapper").css('visibility','hidden');
    $("div.moveprvorder").unbind('click');
    $("div.movenxtorder").unbind('click');
    $("div.moveprvorder.active").click(function(){
        var order=$(this).data('order');
        var brand = $("#root_brand").val();
        order_navigate(order, brand);
    });
    $("div.movenxtorder.active").click(function(){
        var order=$(this).data('order');
        var brand = $("#root_brand").val();
        order_navigate(order, brand);
    });  
    $("div.button_edit_text").unbind('click').click(function(){
        edit_currentorder();
    });
    $("#artModal").find('button.close').unbind('click').click(function () {
        // Check - may be we close edit content
        $("#artModal").find('div.modal-body').empty();
        $("#artModal").modal('hide');
        if ($("input#root_call_page").length>0) {
            var callpage=$("input#root_call_page").val();
            if (callpage=='artorderlist') {
                $("#orderlist").show();
                init_orders();
            } else if (callpage=='art_tasks') {
                $("#taskview").show();
                init_tasks_management();
                init_tasks_page();
            } else if (callpage=='orderslist') {
                // Orders list
                search_leadorders();
            } else if (callpage=='profitlist') {
                search_profit_data();
            } else if (callpage=='accrecive') {
                init_accounts_receivable();
            }
        }
    })
    $("input.artlocationinpt").prop('disabled', true);
    // Show Art Locat Images and AI
    init_showartlocs();
    init_orderbottom_content(0);
    //init_blinkedtext(1);
    $("div.viewmultishipdetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'edit', value: 0});
        params.push({name: 'manage', value: 0});
        var url="/leadorder/multishipview";
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','625px');
                $("#artNextModal").find('.modal-title').empty().html('Shipping Address');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
            } else {
                show_error(response);                
            }
        },'json');        
    });    
    // Show Credit App in view mode
    $("div.creditappview").unbind('click').click(function(){
        var url="/leadorder/creditapp_lines";
        $.post(url,{'edit':0}, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','1030px');
                $("#artNextModal").find('.modal-title').empty().html('Credit Account List');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                init_creditappfunc();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.dublicateorder.active").unbind('click').click(function(){
        // var ordernum=$(this).data('order');
        // if (confirm('Duplicate Order # '+ordernum+' ?')==true) {
        if (confirm('Duplicate Order ?')==true) {
            var params=new Array();
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            params.push({name: 'current_page', value: $("#curpage").val()});
            var url="/leadorder/leadorder_dublicate";
            $("#loader").show();
            $.post(url,params,function(response){
                if (response.errors=='') {
                    // $("#pop_content").empty().html(response.data.content);
                    $("#artModalLabel").empty().html(response.data.header);
                    $("#artModal").find('div.modal-body').empty().html(response.data.content);
                    clearTimeout(timerId);
                    init_onlineleadorder_edit();
                    $("#loader").hide();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }                
            },'json');            
        }
    });
    $("div.pdfprintorder.active").unbind('click').click(function(){
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
    });
    $("div.sendorder.active").unbind('click').click(function(){
        prepare_send_invoice();
    })
    // Switch template
    $("input#orderssystemcheck").unbind('change').change(function(){
        change_order_template();
    });
    // Check lock status
    // timerId = setTimeout('chklockedorder()', timeout);
    $("div.button_update").unbind('click').click(function(){
        var msg=$("textarea[data-field='update']").val();        
        var url="/leadorder/newmsgupdate";
        var params=new Array();
        params.push({name: 'newmsg', value: msg});
        params.push({name: 'updarebd', value: 1});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("div.block_6_historytext").empty().html(response.data.content);
                $("textarea[data-field='update']").val('');                
                // $("input#loctimeout").val(response.data.loctime);
                navigation_init();                
            } else {
                show_error(response);
            }
        },'json');
    }); 
    // History details
    $("a.historydetailsview").unbind('click').click(function(){
        var history=$(this).data('history');
        show_updatedetails(history);
    });    
    // Show Attempts
    $("div.chargeattemptlogcall").unbind('click').click(function(){        
        var order=$(this).data('order');
        show_chargeattempts(order);
    })
    // Show Order Discount 
    $("div.discountdescript.icon_file").popover({
        html: true,
        trigger: 'hover',
        placement: 'right'
    });
}


// Check locked status
function chklockedorder() {
    if ($("input#ordersession").length==0) {
        clearTimeout(timerId);
    } else {
        var params=new Array();    
        var status=$("input#unlockedrec").val();
        params.push({name: 'curlock', value: status});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/checklockedorder";    
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (response.data.lockstatus!=status) {
                    $("input#unlockedrec").val(response.data.lockstatus);
                    $("div#editbuttonarea").empty().html(response.data.editbutton);
                    $("div.orderssystemswitch").empty().html(response.data.switchtemplate);
                    $("input#orderssystemcheck").unbind('change').change(function(){
                        change_order_template();
                    });            
                    $("div.button_edit_text").unbind('click').click(function(){
                        edit_currentorder();
                    });                
                }            
                clearTimeout(timerId);
                timerId=setTimeout('chklockedorder()', timeout);
            } else {
                show_error(response);
            }
        }, 'json');        
    }
}

// Show Next / Previous Order
function order_navigate(order, brand) {
    var params=new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'brand', value: brand});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/leadordernavigate";
    $.post(url,params, function(response){
        if (response.errors=='') {
            $("input#orderdataid").val(order);
            $("input#ordersession").val(response.data.ordersession);            
            $("div#currentorderheaddataarea").empty().html(response.data.order_head);
            $(".moveprvorder").data('order',response.data.prvorder);            
            $(".moveprvorder").removeClass('active');
            if (parseInt(response.data.prvorder)===0) {                
                $(".moveprvorder").removeClass('active').addClass('hidden');
            } else {                
                $(".moveprvorder").removeClass('hidden').addClass('active');
            }
            $(".movenxtorder").data('order',response.data.nxtorder);
            $(".movenxtorder").removeClass('active');            
            if (parseInt(response.data.nxtorder)===0) {
                $(".movenxtorder").removeClass('active').addClass('hidden');
            } else {
                $(".movenxtorder").removeClass('hidden').addClass('active');
            }
            $("div.block_4_text2.sendorder").removeClass('hidden').removeClass('active');
            $("div.block_4_text2.pdfprintorder").removeClass('hidden').removeClass('active');
            if (response.data.order_system=='old') {
                $("div.block_4_text2.sendorder").addClass('hidden');
                $("div.block_4_text2.pdfprintorder").addClass('hidden');
            } else {
                $("div.block_4_text2.sendorder").addClass('active');
                $("div.block_4_text2.pdfprintorder").addClass('active');                
            }
            // Main Content
            // $("")
            $("div#currentorderdataarea").empty().html(response.data.content);
            chklockedorder();
            navigation_init();
        } else {
            show_error(response);
        }
    },'json');
}

function edit_currentorder() {
    var params=new Array();
    params.push({name: 'order', value: $("input#orderdataid").val()});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});    
    params.push({name: 'page', value: $("input#currentpage").val()});
    params.push({name: 'edit', value: 1});
    var url="/leadorder/leadorder_change";
    $.post(url,params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            clearTimeout(timerId);            
            init_onlineleadorder_edit();            
        } else {
            show_error(response);
        }
    },'json');    
}

function init_onlineleadorder_edit() {
    // Create Edit Timer
    create_editorder_timer();
    // Save
    $("div.orderdatasave").unbind('click').click(function(){
        save_leadorderdata();
    });
    $("div.placeorderbtn").unbind('click').click(function(){
        place_neworder();
    })
    // Cancel
    $("#artModal").find('button.close').unbind('click').click(function(){
        clearTimeout(timerId);
        var callpage=$("input#root_call_page").val();
        // Check - may be we close edit content
        if ($("input#locrecid").length>0) {
            // Clean locked record
            var locrecid=$("input#locrecid").val();
            var url="/leadorder/cleanlockedorder";
            var params=new Array();
            params.push({name: 'locrecid', value: locrecid});
            $.post(url, params, function(response){                
            },'json');                    
        }
        $("#artModal").modal('hide');
        $("#artModalLabel").empty();
        $("#artModal").find('div.modal-body').empty();
        // Current page
        if (callpage=='artorderlist') {
            $("#orderlist").show();
            init_orders();
        } else if (callpage=='art_tasks') {
            $("#taskview").show();
            init_tasks_management();
            init_tasks_page();
        } else if (callpage=='orderslist') {
            // Orders list
            search_leadorders();
        } else if (callpage=='profitlist') {
            search_profit_data();
        } else if (callpage=='paymonitor') {
            search_paymonitor();
        } else if (callpage=='accrecive') {
            init_accounts_receivable();
        }
        if (callpage=='finance') {
            disablePopup('leadorderdetailspopup');
            $("#pop_content").empty();
            init_profit_orders();
        } else if (callpage=='art_order') {
            disablePopup('leadorderdetailspopup');
            $("#pop_content").empty();
            initGeneralPagination();
        } else if (callpage=='inventory') {
            disablePopup('leadorderdetailspopup');
            $("#pop_content").empty();
            invetory_exitorder(response.data.color);
/*            } else {
            var curpage=$("input#leadorderpage").val();
            pageLeadorderCallback(curpage); */
        }
    });
    // Revert
    // $("div.button_revert_text").unbind('click').click(function(){
    //     var order=$("input#orderdataid").val();
    //     if (parseInt(order)===0) {
    //         disablePopup();
    //     } else {
    //         clearTimeout(timerId);
    //         var locrecid=$("input#locrecid").val();
    //         var url="/orders/leadorder_edit";
    //         var params=new Array();
    //         params.push({name: 'order_id', value: order});
    //         params.push({name: 'locrecid', value: locrecid});
    //         $.post(url, params, function(response){
    //             if (response.errors=='') {
    //                 $("#pop_content").empty().html(response.data.content);
    //                 $("#popupContactClose").unbind('click').click(function(){
    //                     $("#pop_content").empty();
    //                     disablePopup();
    //                     var curpage=$("input#leadorderpage").val();
    //                     pageLeadorderCallback(curpage);
    //                 });
    //                 navigation_init();
    //             } else {
    //                 show_error(response);
    //             }
    //         },'json');
    //
    //     }
    // });

    // Calendar call
    $("input#shipdatecalendinput").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    var order_date=$("input.calendarinpt").data('order');
    $("#order_date").datepicker({
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (e){
        var newdate = e.format(0,"yyyy-mm-dd");
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'order_date'});
        params.push({name: 'newval', value: newdate});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                // $("input.calendarinpt").val(response.data.order_items);
                $("div.orderdatechange").empty().html(response.data.order_dateview);
                $("input#loctimeout").val(response.data.loctime);
                // Change rush options
                if (parseInt(response.data.shipcal)==1) {
                    $("div#rushdatalistarea").empty().html(response.data.rushview);
                    if (parseInt(response.data.cntshipadrr)===1) {
                        $("div.ship_tax_container2[data-shipadr='"+response.data.shipaddress+"']").empty().html(response.data.shipcost);
                    } else {
                        $("div.multishipadresslist").empty().html(response.data.shipcost);
                    }
                    $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                }
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // $("select.order_itemnumber_select").searchable();
    $("select.order_itemnumber_select").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'item_id'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {   
                $("input[data-field='order_items']").val(response.data.order_items);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
        
    })
    $("input.inputleadorddata").unbind('change').change(function(){
        var fldname=$(this).data('field');
        var params=new Array();        
        params.push({name: 'entity', value:$(this).data('entity')});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();        
        $.post(url, params, function(response){
            if (response.errors=='') {   
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                $("input#loctimeout").val(response.data.loctime);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Update Discounts
    $("input.inputleaddiscount").unbind('change').change(function () {
        var fldname=$(this).data('field');
        var params=new Array();
        params.push({name: 'entity', value:$(this).data('entity')});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_discount";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input.inputleaddiscount[data-field='mischrg_label1']").removeClass('input_border_gray').removeClass('input_border_red').addClass(response.data.mischrg1_class);
                $("input.inputleaddiscount[data-field='mischrg_label2']").removeClass('input_border_gray').removeClass('input_border_red').addClass(response.data.mischrg2_class);
                // $("input.inputleaddiscount[data-field='discount_label']").removeClass('input_border_gray').removeClass('input_border_red').addClass(response.data.discnt_class);
                $("div.discountdescript").removeClass('empty_icon_file').removeClass('icon_file').removeClass('discountdescription_red').addClass(response.data.discnt_class);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                $("input#loctimeout").val(response.data.loctime);
                $(".discountdescript").prop('title',response.data.discnt_title);
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $("textarea.inputleadorddata").unbind('change').change(function(){
        var fldname=$(this).data('field');
        var params=new Array();        
        params.push({name: 'entity', value:$(this).data('entity')});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {  
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    $("input.chkboxleadorddata").unbind('change').change(function(){
        var params=new Array();
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var entity=$(this).data('entity');
        var fldname=$(this).data('field');
        params.push({name: 'entity', value: entity});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {                
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }
                // Left content
                if (parseInt(response.data.showbilladdress)==1) {
                    // New Order 
                    $("div#leftbillingdataarea").empty().html(response.data.leftbilling);
                }
                $("input#loctimeout").val(response.data.loctime);
                if (entity=='artwork' && fldname=='artwork_blank') {
                    if (newval==1) {
                        $("div.blankorderlogos").show();
                        $("div#newartbuttonareaview").hide();
                        $("div.imprintdataarea").find("div.items_table_line:first").find('div.items_content_description3').empty().html('blank, no imprinting');
                    } else {
                        $("div.blankorderlogos").hide();
                        $("div#newartbuttonareaview").show();
                        $("div.imprintdataarea").find("div.items_table_line:first").find('div.items_content_description3').empty();
                    }
                }
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    $("textarea.inputleadorddatas").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:$(this).data('entity')});
        params.push({name: 'fldname', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    $("div.icon_glass.active").unbind('click').click(function(){
        show_leadorditemsearch();
    });
    
    $("input#leadordercredcard").unbind('change').change(function(){
        var fldname='cc_fee';
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        change_leadorder_profit(fldname, newval);
    });
    $("input#shippingcostdata").unbind('change').change(function(){
        var fldname='shipping';
        var newval=parseFloat($(this).val());
        change_leadorder_profit(fldname, newval);
    });
    $("input#revenuevaluedata").unbind('change').change(function(){
        var fldname='revenue';
        var newval=parseFloat($(this).val());
        change_leadorder_profit(fldname, newval);
    });
    $("input#taxsalecostdata").unbind('change').change(function(){
        var fldname='tax';
        var newval=parseFloat($(this).val());
        change_leadorder_profit(fldname, newval);        
    });
    $("select.leadorder_selectreplic").unbind('change').change(function(){        
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'order_usr_repic'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});            
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    $("div.newpaymentadd").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/paymentadd";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','356px');
                $("#artNextModal").find('.modal-title').empty().html('New Manual Payment');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                init_newpayment();
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
   
    // Show Attempts
    $("div.chargeattemptlogcall").unbind('click').click(function(){        
        var order=$(this).data('order');
        show_chargeattempts(order);
    })
    // Discount description
    $("div.discountdescript").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/orderdiscount_preview";
        $.post(url, params, function(response){
            $("#artNextModal").find('div.modal-dialog').css('width','475px');
            $("#artNextModal").find('.modal-title').empty().html(response.data.title);
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("div.vectorsave_data").show();
            $("textarea.artworkusertext").focus();
            $("input#loctimeout").val(response.data.loctime);
            $("div.vectorsave_data").unbind('click').click(function(){
                save_discountdescription();
            });
            create_editorder_timer();
        },'json');        
    })
    // Upload Proof doc
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background: none;"><img src="/img/artpage/artpopup_add_btn.png" alt="Add Proof"/></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('uploadproofdoc'),
        action: '/artproofrequest/proofattach',
        uploadButtonText: '',
        multiple: false,
        debug: false,
        template: upload_templ,
        params: {
            'artwork_id': $("#uploadproofdoc").data("artwork")
        },
        allowedExtensions: ['pdf','PDF'],
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                $(".qq-upload-list").hide();
                var url='/leadorder/saveproofdocload';
                var params=new Array();
                params.push({name: 'ordersession', value: $("input#ordersession").val()});
                params.push({name: 'proofdoc', value: responseJSON.filename});
                params.push({name: 'sourcename', value: responseJSON.srcname});
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $("div#profdocsshowarea").empty().html(response.data.content).css('width', response.data.profdocwidth+'px');
                        init_leadorder_artmanage();
                    } else {
                        show_error(response);
                    }
                },'json');
            } else {
                alert(responseJSON.error);
                $("div#loader").hide();
                $("div.qq-upload-button").css('visibility','visible');
            }
        }
    });

    init_leadorder_artmanage();
    init_leadorder_contactmanage();
    init_leadorder_items();
    init_leadorder_shipping();
    init_leadorder_billing();
    init_leadorder_charges();
    init_orderbottom_content(1);
}

function save_discountdescription() {
    var params=new Array();    
    params.push({name: 'message', value: $("textarea.artworkusertext").val()});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url='/leadorder/orderdiscount_save';
    $.post(url, params, function(response){
        if (response.errors=='') {            
            $("#artNextModal").modal('hide');
            // $("div.discountdescript").removeClass('empty_icon_file').removeClass('icon_file').addClass(response.data.newclass);
            $("div.discountdescript").removeClass('empty_icon_file').removeClass('icon_file').removeClass('discountdescription_red').addClass(response.data.newclass);
            $("div.discountdescript").prop('title', response.data.newtitle);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    }, 'json');    
}

function change_leadorder_profit(fldname, newval) {
    var params=new Array();
    params.push({name: 'fldname', value: fldname});
    params.push({name: 'newval', value: newval});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/change_profit";
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("div#leadorderprofitarea").empty().html(response.data.profit_content);
            if (fldname=='revenue') {
                $(".totalduedataviewarea").empty().html(response.data.total_due);            
                $("#ordertotaloutput").empty().html(response.data.order_revenue);                
            }
            $("div.bl_subtotal_txt").empty().html(response.data.subtotal_view);
            if (response.data.ordersystem=='new') {
                openbalancemanage(response.data.balanceopen);
            }            
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();            
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);            
        }
    },'json');
}

function show_leadorditemsearch() {
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/show_itemsearch";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
            $("#artNextModal").find('div.modal-dialog').css('width','455px');
            $("#artNextModal").find('.modal-title').empty().html('Order Item');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            if (response.data.showother=='1') {
                $("div.order_itemedit_text").show();
            } else {
                $("div.order_itemedit_text").hide();
            }
            // $("select#orderitem_id").searchable();
            $('#orderitem_id').select2({
                dropdownParent: $('#artNextModal'),
                matcher: matchStart,
            });
            // $("select#orderitem_id").focus();
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
                    case '-3':
                        $("div.order_itemedit_text").show();
                        $("div.order_itemedit_text label").empty().html($("select#orderitem_id option:selected").text());
                        break;                        
                    default:
                        $("div.order_itemedit_text").hide();
                        break;
                }
            })
            $("div.order_itemedit_save").click(function(){
                save_leadorderitem();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function save_leadorderitem() {
    var params=new Array();
    params.push({name:'item_id', value:$("select#orderitem_id").val()});
    params.push({name:'order_items', value:$("textarea.orderitemsvalue").val()});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});    
    var url="/leadorder/save_orderitem";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            if (response.data.order_system=='old') {
                $("input.order_itemnumber_input").val(response.data.item_num);
                $("input.order_itemdescript_input").val(response.data.item_description);
            } else {
                $("div#orderitemdataarea").empty().html(response.data.content);
                $("div#rushdatalistarea").empty().html(response.data.rushview);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function init_leadorder_artmanage() {
    $("div.button_newart_text").unbind('click').click(function(){
        var loctype=$("select#arttypechoice").val();
        var params=new Array();
        params.push({name :'loctype', value : loctype});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});        
        var url="/leadorder/artlocation_add";
        $.post(url, params, function(response){
            if (loctype=='Logo' || loctype=='Reference') {
                $("#artNextModal").find('div.modal-dialog').css('width','455px');
                if (loctype=='Logo') {
                    $("#artNextModal").find('.modal-title').empty().html('New Logo Location');
                } else {
                    $("#artNextModal").find('.modal-title').empty().html('New Reference Location');
                }
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                init_imagelogoupload();
                $("div.artlogouploadsave_data").unbind('click').click(function(){
                    save_newleadlogoartloc(loctype);
                });
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else if(loctype=='Text') {
                $("div#artlocationsarea").empty().html(response.data.content);
                init_leadorder_artmanage();            
            } else {
                // Copy
                $("#artNextModal").find('div.modal-dialog').css('width','455px');
                $("#artNextModal").find('.modal-title').empty().html('New Repeat Location');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("div.orderarchive_save").click(function(){
                    var order_num=$("input#archiveord").val();
                    var artwork_id=$("input#newartid").val();
                    if (order_num!='') {
                        save_newartloccopy(artwork_id, order_num);
                    } else {
                        alert('Enter Order Number');
                    }
                });
            }            
        },'json');
    });
    $("div.removeartlocation").unbind('click').click(function(){
        var artloc=$(this).data('artloc');
        var arttype=$(this).data('artloctype');
        if (confirm('Remove Art Location '+arttype+'?')==true) {
            remove_leadartlocat(artloc);
        }
    });

    // Candidat to send
    $("input.sendprofdocdata").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;            
        }
        var params=new Array();
        params.push({name: 'fldname', value: 'senddoc'});
        params.push({name: 'artproof', value: $(this).data('proofdoc')});
        params.push({name: 'newval', value: newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/changeprofdoc";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#loctimeout").val(response.data.loctime);                
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Remove 
    $("div.removeproofdoc").unbind('click').click(function(){
        var profdoc=$(this).data('proofname');
        if (confirm('Remove '+profdoc+'?')) {
            var params=new Array();
            params.push({name: 'fldname', value: 'deleted'});
            params.push({name: 'artproof', value: $(this).data('proofdoc')});
            params.push({name: 'newval', value: '1'});
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            var url="/leadorder/changeprofdoc";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("div#profdocsshowarea").empty().html(response.data.content).css('width', parseInt(response.data.profdocwidth));
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();                    
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Approve
    $("div.proofs_star").unbind('click').click(function(){
        var profdoc=$(this).data('proofname');
        if (confirm('Approve '+profdoc+'?')) {
            var params=new Array();
            params.push({name: 'fldname', value: 'approved'});
            params.push({name: 'artproof', value: $(this).data('proofdoc')});
            params.push({name: 'newval', value: '1'});
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            var url="/leadorder/changeprofdoc";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("div#profdocsshowarea").empty().html(response.data.content).css('width', parseInt(response.data.profdocwidth));
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();                    
                } else {
                    show_error(response);
                }
            },'json');
        }        
    });
    // Revert
    $("div.proofs_starapproved").unbind('click').click(function(){
        var profdoc=$(this).data('proofname');
        if (confirm('Revert '+profdoc+'?')) {
            var params=new Array();
            params.push({name: 'fldname', value: 'approved'});
            params.push({name: 'artproof', value: $(this).data('proofdoc')});
            params.push({name: 'newval', value: '0'});
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            var url="/leadorder/changeprofdoc";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("div#profdocsshowarea").empty().html(response.data.content).css('width', parseInt(response.data.profdocwidth));
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();                    
                } else {
                    show_error(response);
                }
            },'json');
        }        
    });
    // Email 
    $("div.button_proofemail").unbind('click').click(function(){
        var url="/leadorder/prepare_profdocemail";
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $.post(url,params,function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','390px');
                $("#artNextModal").find('.modal-title').empty().html('Proof Doc Email');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("div.addbccapprove").click(function(){
                    var bcctype=$(this).data('applybcc');
                    if (bcctype=='hidden') {
                        $(this).data('applybcc','show').empty().html('hide bcc');
                        $("div#emailbccdata").show();
                        $("textarea.aprovemail_message").css('height','222');
                    } else {
                        $(this).data('applybcc','hidden').empty().html('add bcc');
                        $("div#emailbccdata").hide();
                        $("textarea.aprovemail_message").css('height','241');
                    }
                });
                $("div.approvemail_send").click(function(){
                    send_leadapprovemail();
                }); 
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Redo
    $("input.artundo").unbind('change').change(function(){
        var artloc=$(this).data('artloc');
        var undoval=0;
        if ($(this).prop('checked')==true) {
            undoval=1;
        }
        change_leadartlocation('redo', undoval, artloc);
    });
    $("input.artredraw").unbind('change').change(function(){
        var artloc=$(this).data('artloc');
        var redraw=0;
        if ($(this).prop('checked')==true) {
            redraw=1;
        }
        change_leadartlocation('redrawvect', redraw, artloc);
    });
    $("input.artrush").unbind('change').change(function(){
        var artloc=$(this).data('artloc');
        var rushval=0;
        if ($(this).prop('checked')==true) {
            rushval=1;
        }
        change_leadartlocation('rush', rushval, artloc);
    });
    // Redo Msg
    $("div.redrawmsgarea").unbind('click').click(function(){
        // Show Redo MSG
        var artloc=$(this).data('artloc');
        var params=new Array();
        params.push({name: 'artloc', value: artloc});
        params.push({name: 'mode', value: 'edit'});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url='/leadorder/artlocation_rdnoteview';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','475px');
                $("#artNextModal").find('.modal-title').empty().html('Redo Message');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("div.vectorsave_data").show();
                $("textarea.artworkusertext").focus();
                $("div.vectorsave_data").unbind('click').click(function(){
                    save_leadorderrdnote(artloc);
                });
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json')
    });
    $("input.art_input").unbind('click').click(function(){
        var art_id=$(this).data('artloc');               
        change_leadartlockfont(art_id);
    });
    $("div.customertext").unbind('click').click(function(){
        var art_id=$(this).data('artloc');
        change_artcustomer_text(art_id);
    });
    // Update
    $("div.button_update").unbind('click').click(function(){
        var msg=$("textarea[data-field='update']").val();
        var url="/leadorder/newmsgupdate";
        var params=new Array();
        params.push({name: 'newmsg', value: msg});
        params.push({name: 'updarebd', value: 0});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("div.block_6_historytext").empty().html(response.data.content);
                $("textarea[data-field='update']").val('');                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');
    });
    // History details
    $("a.historydetailsview").unbind('click').click(function(){
        var history=$(this).data('history');
        show_updatedetails(history);
    });    
    // Show Source and Redrawen 
    init_showartlocs();
}


// Change User Text
function change_artcustomer_text(artloc) {
    var params=new Array();
    params.push({name: 'artloc', value: artloc});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url='/leadorder/artlocation_customtextview';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','475px');
            $("#artNextModal").find('.modal-title').empty().html('Location Customer Text');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("div#popupwin").empty().html(response.data.content);
            $("div.vectorsave_data").show();
            $("textarea.artworkusertext").focus();
            $("div.vectorsave_data").click(function(){
                save_leadordercustomtext(artloc);
            });
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();            
        } else {
            show_error(response);
        }
    },'json');    
}

// Change font
function change_leadartlockfont(art_id) {
    var url="/leadorder/artlocation_fontselect";
    var params=new Array();
    params.push({name: 'art_id', value :art_id});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','1005px');
            $("#artNextModal").find('.modal-title').empty().html('Select Font');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            // $("div.imprintfonts").jqTransform();
            $("div#popupwin input.fontmanual").change(function(){
                var fontval=$(this).val();
                $("input#fontselectfor").val(fontval);
                $("div.font_button_select").addClass('active');
            })
            $("input.fontoption").click(function(){
                var fontval=$(this).val();
                $("input#fontselectfor").val(fontval);
                $("div.font_button_select").addClass('active');
            })
            /* Init Management */
            $("div.font_button_select").click(function(){
                var fontval=$("input#fontselectfor").val();
                $("input.artfont[data-artworkartid="+art_id+"]").val(fontval);
                $("#artNextModal").modal('hide');
                change_leadartlocation('font', fontval, art_id);
                $("input.art_input[data-artloc='"+art_id+"']").val(fontval);
            })
            // active
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();            
        } else {
            show_error(response);
        }
    }, 'json')    
}

function save_leadordercustomtext(artloc) {
    var params=new Array();
    params.push({name: 'artloc', value: artloc});
    params.push({name: 'message', value: $("textarea.artworkusertext").val()});
    params.push({name: 'fldname', value: 'customer_text'});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url='/leadorder/artlocation_rdnotesave';
    $.post(url, params, function(response){
        if (response.errors=='') {            
            $("#artNextModal").modal('hide');
            $("div.customertext[data-artloc='"+artloc+"']").removeClass('active').addClass(response.data.newclass);
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    }, 'json');    
}

function save_leadorderrdnote(artloc) {    
    var params=new Array();
    params.push({name: 'artloc', value: artloc});
    params.push({name: 'redraw_message', value: $("textarea.artworkusertext").val()});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url='/leadorder/artlocation_rdnotesave';
    $.post(url, params, function(response){
        if (response.errors=='') {            
            $("#artNextModal").modal('hide');
            $("div.redrawmsgarea[data-artloc='"+artloc+"']").removeClass('active').addClass(response.data.newclass);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_showartlocs() {
    // View Item Images
    $("div.icon_glass.newactive").hover(
        function(){
            var e=$(this);
            $.get(e.data('viewsrc'),function(d) {
                e.popover({
                    content: d,
                    placement: 'right',
                    html: true
                }).popover('show');
            });
        },
        function(){
            $(this).popover('hide');
        }
    );
    $("div.uploadproofdoc").popover({
        html: true,
        trigger: 'hover',
        placement: 'right'
    });
    $("div.sendedproofdoc").popover({
        html: true,
        trigger: 'hover',
        placement: 'right'
    });
    $("div.uploadproofdoc").unbind('click').click(function () {
        var profdoc = $(this).data('proofdoc');
        var params=new Array();
        params.push({name: 'proofdoc', value : profdoc});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url = "/leadorder/showproofdoc";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                openai(response.data.proofdocurl, response.data.proofdocname);
            } else {
                show_error(response);
            }
        }, 'json');
    });    
    $("div.openlocation").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'artloc', value: $(this).data('artloc')});
        params.oush({name: 'doctype', value: 'redrawn'});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url='/leadorder/artlocation_view';
        $.post(url, params, function(response){
            if (response.errors == '') {
                if (response.data.arttype=='logo') {
                    openai(response.data.artlocurl, 'AI');
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
        });        
    });    
    $("div.openlocation").unbind('click').click(function(){
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
                        label='Source '+numpp;
                        openai(entry, label);
                        numpp++;
                    });                    
                }                
            } else {
                show_error(response);
            }            
        },'json');        
    });    
    $("div.viewreadyloc").unbind('click').click(function(){
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
    /* Templates */
    $("div.artpopup_templview").click(function(){        
        show_leadtemplates();
    })
    $("div.empty_template").click(function(){
        var imgurl="/uploads/aitemp/proof_BT15000_customer_item.ai";
        openai(imgurl,'proof_BT15000_customer_item.ai');
    })
    
    function show_leadtemplates() {
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
    
    /* Show Item AI */
    $("div.item_template").click(function(){
        var itemid=$("select#order_item_id").val();
        if (itemid=='') {
            alert('Please select an item first.  Your changes cannot be saved until you do this.')
        } else if(parseInt(itemid)<1) {
            var artid=$("input#artwork_id").val();
            show_templates(artid);
        } else {
            var url="/art/art_showtemplate";
            $.post(url, {'item_id':itemid}, function(response){
                if (response.errors=='') {
                    openai(response.data.fileurl, response.data.filename);
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    });    
}

function send_leadapprovemail() {
    var artwork=$("input#artwork_id").val();
    var params=new Array();
    params.push({name:'artwork_id',value:$("input#artwork_id").val()});
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
    var url="/leadorder/sendproofs";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("div#profdocsshowarea").empty().html(response.data.content).css('width', parseInt(response.data.profdocwidth));
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();                    
        } else {
            show_error(response);
        }
    }, 'json');
}

// Change Art Location Parameter
function change_leadartlocation(locitem, newval, artloc) {
    var params=new Array();
    params.push({name: 'field', value: locitem});
    params.push({name: 'newval', value: newval});
    params.push({name: 'artloc', value: artloc});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/artlocation_change";
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (locitem=='redo') {
                if (newval==1) {
                    $("div.artlocationarea[data-artloc="+artloc+"]").removeClass('locatready');
                    $("div.viewreadyloc[data-artloc="+artloc+"]").removeClass('text_blue').addClass('text_white');
                    $("div.openlocation[data-artloc="+artloc+"]").removeClass('text_blue').addClass('text_white');
                    $("div.fonttype[data-artloc="+artloc+"]").removeClass('text_blue').addClass('text_white');
                } else {
                    $("div.artlocationarea[data-artloc="+artloc+"]").addClass('locatready');
                    $("div.viewreadyloc[data-artloc="+artloc+"]").removeClass('text_white').addClass('text_blue');
                    $("div.openlocation[data-artloc="+artloc+"]").removeClass('text_white').addClass('text_blue');
                    $("div.fonttype[data-artloc="+artloc+"]").removeClass('text_white').addClass('text_blue');
                }
            }  
            if (locitem=='redrawvect') {
                if (newval==1) {
                    $("div.artlocationarea[data-artloc="+artloc+"]").removeClass('locatready');
                    $("div.viewreadyloc[data-artloc="+artloc+"]").removeClass('text_blue').addClass('text_white');
                    $("div.openlocation[data-artloc="+artloc+"]").removeClass('text_blue').addClass('text_white');                    
                    $("div.fonttype[data-artloc="+artloc+"]").removeClass('text_blue').addClass('text_white');
                } else {
                    $("div.artlocationarea[data-artloc="+artloc+"]").addClass('locatready');
                    $("div.viewreadyloc[data-artloc="+artloc+"]").removeClass('text_white').addClass('text_blue');
                    $("div.openlocation[data-artloc="+artloc+"]").removeClass('text_white').addClass('text_blue');                    
                    $("div.fonttype[data-artloc="+artloc+"]").removeClass('text_white').addClass('text_blue');
                }
            }
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();            
        } else {
            show_error(response);
        }
    },'json');    
}

// Remove Art Location
function remove_leadartlocat(artloc) {
    var url="/leadorder/artlocation_remove";
    var params=new Array();
    params.push({name: 'artloc', value: artloc});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url,params, function(response){
        if (response.errors=='') {
            $("div#artlocationsarea").empty().html(response.data.content);
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function save_newleadlogoartloc(loctype) {
    var params=new Array();
    params.push({name: 'logo', value:$("input#filename").val()});
    params.push({name: 'loctype', value: loctype});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/artnewlocation_save";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("div#artlocationsarea").empty().html(response.data.content);
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    }, 'json');    
}

function save_newartloccopy(artwork_id, order_num) {
    var params=new Array();    
    params.push({name: 'artwork_id', value: artwork_id});
    params.push({name: 'order_num', value: order_num});
    params.push({name: 'loctype', value: 'Repeat'});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/artnewlocation_save";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("div#artlocationsarea").empty().html(response.data.content);
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    }, 'json');        
}

function init_leadorder_contactmanage() {
    $("input.ordecontactinput").unbind('change').change(function(){
        var fldname=$(this).data('field');
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
                        $("input.ordecontactchk[data-contact='"+contact+"']").prop('checked',false).prop('disabled',true);
                    } else {
                        $("input.ordecontactchk[data-contact='"+contact+"']").prop('disabled',false);
                    }
                }
                if (fldname==='contact_phone') {
                    $("input.contact_phone_input[data-contact='"+contact+"']").val(response.data.contact_phone);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.ordecontactchk").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params=new Array();        
        params.push({name: 'fldname', value:$(this).data('field')});
        params.push({name: 'contact', value:$(this).data('contact')});
        params.push({name: 'newval', value:newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_contact";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');        
    })
}

function init_leadorder_items() {
    $("div.addleadorderitem").unbind('click').click(function(){
        show_leadorditemsearch();
    });
    $("div.itemcoloradd").unbind('click').click(function(){
        var item=$(this).data('item');
        var orderitem=$(this).data('orderitem');
        var params=new Array();
        params.push({name: 'item', value: item});
        params.push({name: 'order_item', value: orderitem});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/add_itemcolor";
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("div.orderitemsarea[data-orderitem='"+orderitem+"']").empty().html(response.data.content);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.imprintdetails").unbind('click').click(function(){
        var orderitem=$(this).data('orderitem');
        show_leadorder_imprint(orderitem);
    });
    $("input.orderitem_input").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('field')});
        params.push({name: 'item', value:$(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'order_item', value: $(this).data('orderitem')});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        change_leadorder_item(params);
    });
    $("select.orderitemcolors").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('field')});
        params.push({name: 'item', value:$(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'order_item', value: $(this).data('orderitem')});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        change_leadorder_item(params);        
    });
    $("div.items_content_trash2").find('i').unbind('click').click(function(){
        var item=$(this).data('item');
        if (confirm('Delete Item '+item+'?')==true) {
            var params=new Array();
            params.push({name: 'order_item', value: $(this).data('orderitem')});
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            var url="/leadorder/orderitem_remove";
            $("#loader").show();
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                    $("div#orderitemdataarea").empty().html(response.data.content);                
                    $("div#rushdatalistarea").empty().html(response.data.rushview);                    
                    $(".totalduedataviewarea").empty().html(response.data.total_due);
                    $("#ordertotaloutput").empty().html(response.data.order_revenue);
                    $("input.salestaxcost").val(response.data.tax);
                    $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                    $("input.shippingcost").val(response.data.shipping);
                    if (parseInt(response.data.cntshipadrr)===1) {
                        $("div.ship_tax_container2[data-shipadr='"+response.data.shipaddress+"']").empty().html(response.data.shipcost);
                    } else {
                        $("div.bl_ship_tax_content").empty().html(response.data.shipcost);
                    }                    
                    // Art Locations
                    $("div#artlocationsarea").empty().html(response.data.locat_view);
                    
                    $("#loader").hide();
                    if (response.data.ordersystem=='new') {
                        openbalancemanage(response.data.balanceopen);
                    }                    
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
}

// Show Imprint Details popup
function show_leadorder_imprint(orderitem) {
    var url="/leadorder/show_itemimprint";
    var params=new Array();
    params.push({name :'order_item_id', value : orderitem});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','1077px');
            $("#artNextModal").find('.modal-title').empty().html('Order Item Imprint');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            // Init Save functions
            init_imprint_details();
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();             
        } else {
            show_error(response);
        }
    },'json');    
}

// Imprint Details manage
function init_imprint_details() {
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
    $("div.saveimprintdetailsdata").unbind('click').click(function(){
        save_imprint_details();
    });
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
    $("div.locattempl.active").qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('content') // Use href attribute as URL
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
            at: 'top left',
        },
        style: 'qtip-light'
    });

    $("div.revertimprintdetailsdata").unbind('click').click(function(){
        $("#artNextModal").modal('hide');
    });
}

// Edit Repeat Note
function edit_imprintnote(detail) {
    var params=new Array();
    params.push({name:'imprintsession', value: $("input#imprintsession").val()});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name:'details', value: detail});        
    $.colorbox({
        opacity: .7,
        transition: 'fade',
        ajax: true,
        width:440,        
        href: '/leadorder/edit_repeatnote',
        data: params,
        onComplete: function() {
            $.colorbox.resize();
            init_edit_repeatnote(detail);
        }
    });    
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
                    $.colorbox.close();
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

// Save changed Imptint Details
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
                var newval=response.data.newval;
                $("input.imprintprice[data-details='"+details+"']").prop('disabled',true);
                $("input.imprintprice[data-details='"+details+"'][data-fldname='extra_cost']").prop('disabled',false);
                // Lock print prices                
                for (i=1; i<=newval; i++) {
                    $("input.imprintprice[data-details='"+details+"'][data-fldname='print_"+i+"']").prop('disabled',false);
                    $("input.imprintprice[data-details='"+details+"'][data-fldname='setup_"+i+"']").prop('disabled',false);
                }                                
            } else if (response.data.fldname=='imprint_type') {
                if (response.data.newval=='REPEAT') {
                    // $("div.repeatdetail[data-details='"+response.data.details+"']").addClass('active').removeClass('full').addClass(response.data.class);
                    for (i=1; i<=4; i++) {
                        $("input.imprintprice[data-details='"+response.data.details+"'][data-fldname='setup_"+i+"']").val('0.00');
                    }
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
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);            
        }
    },'json');
}

// Save Imprint Details
function save_imprint_details() {
    var url='/leadorder/save_imprintdetails';
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name:'imprintsession', value: $("input#imprintsession").val()});
    
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("#ordertotaloutput").empty().html(response.data.order_revenue);            
            $("div.imprintdataarea[data-orderitem='"+response.data.order_item_id+"']").empty().html(response.data.imprint_content);
            $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
            $(".totalduedataviewarea").empty().html(response.data.total_due);
            if (parseInt(response.data.order_blank)===1) {
                $("input.chkboxleadorddata[data-field='artwork_blank']").prop('checked',true);
                $("div#newartbuttonareaview").hide();
                $("div.blankorderlogos").show();                
            } else {
                $("input.chkboxleadorddata[data-field='artwork_blank']").prop('checked',false);
                $("div#newartbuttonareaview").show();
                $("div.blankorderlogos").hide();
            }
            $("div#leadorderprofitarea").empty().html(response.data.profit_content);
            // Rush view
            if (response.data.shiprebuild==1) {
                $("#rushdatalistarea").empty().html(response.data.rushview);
                $("input.shiprushcost").val(response.data.rush_price);
            }
            // Art Location
            if (response.data.artlocchange==1) {
                $("#artlocationsarea").empty().html(response.data.locat_view);
            }
            $("input#loctimeout").val(response.data.loctime);            
            init_onlineleadorder_edit();            
        } else {
            show_error(response);
        }
    },'json');
}

// Activate / Deactivale details
function activate_imprint_details(details, newval) {
    if (newval==1) {
        $("input.orderblankchk").prop('checked',false);
        $("div.imprintlocdata[data-details='"+details+"']").addClass('active');
        $("select.locationtype[data-details='"+details+"']").prop('disabled',false);
        if ($("select.locationtype[data-details='"+details+"']").val()=='REPEAT') {
            // $("div.repeatdetail[data-details='"+details+"']").addClass('active');
            $("input.imprintrepeatnote[data-details='"+details+"']").prop('disabled',false);
        } else {
            // $("div.repeatdetail[data-details='"+details+"']").removeClass('active');
            $("input.imprintrepeatnote[data-details='"+details+"']").prop('disabled',true);
        }
        $("select.imprintcolorschoice[data-details='"+details+"']").prop('disabled',false);
        $("input.imprintprice[data-details='"+details+"']").prop('disabled',true);
        // Lock print prices
        var colors=$("select.imprintcolorschoice[data-details='"+details+"']").val();
        for (i=1; i<=colors; i++) {
            $("input.imprintprice[data-details='"+details+"'][data-fldname='print_"+i+"']").prop('disabled',false);
            $("input.imprintprice[data-details='"+details+"'][data-fldname='setup_"+i+"']").prop('disabled',false);
        }
        $("select.imprintlocationchoice[data-details='"+details+"']").prop('disabled',false);
        $("input.imprintprice[data-details='"+details+"'][data-fldname='extra_cost']").prop('disabled',false);
    } else {
        $("div.imprintlocdata[data-details='"+details+"']").removeClass('active');
        // $("div.repeatdetail[data-details='"+details+"']").removeClass('active');
        $("input.imprintrepeatnote[data-details='"+details+"']").prop('disabled',true);
        $("select.locationtype[data-details='"+details+"']").prop('disabled',true);
        $("select.imprintcolorschoice[data-details='"+details+"']").prop('disabled',true);
        $("input.imprintprice[data-details='"+details+"']").prop('disabled',true);
        $("select.imprintlocationchoice[data-details='"+details+"']").prop('disabled',true);
        $("input.imprintprice[data-details='"+details+"'][data-fldname='extra_cost']").prop('disabled',true);
    }
}

function change_leadorder_item(params) {
    var url="/leadorder/change_itemparams";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            // Update prices classes
            $("input.orderitem_price[data-orderitem='"+response.data.order_item+"']").removeClass('normal').removeClass('warningprice').addClass(response.data.price_class);
            $("input.orderitem_price[data-orderitem='"+response.data.order_item+"']").prop('title', response.data.price_title);
            $(".totalduedataviewarea").empty().html(response.data.total_due);
            $("#ordertotaloutput").empty().html(response.data.order_revenue);
            $("input.salestaxcost").val(response.data.tax);
            $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
            $("div#leadorderprofitarea").empty().html(response.data.profit_content);
            if (response.data.fldtype=='item') {                // 
                var a =response.data.subtotals;                
                for (index = 0; index < a.length; ++index) {
                    var price=a[index].split('|');
                    $("div.items_content_sub_total2[data-orderitem='"+response.data.order_item+"'][data-item='"+price[0]+"']").empty().html(price[1]);
                }                
                a=response.data.item_price;
                for (index = 0; index < a.length; ++index) {
                    var price=a[index].split('|');
                    $("input.orderitem_price[data-orderitem='"+response.data.order_item+"'][data-item='"+price[0]+"']").val(price[1]);                    
                }                
                $("div.imprintdataarea[data-orderitem='"+response.data.order_item+"']").empty().html(response.data.imprint_content);                
                
                if (parseInt(response.data.shipcalc)===1) {
                    $("input.shippingcost").val(response.data.shipping);
                    if (parseInt(response.data.cntshipadrr)==1) {                        
                        $("div.ship_tax_container2[data-shipadr='"+response.data.shipaddress+"']").empty().html(response.data.shipcost);
                    } else {
                        $("div.bl_ship_tax_content").empty().html(response.data.shipcost);
                    }
                }
            }
            if (response.data.ordersystem=='new') {
                openbalancemanage(response.data.balanceopen);
            }            
            if (response.data.warning==1) {
                // alert(response.data.shipwarn);
                init_confirmshipcost(response.data.shipwarn);
            }
            
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
            $("#loader").hide();               
        } else {
            $("#loader").hide();                
            show_error(response);
        }
    },'json');
}

function init_confirmshipcost(content) {
    $("#artNextModal").find('div.modal-dialog').css('width','455px');
    $("#artNextModal").find('.modal-title').empty().html('Change Shipping Cost');
    $("#artNextModal").find('div.modal-body').empty().html(content);
    $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
    $("#artNextModal").on('hidden.bs.modal', function (e) {
        $(document.body).addClass('modal-open');
    })
    /* Init restore shipcost */
    $("div.restoreoldshipcost").unbind('click').click(function(){
        var fldname='shipping';
        var newval=$("input#orderoldshipcostvalue").val();
        // Change Shipcost input
        $("input.shippingcost").val(newval);
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'shipping'});
        params.push({name: 'newval', value: $("input#orderoldshipcostvalue").val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (parseInt($("#citychangevalid").val())!==0) {
                    // Send info to change City URL
                    var paramcity = new Array();
                    paramcity.push({name: 'shipadr', value: $("#citychangevalid").val()});
                    paramcity.push({name: 'fldname', value: 'city'});
                    paramcity.push({name: 'newval', value: $("select.validcity").val()});
                    paramcity.push({name: 'ordersession', value: $("input#ordersession").val()});
                    var cityurl = '/leadorder/change_shipadrress';
                    $.post(cityurl, paramcity, function(response) {
                        if (response.errors=='') {
                            $(".ship_tax_input1[data-shipadr='"+$("#citychangevalid").val()+"']").val($("select.validcity").val());
                            $("input#loctimeout").val(response.data.loctime);
                        }
                    },'json');
                }
                $("#artNextModal").modal('hide');
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $("div.leavenewshipcost").unbind('click').click(function(){
        if (parseInt($("#citychangevalid").val())!==0) {
            // Send info to change City URL
            var paramcity = new Array();
            paramcity.push({name: 'shipadr', value: $("#citychangevalid").val()});
            paramcity.push({name: 'fldname', value: 'city'});
            paramcity.push({name: 'newval', value: $("select.validcity").val()});
            paramcity.push({name: 'ordersession', value: $("input#ordersession").val()});
            var cityurl = '/leadorder/change_shipadrress';
            $.post(cityurl, paramcity, function(response) {
                if (response.errors=='') {
                    $(".ship_tax_input1[data-shipadr='"+$("#citychangevalid").val()+"']").val($("select.validcity").val());
                    $("input#loctimeout").val(response.data.loctime);
                }
            },'json');
        }
        $("#artNextModal").modal('hide');
        init_onlineleadorder_edit();
    });
    $("div.confirmshipcost_container").find('div.shipoption').unbind('click').click(function(){
        var stype="old";
        if ($(this).hasClass('newship')) {
            stype='new';
        }
        $("div.confirmshipcost_container").find('div.shipoption').empty().html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
        if (stype=='new') {
            $("div.confirmshipcost_container").find('div.newship').empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
        } else {
            $("div.confirmshipcost_container").find('div.oldship').empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
        }
        $("#shiptypeselect").val(stype);
    });
    $("div.confirmshipcost_container").find('div.savewarning').find('img').unbind('click').click(function () {
        if (parseInt($("#citychangevalid").val())!==0) {
            // Send info to change City URL
            var paramcity = new Array();
            paramcity.push({name: 'shipadr', value: $("#citychangevalid").val()});
            paramcity.push({name: 'fldname', value: 'city'});
            paramcity.push({name: 'newval', value: $("select.validcity").val()});
            paramcity.push({name: 'ordersession', value: $("input#ordersession").val()});
            var cityurl = '/leadorder/change_shipadrress';
            $.post(cityurl, paramcity, function(response) {
                if (response.errors=='') {
                    $(".ship_tax_input1[data-shipadr='"+$("#citychangevalid").val()+"']").val($("select.validcity").val());
                    $("input#loctimeout").val(response.data.loctime);
                }
            },'json');
        }
        if (parseInt($("#warnshipchange").val())>0 && $("#shiptypeselect").val()=='old') {
            var params=new Array();
            var url="/leadorder/change_leadorder_item";
            var newval=$("input#orderoldshipcostvalue").val();
            // Change Shipcost input
            $("input.shippingcost").val(newval);
            var params=new Array();
            params.push({name: 'entity', value:'order'});
            params.push({name: 'fldname', value: 'shipping'});
            params.push({name: 'newval', value: $("input#orderoldshipcostvalue").val()});
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            $("#loader").show();
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $(".totalduedataviewarea").empty().html(response.data.total_due);
                    $("#ordertotaloutput").empty().html(response.data.order_revenue);
                    $("input.salestaxcost").val(response.data.tax);
                    $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                    $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                    if (response.data.ordersystem=='new') {
                        openbalancemanage(response.data.balanceopen);
                    }
                    $("input#loctimeout").val(response.data.loctime);
                    $("#loader").hide();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
        $("#artNextModal").modal('hide');
        init_onlineleadorder_edit();
    });
}

function init_leadorder_shipping() {
    $("input.eventdatevalue").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("input.eventdatevalue").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'shipping'});
        params.push({name: 'fldname', value: 'event_date'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                // Shippings Dates 
                $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                if (response.data.warning==1) {
                    // alert(response.data.shipwarn);
                    init_confirmshipcost(response.data.shipwarn);
                }
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.shiprashselect").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'shipping'});
        params.push({name: 'fldname', value: 'rush_idx'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {  
                $("input.shiprushcost").val(response.data.rush_price);
                if (parseInt(response.data.is_shipping)===1) {
                    $("input.shippingcost").val(response.data.shipping);
                    if (parseInt(response.data.cntshipadrr)===1) {
                        $("div.ship_tax_container2[data-shipadr='"+response.data.shipaddress+"']").empty().html(response.data.shipcost);
                    } else {
                        $("div.multishipadresslist").empty().html(response.data.shipcost);
                    }
                }
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }      
                if (parseInt(response.data.rushallow)==0) {                    
                    $("input.chkboxleadorddata[data-field='artwork_rush']").attr('checked','checked').prop('disabled',true);
                } else {
                    $("input.chkboxleadorddata[data-field='artwork_rush']").prop('disabled',false);
                }
                $("input#loctimeout").val(response.data.loctime);
                $("#loader").hide();
                if (response.data.warning==1) {
                    // alert(response.data.shipwarn);
                    init_confirmshipcost(response.data.shipwarn);
                }
                init_onlineleadorder_edit();                
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    $("input.shiprushcost").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'shipping'});
        params.push({name: 'fldname', value: 'rush_price'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {         
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }            
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');        
    });
    $("input.shippingcost").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'shipping'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {     
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }            
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');        
    });
    $("select.shipcountryselect").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: 'country_id'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div[data-content='shipstateshow'][data-shipadr='"+response.data.shipaddress+"']").empty().html(response.data.stateview);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                if (response.data.taxview.length>0) {
                    $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                }
                $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);                
                $("#loader").hide();
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }            
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    $("input.shipadrchk").unbind('change').change(function(){
        var newval=1;
        if ($(this).prop('checked')==false) {
            newval=0;
        }
        var fldname=$(this).data('fldname');
        var params=new Array();
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value:  newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (fldname=='resident') {
                    if (newval==1) {
                        $("div#residentlabel").removeClass('shipblind');
                    } else {
                        $("div#residentlabel").addClass('shipblind');
                    }
                } 
                if (fldname=='ship_blind') {
                    if (newval==1) {
                        $("div#shblindlabel").removeClass('shipblind');
                    } else {
                        $("div#shblindlabel").addClass('shipblind');
                    }
                }
                $("#loader").hide();
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');
    });
    $("textarea.ship_tax_textarea").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: 'address'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#loader").hide(); 
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');                
    });
    $("input.ship_tax_textareainpt").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#loader").hide();    
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');
    })
    $("input.ship_tax_input1").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: 'city'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();        
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                if (response.data.taxview.length>0) {
                    $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                }                
                $("#loader").hide(); 
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');        
    });
    $("select.ship_tax_select2").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: 'state_id'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").hide();        
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                if (response.data.taxview.length>0) {
                    $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                }                
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);                
                $("#loader").hide();        
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }            
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');
    });
    
    $("input.ship_tax_input2").unbind('change').change(function(){
        var params=new Array();        
        var shipdata=$(this).data('shipadr');
        params.push({name: 'shipadr', value: shipdata});
        params.push({name: 'fldname', value: 'zip'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $("#loader").show();
        var url="/leadorder/change_shipadrress";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.ship_tax_container2[data-shipadr='"+response.data.shipaddress+"']").empty().html(response.data.shipcost);
                $("input.shippingcost").val(response.data.shipping);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                if (response.data.taxview.length>0) {
                    $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                }                
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                $("input.ship_tax_input1[data-shipadr='"+shipdata+"']").val(response.data.city);
                $("select.ship_tax_select2[data-shipadr='"+shipdata+"']").val(response.data.state_id);
                $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                $("#loader").hide();  
                if (response.data.warning==1) {
                    // alert(response.data.shipwarn);
                    init_confirmshipcost(response.data.shipwarn);
                }
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');        
    });
    
    $("input.ship_tax_radio").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});        
        params.push({name: 'fldname', value: 'shipping_method'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipcost";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".ship_tax_cont2_line").addClass('opast');
                $(".shiprateshowarea").removeClass('active');
                $(".ship_tax_cont2_line[data-shipcost='"+response.data.order_shipcost_id+"']").removeClass('opast');
                $(".shiprateshowarea[data-shipcost='"+response.data.order_shipcost_id+"']").addClass('active');
                $("input.shippingcost").val(response.data.shipping);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);                                
                $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                $("#loader").hide(); 
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');        
    });
    // Tax excepts
    $("input.excepttax").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: 'tax_exempt'});
        params.push({name: 'newval', value: newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (newval==1) {
                    $("select.taxexcept_select[data-shipadr='"+response.data.shipaddress+"']").prop('disabled',false);
                    $("div.taxexceptdoc[data-shipadr='"+response.data.shipaddress+"']").removeClass('nonactive').addClass('active');
                } else {
                    $("select.taxexcept_select[data-shipadr='"+response.data.shipaddress+"']").prop('disabled',true);
                    $("div.taxexceptdoc[data-shipadr='"+response.data.shipaddress+"']").removeClass('active').addClass('nonactive');
                }         
                
                $("input.shippingcost").val(response.data.shipping);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                if (response.data.taxview.length>0) {
                    $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                }                
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);                
                $("#loader").hide();  
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');
    });
    $("select.taxexcept_select").unbind('change').change(function(){
        var params=new Array();        
        params.push({name: 'shipadr', value: $(this).data('shipadr')});
        params.push({name: 'fldname', value: 'tax_reason'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_shipadrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input.shippingcost").val(response.data.shipping);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                $("#loader").hide();
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                $("#loader").hide();                
                show_error(response);
            }
        },'json');
    });    
    $(".taxexceptdoc.active").unbind('click').click(function(){
        var shipaddr=$(this).data('shipadr');
        var url="/leadorder/taxexcptdoc";
        var params=new Array();
        params.push({name :'shipadr', value: shipaddr});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});        
        $.post(url,params,function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','350px');
                $("#artNextModal").find('.modal-title').empty().html('Tax Exception Doc');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                init_taxdocupload(shipaddr);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.ship_tax_tabs2.active").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'edit', value: 1});
        params.push({name: 'manage', value: 1});
        var url="/leadorder/multishipview";
        $.post(url,params , function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','625px');
                $("#artNextModal").find('.modal-title').empty().html('Shipping Address');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("div.manageship.save").hide();
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);                
            }
        },'json');
    });
    $("div.viewmultishipdetails").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        params.push({name: 'edit', value: 1});
        params.push({name: 'manage', value: 1});
        var url="/leadorder/multishipview";
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','625px');
                $("#artNextModal").find('.modal-title').empty().html('Shipping Address');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);                
            }
        },'json');        
    });
}

function edit_multishipaddress() {
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name: 'edit', value: 1});
    params.push({name: 'manage', value: 1});
    var url="/leadorder/multishipview";
    $.post(url,params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
            init_multiaddress_ship();
        } else {
            show_error(response);                
        }
    },'json');    
}

// Multiship View
function init_multiaddress_ship() {
    $("#artNextModal").find('button.close').unbind('click').click(function () {
        $("#artNextModal").find('div.modal-body').empty();
        $("#artNextModal").modal('hide');
        init_onlineleadorder_edit();
    });
    $("input.eventdatevalue").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("input.eventdatevalue").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'shipping'});
        params.push({name: 'fldname', value: 'event_date'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});        
        var url="/leadorder/change_multishiporder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {                
                $("div#multishiptotals").empty().html(response.data.total_view);
                // Save button
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.shiprashselect").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'shipping'});
        params.push({name: 'fldname', value: 'rush_idx'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multishiporder_item";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {  
                $("div.multishipadressarea").empty().html(response.data.shipcontent);
                $("div#multishiptotals").empty().html(response.data.total_view);
                $("input.shiprushcost").val(response.data.rush_price);
                $("#loader").hide();
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                init_multiaddress_ship();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    }); 
    // Add new Address
    $("div.multishipadressadd").unbind('click').click(function(){
        var params=new Array();
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});        
        var url="/leadorder/multiship_addaddress";
        $("#loader").show();
        $.post(url,params,function(response){
            if (response.errors=='') {
                $("div.multishipadressarea").empty().html(response.data.shipcontent);
                $("div#multishiptotals").empty().html(response.data.total_view);
                $("div.numaddress").empty().html(response.data.numaddress)
                show_multishipsave(response);
                $("#loader").hide();
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                init_multiaddress_ship();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
        
    });
    // Change Number of QTY
    $("input.shipaddrinput").unbind('change').change(function(){
        var shipadr=$(this).data('shipadr');
        var params=new Array();
        params.push({name: 'shipadr', value: shipadr});
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()})
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {  
                $("#loader").hide();                
                $("div#multishiptotals").empty().html(response.data.total_view);
                $("input.shippingcost[data-shipadr='"+shipadr+"']").val(response.data.shiprate);
                $("input.salestaxcost[data-shipadr='"+shipadr+"']").val(response.data.sales_tax);
                if (parseInt(response.data.is_calc)===1) {
                    $("div.multishippopuparea").find("div.ship_tax_container2[data-shipadr='"+shipadr+"']").empty().html(response.data.cost_view);
                }
                if (parseInt(response.data.taxdata)===1) {
                    $("div.multishippopuparea").find("div.ship_tax_cont_bl3[data-shipadr='"+shipadr+"']").empty().html(response.data.taxview);
                }   
                $("input.ship_tax_input1[data-shipadr='"+shipadr+"']").val(response.data.city);
                $("select.ship_tax_select2[data-shipadr='"+shipadr+"']").val(response.data.state_id);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
                init_multiaddress_ship();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');        
    });
    // Blind, Resident
    $("input.shipadrchk").unbind('change').change(function(){
        var newval=1;
        if ($(this).prop('checked')==false) {
            newval=1;
        }
        var fldname=$(this).data('fldname');
        var shipadr=$(this).data('shipadr');
        var params=new Array();
        params.push({name: 'shipadr', value: shipadr});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: newval});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (fldname=='resident') {                                        
                    if (newval==1) {
                        $("div.residentlabel[data-shipadr='"+shipadr+"']").removeClass('shipblind');
                    } else {
                        $("div.residentlabel[data-shipadr='"+shipadr+"']").addClass('shipblind');
                    }
                }
                if (fldname=='ship_blind') {                    
                    if (newval==1) {
                        $("div.shblindlabel[data-shipadr='"+shipadr+"']").removeClass('shipblind');
                    } else {
                        $("div.shblindlabel[data-shipadr='"+shipadr+"']").addClass('shipblind');
                    }                    
                }
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');        
        
    })
    // Change Radio button
    $("input.ship_tax_radio").unbind('change').change(function(){
        var params=new Array();        
        var shipadr=$(this).data('shipadr');
        params.push({name: 'shipadr', value: shipadr});        
        params.push({name: 'fldname', value: 'shipping_method'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // shipping_cost_id
                // shiprate                
                $("div.multishippopuparea").find("div.ship_tax_cont2_line[data-shipadr='"+shipadr+"']").addClass('opast');
                $("div.multishippopuparea").find("div.shiprateshowarea[data-shipadr='"+shipadr+"']").removeClass('active');
                $("div.multishippopuparea").find("div.ship_tax_cont2_line[data-shipadr='"+shipadr+"'][data-shipcost='"+response.data.shipping_cost_id+"']").removeClass('opast');
                $("div.multishippopuparea").find("div.shiprateshowarea[data-shipadr='"+shipadr+"'][data-shipcost='"+response.data.shipping_cost_id+"']").addClass('active');
                $("input.shippingcost[data-shipadr='"+shipadr+"']").val(response.data.shiprate);                
                $("input.salestaxcost[data-shipadr='"+shipadr+"']").val(response.data.sales_tax);                
                $("div#multishiptotals").empty().html(response.data.total_view);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');        
    });  
    // Country
    $("select.shipcountryselect").unbind('change').change(function(){
        var params=new Array();        
        var shipadr=$(this).data('shipadr');
        params.push({name: 'shipadr', value: shipadr});        
        params.push({name: 'fldname', value: 'country_id'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {                
                $("div[data-content='shipstateshow'][data-shipadr='"+shipadr+"']").empty().html(response.data.stateview);
                $("div#multishiptotals").empty().html(response.data.total_view);
                $("input.shippingcost[data-shipadr='"+shipadr+"']").val(response.data.shiprate);                
                $("input.salestaxcost[data-shipadr='"+shipadr+"']").val(response.data.sales_tax);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Address
    $("textarea.ship_tax_textarea").unbind('change').change(function(response){
        var params=new Array();        
        var shipadr=$(this).data('shipadr');
        params.push({name: 'shipadr', value: shipadr});        
        params.push({name: 'fldname', value: 'address'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {                
                $("input.shippingcost[data-shipadr='"+shipadr+"']").val(response.data.shiprate);                
                $("input.salestaxcost[data-shipadr='"+shipadr+"']").val(response.data.sales_tax);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');        
    });
    $("input.ship_tax_textareainpt").unbind('change').change(function(response){
        var params=new Array();        
        var shipadr=$(this).data('shipadr');
        params.push({name: 'shipadr', value: shipadr});        
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {                
                $("input.shippingcost[data-shipadr='"+shipadr+"']").val(response.data.shiprate);                
                $("input.salestaxcost[data-shipadr='"+shipadr+"']").val(response.data.sales_tax);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();  
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');        
    });
    
    // State
    $("select.ship_tax_select2").unbind('change').change(function(){
        var shipadr=$(this).data('shipadr');
        var params=new Array();                
        params.push({name: 'shipadr', value: shipadr });
        params.push({name: 'fldname', value: 'state_id'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {                
                $(".ship_tax_cont_bl3[data-shipadr='"+shipadr+"']").empty().html(response.data.taxview);                
                $("input.shippingcost[data-shipadr='"+shipadr+"']").val(response.data.shiprate);                
                $("input.salestaxcost[data-shipadr='"+shipadr+"']").val(response.data.sales_tax);
                $("div#multishiptotals").empty().html(response.data.total_view);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');
    });  
    $("input.excepttax").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var shipaddr=$(this).data('shipadr');
        var params=new Array();        
        params.push({name: 'shipadr', value: shipaddr});
        params.push({name: 'fldname', value: 'tax_exempt'});
        params.push({name: 'newval', value: newval});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});
        var url="/leadorder/change_multiship_adrress";
        $.post(url, params, function(response){
            if (response.errors=='') {                
                if (newval==1) {
                    $("select.taxexcept_select[data-shipadr='"+shipaddr+"']").prop('disabled',false);
                    $("div.taxexceptdoc[data-shipadr='"+shipaddr+"']").removeClass('nonactive').addClass('active');
                } else {
                    $("select.taxexcept_select[data-shipadr='"+shipaddr+"']").prop('disabled',true);
                    $("div.taxexceptdoc[data-shipadr='"+shipaddr+"']").removeClass('active').addClass('nonactive');
                }
                $("input.shippingcost[data-shipadr='"+shipaddr+"']").val(response.data.shiprate);                
                $("input.salestaxcost[data-shipadr='"+shipaddr+"']").val(response.data.sales_tax);                                
                $("div#multishiptotals").empty().html(response.data.total_view);
                show_multishipsave(response);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                init_multiaddress_ship();
            } else {
                show_error(response);
            }
        },'json');        
    });
    
    $("div.taxexceptdoc.active");
    // Delete Shipping Address
    $("div.shipadrtrash").find('i').unbind('click').click(function(response){
        if (confirm('Delete Shipping Address?')==true) {
            var params=new Array();
            params.push({name: 'shipadr', value:$(this).data('shipadr')});
            params.push({name:'ordersession', value: $("input#ordersession").val()});
            params.push({name:'shipsession', value: $("input#shipsession").val()});
            var url="/leadorder/remove_multiship_address";
            $.post(url, params, function(response){
                if (response.errors=='') {                    
                    $("div.multishipadressarea").empty().html(response.data.shipcontent);
                    $("div#multishiptotals").empty().html(response.data.total_view);
                    $("div.numaddress").empty().html(response.data.numaddress);
                    show_multishipsave(response);
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();
                    init_multiaddress_ship();
                }                
            },'json');
        }
    });    
    
    // Save Shipping 
    $("div.manageship.save").unbind('click').click(function(){
        var url="/leadorder/multiship_save";
        var params=new Array();
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shipsession', value: $("input#shipsession").val()});        
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("#artNextModal").modal('hide');
                // Show new content
                $("div.bl_ship_tax_content").empty().html(response.data.content);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                $("input.salestaxcost").val(response.data.tax);
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                // Shippings Dates 
                $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');
    });
}

// Show / hide save button
function show_multishipsave(response) {
    if (parseInt(response.data.save_view) === 1) {
        $("div.manageship.save").show();
    } else {
        $("div.manageship.save").hide();
    }
}

function init_taxdocupload(shipadr) {
    var temp= '<div class="qq-uploader"><div class="custom_upload qq-upload-button"><span style="clear: both; float: left; padding-left: 10px; padding-top: 8px;">'+
      '<em>Upload</em></span></div>' +
      '<ul class="qq-upload-list"></ul>' +
      '<ul class="qq-upload-drop-area"></ul>'+
      '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: ['pdf', 'eps','doc', 'docx'],
        action: '/utils/redrawattach',
        /* template: temp, */
        multiple: false,
        debug: false,
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success) {
                var url="/leadorder/taxexcptdocsave";
                $("ul.qq-upload-list").css('display','none');
                var params = new Array();
                params.push({name: 'shipadr', value: shipadr});
                params.push({name: 'newdoc', value: responseJSON.filename});
                params.push({name: 'srcname', value: responseJSON.source});
                params.push({name: 'ordersession', value: $("input#ordersession").val()});
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $("#artNextModal").modal('hide');
                    } else {
                        alert(response.errors);
                        if(response.data.url !== undefined) {
                            window.location.href=response.data.url;
                        }
                    }
                }, 'json');
            }
        }
    });    
}

function save_taxdoc(shipaddr, newdoc, srcname) {
    var params=new Array();
    params.push({name: 'shipadr', value: shipaddr});
    params.push({name: 'newdoc', value: newdoc});
    params.push({name: 'srcname', value: srcname});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/taxexcptdocsave";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function init_leadorder_billing() {
    $("input.billinginput").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'billing'});
        params.push({name: 'fldname', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') { 
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');        
    });
    // Change country
    $("select.billing_select2").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'billing'});
        params.push({name: 'fldname', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {  
                $("div#billingstateselectarea").empty().html(response.data.stateview);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');        
    });
    // Change state
    $("select.billing_select1").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'billing'});
        params.push({name: 'fldname', value: 'state_id'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');        
    });
}

function init_leadorder_charges() {
    $("input.creditappduedate").datepicker({
        autoclose: true
    });
    $("input.chargeinput").unbind('change').change(function(response){
        var fldname=$(this).data('field');
        var chargeid=$(this).data('charge');
        var params=new Array();        
        params.push({name:'charge', value: chargeid});
        params.push({name:'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_charges";
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (fldname=='cardnum') {                    
                    $("input.pay_method_input2[data-charge='"+chargeid+"']").val(response.data.cardnum);
                }
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
                if (typeof response.data.oldval !== 'undefined') {
                    $("input.chargeinput[data-charge='"+response.mdata.charge+"'][data-field='"+response.data.fldname+"']").val(response.data.oldval);
                }
            }
        },'json');
    });
    // Change AutoPay
    $("input.autopaycharge").unbind('change').change(function(){
        var params=new Array();
        params.push({name:'charge', value: $(this).data('charge')});
        params.push({name:'fldname', value: 'autopay'});
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        } 
        params.push({name: 'newval', value: newval});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/change_leadorder_charges";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');        
    });  
    $("div.addcreditcard").unbind('click').click(function(){        
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/add_leadorder_charge";
        $.post(url,params, function(response){
            $(".pay_methods_area").empty().html(response.data.content);
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        },'json');
        
    });
    $("input.balancemanage_radio").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'balance_manage'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#crediapporderarea").empty().html(response.data.creditview);
                $("input#loctimeout").val(response.data.loctime);
                init_leadorder_charges();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.creditappduedate").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'credit_appdue'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');        
    });
    $("select.creditappselectterm").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value:'order'});
        params.push({name: 'fldname', value: 'balance_term'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/change_leadorder_item";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.uploadappfile").unbind('click').click(function(){        
        var url="/leadorder/creditappdoc";
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});        
        $.post(url,params,function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','350px');
                $("#artNextModal").find('.modal-title').empty().html('Credit App Doc');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                init_taxdocupload();
                $("div.artlogouploadsave_data").unbind('click').click(function(){
                    var newdoc=$("input#filename").val();
                    var srcname=$("input#sourcename").val();
                    save_creditappdoc(newdoc, srcname);
                });
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');        
    });
    $("div.pay_method_buttonsend").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'order_payment_id', value: $(this).data('charge')});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});    
        var url="/leadorder/leadorder_paycharge";
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.payments_table.payments_table_text").empty().html(response.data.content);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
                $("#loader").hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })
}

function save_creditappdoc(newdoc, srcname) {
    var params=new Array();    
    params.push({name: 'newdoc', value: newdoc});
    params.push({name: 'srcname', value: srcname});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/creditappdocsave";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").hide();
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');    
}

function init_orderbottom_content(edit_mode) {
    $("div.ticketdataviewarea").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/order_ticket";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','975px');
                $("#artNextModal").find('.modal-title').empty().html('Tickets');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                });
                $("form#tickededitform").find("input#order_num").prop('readonly','readonly');
                // $("form#tickededitform").find("select#type").prop('disabled',true);
                $("a.saveticketdat").unbind('click').click(function(){
                    save_orderticket();
                });
                init_ticketupload();
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }            
        },'json');
    });    
    $("div.shippingdataviewarea").unbind('click').click(function(){
        var url="/leadorder/shiptracks_show";
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','925px');
                $("#artNextModal").find('.modal-title').empty().html('Shipping Track Codes');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                init_orderstatus_change(edit_mode);
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');
    });
    // Profit
    $("div.profitdetailsviewarea").qtip({
        content : {
            text: function(event, api) {
                $.ajax({
                    // url: href // Use href attribute as URL
                    // url: api.elements.target.data('viewsrc') // Use href attribute as URL
                    url: $(this).data('viewsrc')
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                    init_profitedit_call();
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
            classes: 'qtip-dark profitdetails_tooltip'
        },
        // show: {
            // effect: function() { $(this).fadeIn(250); }
        // },
        show: 'click',
        // hide: {
        //    delay: 200,
        //     fixed: true, // <--- add this
        //    effect: function() { $(this).fadeOut(250); }
        // },
        hide: 'unfocus'
    });

}

function init_profitedit_call() {
    console.log('Content Loaded');
}

function save_orderticket() {
    $("form#tickededitform").find("select#type").prop('disabled',false);
    var dat=$("form#tickededitform").serializeArray();
    dat.push({name: 'ordersession', value: $("input#ordersession").val()});
    var url="/leadorder/save_orderticket";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $(".ticketdataviewarea").empty().html(response.data.ticket_content);
            $("input#loctimeout").val(response.data.loctime);            
            init_onlineleadorder_edit();
        } else {
            show_error(response);
            $("form#tickededitform").find("select#type").prop('disabled',true);
        }
    }, 'json');    
}

// Ship Track codes
function init_orderstatus_change(edit_mode) {    
    // Add new package
    $("input.trackcodeinpt").keypress(function(event){
        var addres=$(this).data('shipaddr');        
        var newval=$(this).val();
        var package=$(this).data('shippack');
        if (newval!='') {
            $("div.trackcodeupdate[data-shippack='"+package+"'][data-shipaddr='"+addres+"']").addClass('active');
        } else {
            $("div.trackcodeupdate[data-shippack='"+package+"'][data-shipaddr='"+addres+"']").removeClass('active');
        }
    });
    // 
    $(".newshippack").unbind('click').click(function(){
        var shipadr=$(this).parent('div.shiptrackpackrow').data('shipaddr');
        var url="/leadorder/shippackage_add";
        var params=new Array();
        params.push({name:'shipaddr', value: shipadr});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shiptraccodes', value: $("input#tracksession").val()});
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("div.shiptrackadrpacks").empty().html(response.data.shipaddr_content);
                init_orderstatus_change(edit_mode);
                if (edit_mode==1) {
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();                
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Update code
    $("div.trackcodeupdate").unbind('click').click(function(){
        var addres=$(this).data('shipaddr');
        var field='track_code';
        var package=$(this).data('shippack');
        var newval=$("input.trackcodeinpt[data-shipaddr='"+addres+"'][data-shippack='"+package+"']").val();
        shiptrack_change(addres, package, field, newval, edit_mode);
    });
    $("select.deliveryservicelist").unbind('change').change(function(){
        var addres=$(this).data('shipaddr');
        var field='deliver_service';
        var newval=$(this).val();
        var package=$(this).data('shippack');
        shiptrack_change(addres, package, field, newval, edit_mode);
    });
    $("input.trackcodeinpt").unbind('change').change(function(){
        var addres=$(this).data('shipaddr');
        var field='track_code';
        var newval=$(this).val();
        var package=$(this).data('shippack');
        shiptrack_change(addres, package, field, newval, edit_mode);        
    });
    // Remove Package
    $("div.trackcoderemove").unbind('click').click(function(){
        if (confirm('Remove Track Code?')==true) {
            var address=$(this).data('shipaddr');
            var params=new Array();        
            params.push({name: 'shipaddres', value: address});
            params.push({name: 'package_id', value: $(this).data('shippack')});
            params.push({name:'ordersession', value: $("input#ordersession").val()});
            params.push({name:'shiptraccodes', value: $("input#tracksession").val()});
            var url="/leadorder/shiptrackpackage_remove";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("div.shiptrackadrpacks[data-shipaddr='"+address+"']").empty().html(response.data.shipaddr_content);
                    if (response.data.showalltrack==1) {
                        $("div.trackallbtn").show();                
                    } else {
                        $("div.trackallbtn").hide();
                    }
                    init_orderstatus_change(edit_mode);
                    if (edit_mode==1) {
                        $("input#loctimeout").val(response.data.loctime);
                        init_onlineleadorder_edit();                
                    }
                } else {
                    show_error(response);
                }
            },'json');            
        }
    });       
    // Track Code
    $("div.trackcodemanage").unbind('click').click(function(){
        var addres=$(this).data('shipaddr');
        var package=$(this).data('shippack');
        var params=new Array();
        params.push({name: 'shipaddres', value: addres});
        params.push({name: 'package_id', value: package});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shiptraccodes', value: $("input#tracksession").val()});
        var url="/leadorder/shiptrackpackage_tracking";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // Change Package View
                $("div.shiptrackpackrow[data-shipaddr='"+addres+"'][data-shippack='"+package+"']").empty().html(response.data.packageview);
                init_orderstatus_change(edit_mode);
                $.colorbox({html:response.data.content});
                if (edit_mode==1) {
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();
                }
            } else {
                show_error(response);
            }
        },'json');
    })

    // Show / Hide Send form
    $("input.senttrackcode").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var addres=$(this).data('shipaddr');
        var package=$(this).data('shippack');
        check_sendtrack(addres, package, newval);
    });
    // Email Fields
    $("input.trackemailinpt").unbind('change').change(function(){
        var fldname=$(this).data('field');
        var newval=$(this).val();
        shiptrack_message_change(fldname, newval);
    });
    $("input.trackemailsubj").unbind('change').change(function(){
        var fldname='subject';
        var newval=$(this).val();
        shiptrack_message_change(fldname, newval);        
    });
    $("textarea.trackemailto").unbind('change').change(function(){
        var fldname='customer';
        var newval=$(this).val();
        shiptrack_message_change(fldname, newval);
    });
    $("textarea.trackemailmessage").unbind('change').change(function(){
        var fldname='message';
        var newval=$(this).val();
        shiptrack_message_change(fldname, newval);        
    });
    $("div.showtrackmailbcc").unbind('click').click(function(){
        $("#trackshowbccarea").empty().html('<div class="label">From:</div><div class="value"><input type="text" class="trackemailinpt" data-field="bcc" value=""/></div>');
        init_orderstatus_change(edit_mode);
    });
    $("div.sendtraccodemessage").unbind('click').click(function(){
        if (confirm('Send Track codes?')==true) {
            var params=new Array();
            params.push({name: 'edit_mode', value: edit_mode});
            params.push({name:'ordersession', value: $("input#ordersession").val()});
            params.push({name:'shiptraccodes', value: $("input#tracksession").val()});            
            var url="/leadorder/shiptrackmessage_send";            
            $.post(url,params, function(response){
                if (response.errors=='') {
                    $("#artNextModal").modal('hide');
                    $(".shippingdataviewarea").empty().html(response.data.shipstatus);
                    if (edit_mode==1) {
                        $("input#loctimeout").val(response.data.loctime);
                        init_onlineleadorder_edit();
                    }
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $("div.orderstatussave").unbind('click').click(function(){        
        var url="/leadorder/shiptrack_save";
        var params=new Array();
        params.push({name: 'edit_mode', value: edit_mode});
        params.push({name:'ordersession', value: $("input#ordersession").val()});
        params.push({name:'shiptraccodes', value: $("input#tracksession").val()});            
        $.post(url,params,function(response){
            if (response.errors=='') {
                $("#artNextModal").modal('hide');
                $("#loader").hide();
                $(".shippingdataviewarea").empty().html(response.data.shipstatus);
                if (edit_mode==1) {
                    $("input#loctimeout").val(response.data.loctime);
                    init_onlineleadorder_edit();
                }
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });    
}

function shiptrack_change(addres, package, field, newval, edit_mode) {
    var params=new Array();
    params.push({name: 'shipaddres', value: addres});
    params.push({name: 'package_id', value: package});
    params.push({name: 'field', value: field});
    params.push({name: 'newval', value: newval});
    params.push({name:'ordersession', value: $("input#ordersession").val()});
    params.push({name:'shiptraccodes', value: $("input#tracksession").val()});    
    var url="/leadorder/shiptrack_change";
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (response.data.shownewrow==1) {
                $("div.shiptrackpackrow[data-shipaddr='"+addres+"'][data-shippack='"+package+"']").empty().html(response.data.packageview);
            }
            if (response.data.viewtrack==1) {
                $("div.trackcodemanage[data-shipaddr='"+addres+"'][data-shippack='"+package+"']").css('visibility','visible');
            } else {
                $("div.trackcodemanage[data-shipaddr='"+addres+"'][data-shippack='"+package+"']").css('visibility','hidden');
            }
            if (response.data.showalltrack==1) {
                $("div.trackallbtn").show();                
            } else {
                $("div.trackallbtn").hide();
            }
            init_orderstatus_change(edit_mode);
            if (edit_mode==1) {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            }
        } else {
            show_error(response);
        }
    },'json');
}

function check_sendtrack(addres, package, newval, edit_mode) {
    var params=new Array();    
    var numchk=0;
    $("div.shiptrackaddresarea").find('input.senttrackcode').each(function(){
    if ($(this).prop('checked')==true) {
            numchk+=1;
        }
    });    
    params.push({name: 'shipaddres', value: addres});
    params.push({name: 'package_id', value: package});
    params.push({name: 'field', value: 'senddata'});
    params.push({name: 'newval', value: newval});
    params.push({name:'ordersession', value: $("input#ordersession").val()});
    params.push({name:'shiptraccodes', value: $("input#tracksession").val()});        
    var url="/leadorder/shiptrack_change";
    $.post(url, params, function(response){
        if (response.errors=='') {            
            if (newval==0) {
                if (numchk==0) {
                    $("div.shiptracksendarea").empty();
                } 
            } else {
                if ($("div.sendtraccodemessage").length==0) {
                    $("div.shiptracksendarea").empty().html(response.data.email_view);                    
                }                
            }
            init_orderstatus_change(edit_mode);
            if (edit_mode==1) {
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            }            
        } else {
            show_error(response);
        }
    },'json');
}

// Change parameters of value
function shiptrack_message_change(fldname, newval) {
    var url="/leadorder/shiptrackmessage_change";
    var params=new Array();
    params.push({name:'field', value: fldname});
    params.push({name: 'newval', value: newval});
    params.push({name:'ordersession', value: $("input#ordersession").val()});
    params.push({name:'shiptraccodes', value: $("input#tracksession").val()});
    $.post(url, params,function(response){
        if (response.errors=='') {
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            show_error(response);
        }
    },'json');
}

// New Manualy Payment
function init_newpayment() {
    $("input.paydatadetails.paydate").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("div.paymentdatasave").unbind('click').click(function(){
        var params=$("form#paymentdataform").serializeArray();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/payment_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").modal('hide');
                $("div.payments_table.payments_table_text").empty().html(response.data.content);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                if (response.data.ordersystem=='new') {
                    openbalancemanage(response.data.balanceopen);
                }                
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    })
    $("input.paydatadetails").unbind('change').change(function(response){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/payment_edit";
        $.post(url, params, function(response){
            if (response.errors=='') {         
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.paydatadetails").unbind('change').change(function(response){
        var params=new Array();
        params.push({name: 'fldname', value: 'paytype'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        var url="/leadorder/payment_edit";
        $.post(url, params, function(response){
            if (response.errors=='') {   
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();
            } else {
                show_error(response);
            }
        },'json');
    });    
}

// Change Order Template
function change_order_template() {
    if (confirm('Change Order Template ?')) {
        var url="/orders/order_system";
        var params=new Array();
        params.push({name: 'ordersession', value: $("input#ordersession").val()});
        $.post(url,params, function(response){
            if (response.errors=='') {
                // $("#pop_content").empty().html(response.data.content);
                $("#artModal").find('div.modal-body').empty().html(response.data.content);
                clearTimeout(timerId);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
                $("input#orderssystemcheck").prop('checked',false);
            }
        },'json');
    } else {
        $("input#orderssystemcheck").prop('checked',false);
    }
}

function place_neworder() {
    var url="/leadorder/leadorder_place";
    $("#loader").show();
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            save_leadorderdata();
        } else {
            $("#loader").hide();
            $("#artNextModal").find('div.modal-dialog').css('width','475px');
            $("#artNextModal").find('.modal-title').empty().html('Place Order');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            // Init choice
            $("div.leavesamepayment").unbind('click').click(function(){
                $("#artNextModal").modal('hide');
                save_leadorderdata();
            });
            $("div.applynewpayment").unbind('click').click(function(){
                $("#artNextModal").modal('hide');
                change_paymenttotal();
            });    
        }
    },'json');
}

function change_paymenttotal() {
    var url="/leadorder/leadorder_change_paymenttotal";
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            save_leadorderdata();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function save_leadorderdata() {
    var url="/leadorder/leadorder_save";
    var callpage=$("input#callpage").val();    
    $("#loader").show();
    // Get Lock ID 
    var params=new Array();
    if ($("input#locrecid").length>0) {
        // Clean locked record
        params.push({name: 'locrecid', value: $("input#locrecid").val()});        
    }
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    params.push({name: 'callpage', value: callpage});
    params.push({name: 'brand', value: $("#root_brand").val()});
    $.post(url,params,function(response){
        if (response.errors=='') {       
            clearTimeout(timerId);
            // Current page
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#loader").hide();
            if (parseInt(response.data.newplaceorder)==1) {
                $("#flash").css('width','975');
                if (parseInt(response.data.finerror)==0) {
                    $("#flash").css('background-color','#2b34d1');
                } else {
                    $("#flash").css('background-color','#ff0000');
                }
                $.flash(response.data.popupmsg,7000);
            }
            navigation_init();
            /*
            $("#artModal").modal('hide');
            if (callpage=='finance') {
                disablePopup('leadorderdetailspopup');           
                $("#pop_content").empty();
                init_profit_orders();
            } else if (callpage=='paymonitor') {
                disablePopup('leadorderdetailspopup');           
                $("#pop_content").empty();
                init_paymonitor();
            } else if (callpage=='art_tasks') {
                disablePopup('leadorderdetailspopup');           
                $("#pop_content").empty();
                init_tasks_page();
            } else if (callpage=='art_order') {
                disablePopup('leadorderdetailspopup');           
                $("#pop_content").empty();
                initGeneralPagination();
            } else if (callpage=='inventory') {
                disablePopup('leadorderdetailspopup');           
                $("#pop_content").empty();
                invetory_exitorder(response.data.color);                
            } else {
                $("#pop_content").empty().html(response.data.content);
                $("#loader").hide();            
                if(typeof response.data.popupmsg !== 'undefined') {
                    $("#flash").css('width','975');
                    $.flash(response.data.popupmsg,5000);
                }
                navigation_init();                
            }
            */
        } else {
            $("#loader").hide();
            show_error(response);            
        }
    },'json');
}

function prepare_send_invoice() {
    var url="/leadorder/leadorderinv_prepare";
    var params=new Array();
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','400px');
            $("#artNextModal").find('.modal-title').empty().html('Send Email Message');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("div.addbccapprove").click(function(){
                var bcctype=$(this).data('applybcc');
                if (bcctype=='hidden') {
                    $(this).data('applybcc','show').empty().html('hide bcc');
                    $("div#emailbccdata").show();
                    $("textarea.aprovemail_message").css('height','222');
                } else {
                    $(this).data('applybcc','hidden').empty().html('add bcc');
                    $("div#emailbccdata").hide();
                    $("textarea.aprovemail_message").css('height','241');
                }
            });  
            $("div.approvemail_send").unbind('click').click(function(){
                send_invoicemail()
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
            $("#artNextModal").modal('hide');
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function create_editorder_timer() {
    if (parseInt($("input#orderdataid").val())>0) {
        clearTimeout(timerId);        
        timerId = setTimeout('timershow()', 1000);        
    }
}
function timershow() {
    if ($("input#loctimeout").length>0) {
        var today = new Date();
        var timeend=$("input#loctimeout").val();
        today = Math.floor((timeend-today)/1000);        
        if (today<=0) {
            clearTimeout(timerId);
            $("#artNextModal").modal('hide');
            var order=$("input#orderdataid").val();
            var locrecid=$("input#locrecid").val();
            var url="/leadorder/leadorder_change";
            var params=new Array();            
            params.push({name: 'order', value: order});
            params.push({name: 'locrecid', value: locrecid});
            params.push({name: 'edit', value: 0});
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#artModalLabel").empty().html(response.data.header);
                    $("#artModal").find('div.modal-body').empty().html(response.data.content);
                    navigation_init();
                    alert('Your Edit Time has expired prior to saving.  Please re-do any work you meant to do.');
                } else {
                    show_error(response);
                }
            },'json');
        }
        if (today==15) {
            // Call colorbox
            var params = new Array();
            params.push({name: 'ordersession', value: $("input#ordersession").val()});
            var url='/leadorder/extend_edittime';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#artNextModal").modal('hide');
                    $("#artNextModal").find('div.modal-dialog').css('width','440px');
                    $("#artNextModal").find('.modal-title').empty().html('Extend Edit Time');
                    $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                    $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                    $("#artNextModal").on('hidden.bs.modal', function (e) {
                        $(document.body).addClass('modal-open');
                    })
                    init_extendtime();
                } else {
                    show_error(response);
                }
            },'json')
        }
        var tsec=today%60; today=Math.floor(today/60); if(tsec<10)tsec='0'+tsec;
        var tmin=today%60; today=Math.floor(today/60); if(tmin<10)tmin='0'+tmin;    
        var timestr=tmin+":"+tsec;
        $("div.timeroutarea").empty().html(timestr);
        clearTimeout(timerId);
        timerId = setTimeout('timershow()', 1000);        
    } else {
        clearTimeout(timerId);
    }
}

function init_extendtime() {
    $("div.extendtime_button").unbind('click').click(function(){
        var url="/leadorder/extendtime_order";
        $.post(url, {ordersession: $("input#ordersession").val()}, function(response){
            if (response.errors=='') {
                $("#artNextModal").modal('hide');
                $("input#loctimeout").val(response.data.loctime);
                init_onlineleadorder_edit();                
            } else {
                show_error(response);
            }
        },'json');
    })
}

function openbalancemanage(balanceopen) {    
    $("div.creditappview").removeClass('uploadappfile');
    if (parseInt(balanceopen)==1) {
        $("input.balancemanage_radio").prop("disabled",false);
        $("select.creditappselectterm").prop("disabled",false);
        $("input.creditappduedate").prop("disabled",false);
        $("div.creditappview").addClass('uploadappfile');        
    } else {
        $("input.balancemanage_radio").prop("disabled",true);
        $("select.creditappselectterm").prop("disabled",true);
        $("input.creditappduedate").prop("disabled",true);        
    }
}

function show_updatedetails(history) {
    var params=new Array();
    params.push({name: 'artwork_history_id', value: history});
    var url="/leadorder/show_update_details";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','742px');
            $("#artNextModal").find('.modal-title').empty().html('Update Details');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
        } else {
            show_error(response);
        }
    },'json');
}

function show_chargeattempts(order) {
    var params=new Array();
    params.push({name: 'order_id', value: order});
    var url='/leadorder/show_charge_attempts';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','856px');
            $("#artNextModal").find('.modal-title').empty().html('Charge Attempts');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
        } else {
            show_error(response);
        }
    },'json');
//    $.colorbox({
//        opacity: .7,
//        transition: 'fade',
//        ajax: true,
//        width:440,        
//        href: '/leadorder/show_charge_attempts',
//        data: params,
//        onComplete: function() {
//            $.colorbox.resize();
//        }
//    });    
    
//    var url="/leadorder/extendtime_order";
//    $.post(url, params, function(response){
//        if (response.errors=='') {
//            
//        } else {
//            show_error(response);
//        }
//    },'json');
    

}

function init_imagelogoupload() {
    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: ['jpg','gif', 'jpeg', 'pdf', 'ai', 'eps','doc', 'docx', 'png'],
        action: '/artproofrequest/art_redrawattach',
        multiple: false,
        debug: false,
        uploadButtonText:'',
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success) {
                var url="/artproofrequest/art_newartupload";
                $("ul.qq-upload-list").css('display','none');
                // $.post(url, {'filename':responseJSON.filename,'doc_name':fileName}, function(response){
                $.post(url, {'filename':responseJSON.uplsource, 'doc_name':fileName}, function(response){
                    if (response.errors=='') {
                        $("#orderattachlists").empty().html(response.data.content);
                        $(".qq-uploader").hide();
                        $("div.artlogouploadsave_data").show();
                        $("div.delvectofile").click(function(){
                            $("#orderattachlists").empty();
                            $(".qq-uploader").show();
                            $("div.artlogouploadsave_data").hide();
                        })
                    } else {
                        alert(response.errors);
                        if(response.data.url !== undefined) {
                            window.location.href=response.data.url;
                        }
                    }
                }, 'json');
            }
        }
    });
}

function init_creditappfunc() {
    initCreditAppPagination();
    $(".creditapp_popup").find("input.creditapptemplate").keypress(function(event){
        if (event.which == 13) {
            search_creaditappdata();
        }
    });
    $(".creditapp_popup").find("div.searchbtn").unbind('click').click(function(){
        search_creaditappdata();
    });
    $(".creditapp_popup").find("div.cleansearchbtn").unbind('click').click(function(){
        $("input.creditapptemplate").val('');
        $(".creditapp_popup").find("select.filterdataselect").val('');
        search_creaditappdata();
    });
    $(".creditapp_popup").find("select.filterdataselect").unbind('change').change(function(){
        search_creaditappdata();
    });
    $(".creditapp_popup").find("div.addnew").unbind('click').click(function(){
        add_creditapp();
    });

}

function initCreditAppPagination() {
    // count entries inside the hidden content
    var num_entries = $('#crapptotals').val();
    // var perpage = itemsperpage;
    var perpage = $("#crappperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".credapp_pagination").empty();
        pageCreditAppCallback(0);
    } else {
        var curpage = $("#crappcurrentpage").val();
        // Create content inside pagination element
        $(".proford_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageCreditAppCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageCreditAppCallback(page_index) {
    var search=$("input.creditapptemplate").val();
    var params=new Array();
    params.push({name:'search', value:search});
    params.push({name:'limit', value:$("#crappperpage").val()});
    params.push({name:'status', value:$(".creditapp_popup").find("select.filterdataselect").val()});
    params.push({name:'offset', value:page_index});
    var url='/creditapplication/creditappldata';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#crappcurrentpage").val(page_index);
            $('.creditappdataarea').empty().html(response.data.content);
            init_creditapp_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}


function search_creaditappdata() {
    var search=$("input.creditapptemplate").val();
    var params=new Array();
    params.push({name:'search', value:search});
    params.push({name:'status', value:$(".creditapp_popup").find("select.filterdataselect").val()});
    var url='/creditapplication/creditapplsearch';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $('#crapptotals').val(response.data.totals);
            $("#crappcurrentpage").val(0);
            initCreditAppPagination();
        } else {
            show_error(response);
        }
    },'json');
}

function init_creditapp_manage() {
    $(".creditapp_popup").find("div.edit.active").unbind('click').click(function(){
        var creditapp=$(this).data('appdat');
        var url="/creditapplication/creditappledit";
        $.post(url, {'creditapp': creditapp}, function(response){
            if (response.errors=='') {
                $('.creditappdataarea').animate({ scrollTop: 0 }, "slow");
                $("div.creditappdatarow[data-appdat='"+creditapp+"']").empty().html(response.data.content).show();
                $("div.creditappdatarow[data-appdat='"+creditapp+"']").find('input.customer').focus();
                $("div.creditapp_popup").find('div.cancelapp').unbind('click').click(function(){
                    restore_creditapp_content(creditapp);
                });
                init_creditapp_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Approve / Reject
    $(".creditapp_popup").find("div.status.pending").unbind('click').click(function(){
        var creditapp=$(this).data('appdat');
        prepare_approve_creditapp(creditapp);
    });

}

// Cancel edit
function restore_creditapp_content(creditapp) {
    var url="/creditapplication/creditapplcanceledit";
    $.post(url,{'creditapp': creditapp}, function(response){
        if (response.errors=='') {
            $("div.creditappdatarow[data-appdat='"+creditapp+"']").empty().html(response.data.content);
            init_creditapp_manage();
        } else {
            show_error(response);
        }
    },'json');
}

function add_creditapp() {
    var url="/creditapplication/creditappledit";
    $.post(url, {'creditapp': 0}, function(response){
        if (response.errors=='') {
            $('.creditappdataarea').animate({ scrollTop: 0 }, "slow");
            $("div.creditappdatarow.newapp").empty().html(response.data.content).show();
            $("div.creditappdatarow.newapp").find('input.customer').focus();
            $("div.creditapp_popup").find('div.cancelapp').unbind('click').click(function(){
                $("div.creditappdatarow.newapp").empty().hide();
            });
            init_creditapp_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function init_creditapp_edit() {
    $("div.creditapp_popup").find('div.saveapp').unbind('click').click(function(){
        var url="/creditapplication/creditapplsave";
        $.post(url,{}, function(response){
            if (response.errors=='') {
                initCreditAppPagination();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.creditappinpt").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/creditapplication/creditapplchange";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    })
}

function prepare_approve_creditapp(creditapp) {
    var url="/creditapplication/creditapplpreapprove";
    $.post(url,{'creditapp': creditapp}, function(response){
        if (response.errors=='') {
            // $.colorbox({html:response.data.content});
            // $.colorbox.resize();
            $("#pageModal").css('z-index','1100');
            $("#pageModal").find('div.modal-dialog').css('width','225px');
            $("#pageModal").find('.modal-title').empty().html('Approve Credit APP');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            init_creditappapproveedit(creditapp);
        } else {
            show_error(response);
        }
    },'json');
}

function init_creditappapproveedit(creditapp) {
    var params=new Array();
    var url="/creditapplication/creditapplapprove";
    params.push({name: 'creditapp', value: creditapp});
    $("div.approveappbutton").unbind('click').click(function(){
        params.push({name:'approve', value: 1});
        params.push({name:'reject', value: 0});
        params.push({name:'notes', value: $("textarea#review_notes").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                // $.colorbox.close();
                $("#pageModal").modal('hide');
                $("#pageModal").css('z-index','1050');
                initCreditAppPagination();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.rejectappbutton").unbind('click').click(function(){
        params.push({name:'approve', value: 0});
        params.push({name:'reject', value: 1});
        params.push({name:'notes', value: $("textarea#review_notes").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                // $.colorbox.close();
                $("#pageModal").modal('hide');
                $("#pageModal").css('z-index','1050');
                initCreditAppPagination();
            } else {
                show_error(response);
            }
        },'json');
    });
}
/* Ticket */
function init_ticketupload() {
    $("input#ticket_date").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("select#type").change(function(){
        change_custompart();
    })
    $("input#order_num").change(function(){
        change_custompart();
        // Update Customer
        update_ticket_customer();
    })
    $("input#customer").change(function(){
        change_custompart();
    })
    $("select#custom_issue_id").change(function(){
        change_custompart();
    })
    $("textarea#custom_description").change(function(){
        change_custompart();
    })
    $("textarea#custom_history").change(function(){
        change_custompart();
    })
    $("input#cost").change(function(){
        change_vendorpart();
    })
    $("select#vendor_id").change(function(){
        if ($("select#vendor_id").val()=='-') {
            $("select#vendor_id").val('-1');
            $("input#other_vendor").prop('readonly',false);
        } else if ($("select#vendor_id").val()=='-1') {
            $("input#other_vendor").prop('readonly',false);
        } else {
            $("input#other_vendor").prop('readonly',true);
        }
        change_vendorpart();
    })
    $("select#vendor_issue_id").change(function(){
        change_vendorpart();
    })
    $("textarea#vendor_description").change(function(){
        change_vendorpart();
    })
    $("textarea#vendor_history").change(function(){
        change_vendorpart();
    })
    /* Lock customer part */
    var custlock=$("#custom_closed").prop('checked');
    if (custlock==true) {
        custom_lock();
    }
    $("#custom_closed").change(function(){
        if ($("#custom_closed").prop('checked')==true) {
            custom_lock();
        } else {
            custom_unlock();
        }
    })
    $("#vendor_closed").change(function(){
        if ($("#vendor_closed").prop('checked')==true) {
            vendor_lock();
        } else {
            vendor_unlock();
        }
    })
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button"></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-tickuploader'),
        action: '/artproofrequest/proofattach',
        allowedExtensions: ['jpg', 'jpeg', 'pdf', 'ai', 'eps','doc', 'docx','JPG', 'JPEG', 'PDF', 'AI', 'EPS','DOC', 'DOCX'],
        uploadButtonText: '',
        action: '/utils/ticketattach',
        multiple: false,
        debug: false,
        template: upload_templ,
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                var url = "/tickets/saveattach";
                var ticket_id = $("#ticket_id").val();
                var session = $("#ticketattach").val();
                $("ul.qq-upload-list").css('display', 'none');
                $.post(url, {
                    'filename': responseJSON.filename,
                    'doc_name': fileName,
                    'ticket_id': ticket_id,
                    'session' : session,
                }, function (response) {
                    if (response.errors == '') {
                        $("#ticketattachlists").empty().html(response.data.content);
                        $("div.attachactions").click(function () {
                            delete_attach(this);
                        })
                    } else {
                        alert(response.errors);
                        if (response.data.url !== undefined) {
                            window.location.href = response.data.url;
                        }
                    }
                }, 'json');
            }
        }
    });
}

function change_custompart() {
    var order_num=$("input#order_num").val();
    var customer=$("input#customer").val();
    var customer_issue=$("select#custom_issue_id").val();
    var descrip=$("textarea#custom_description").val();
    var histor=$("textarea#custom_history").val();
    if (order_num=='' && customer=='' && customer_issue=='' && descrip=='' && histor=='') {
        $("div.ticket_custom_part").removeClass('colored');
    } else {
        $("div.ticket_custom_part").addClass('colored');
    }
}

function change_vendorpart() {
    var cost=$("input#cost").val();
    var vendor=$("select#vendor_id").val()
    var vend_iss=$("select#vendor_issue_id").val();
    var descr=$("textarea#vendor_description").val();
    var histor=$("textarea#vendor_history").val();
    if (cost=='' && vendor=='' && vend_iss=='' && descr=='' && histor=='') {
        $("div.ticket_vendr_part").removeClass('colored');
    } else {
        $("div.ticket_vendr_part").addClass('colored');
    }
}

function update_ticket_customer() {
    var order_num=$("input#order_num").val();
    var url="/tickets/ticket_ordercustomer";
    $.post(url, {'order_num': order_num}, function(response){
        if (response.errors=='') {
            $("input#customer").val(response.data.customer);
        }
    },'json');
}

function vendor_unlock() {
    $("input#cost").attr('readonly', false);
    $("select#vendor_id").attr('readonly', false);
    $("select#vendor_issue_id").attr('readonly', false);
    $("textarea#vendor_description").attr('readonly', false);
    $("textarea#vendor_history").attr('readonly', false);
}

function vendor_lock() {
    $("input#cost").attr('readonly', true);
    $("select#vendor_id").attr('readonly', true);
    $("select#vendor_issue_id").attr('readonly', true);
    $("textarea#vendor_description").attr('readonly', true);
    $("textarea#vendor_history").attr('readonly', true);
}

function custom_unlock() {
    $("input#order_num").attr('readonly', false);
    $("input#customer").attr('readonly', false);
    $("textarea#custom_description").attr('readonly', false);
    $("textarea#custom_history").attr('readonly', false);
    $("select#custom_issue_id").attr('readonly', false);
}

function custom_lock() {
    $("input#order_num").attr('readonly', true);
    $("input#customer").attr('readonly', true);
    $("textarea#custom_description").attr('readonly', true);
    $("textarea#custom_history").attr('readonly', true);
    $("select#custom_issue_id").attr('readonly', true);
}

