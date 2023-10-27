function addnewcustomquote() {
    var lead_num=$("div.lead_popup_number").text();
    var msg="You will now save the updates of the "+lead_num+" by creating the quote.  Ok?";
    if (confirm(msg)==true) {
        var url="/leadquote/lead_addquote";
        var dat=$("form#leadeditform").serializeArray();
        // var dat = new Array();
        dat.push({name:'lead_item_id', value: $("select#lead_item").val()});
        dat.push({name:'session_id', value: $("#session").val()});
        dat.push({name: 'session_attach', value: $("#session_attach").val()});
        dat.push({name: 'lead_type', value: $("#lead_type").val()});
        $("#loader").show();
        $.post(url, dat, function (response) {
            if (response.errors=='') {
                $("#quotepopupdetails").empty().html(response.data.quotecontent);
                $("#quotepopupdetails").show();
                $(".quotepopupclose").show();
                $(".leadquotenumberlist").unbind('click');
                $(".quotesaddnew").unbind('click');
                $("#lead_id").val(response.data.lead_id);
                init_leadquotes_content();
                if (parseInt($("#quotemapuse").val())==1) {
                    initShipQuoteAutocomplete();
                    initBillQuoteAutocomplete();
                }
                init_billingaddress_copy();
                $("#loader").hide();
                if (parseInt(response.data.newitem)!==0) {
                    $(".addprintdetails[data-quoteitem='"+response.data.newitem+"']").trigger('click');
                }
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    }
}

function init_leadquotes_content() {
    $(".quotepopupclose").unbind('click').click(function (){
        var quoteactive = $("#quoteleadnumber").val();
        $("#quotepopupdetails").empty();
        $("#quotepopupdetails").hide();
        $(".quotepopupclose").hide();
        $(".datarow[data-leadquote='"+quoteactive+"']").children('div').removeClass('active');
        $(".leadquotenumberlist").unbind('click').click(function(){
            var quote_id = $(this).data('leadquote');
            leadquote_edit(quote_id);
        })
        $(".quotesaddnew").unbind('click').click(function () {
            addnewcustomquote();
        });
    });
    $(".leadquotesavebtn").unbind('click').click(function (){
       // Save quote
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'lead', value: $("#quoteleadconnect").val()});
        var url = '/leadquote/quotesave';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                // Receive quote id
                $(".quotesdataarea").empty().html(response.data.quotescontent);
                var quote_id = response.data.quote_id;
                var docparams = new Array();
                docparams.push({name: 'quote_id', value: quote_id});
                docparams.push({name: 'edit_mode', value: 0});
                var url = '/leadquote/quoteedit';
                $.post(url, docparams, function(response){
                    if (response.errors=='') {
                        $("#quotepopupdetails").empty().html(response.data.quotecontent);
                        $("#quotepopupdetails").show();
                        $(".quotepopupclose").show();
                        $(".leadquotenumberlist").unbind('click');
                        $(".quotesaddnew").unbind('click');
                        $(".datarow[data-leadquote='"+quote_id+"']").children('div').addClass('active');
                        init_leadquotes_view();
                    } else {
                        show_error(response);
                    }
                },'json');
            } else {
                show_error(response);
            }
        },'json');
    });
    // Buttons
    $(".quoteactionpdfdoc.active").unbind('click').click(function (){
        // Save
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'lead', value: $("#quoteleadconnect").val()});
        var url = '/leadquote/quotesave';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".quotesdataarea").empty().html(response.data.quotescontent);
                // New session
                $("#quotesessionid").val(response.data.session_id);
                var quote = response.data.quote_id;
                var docparams = new Array();
                docparams.push({name: 'quote_id', value: quote});
                var url = '/leadquote/quotepdfdoc';
                $.post(url, docparams, function (response){
                    if (response.errors=='') {
                        var newWin = window.open(response.data.docurl,"Quoute PDF","width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
                    } else {
                        show_error(response);
                    }
                },'json')
            } else {
                show_error();
            }
        },'json');
    });
    $(".quoteactionsend.active").unbind('click').click(function (){
        // Save
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'lead', value: $("#quoteleadconnect").val()});
        var url = '/leadquote/quotesave';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".quotesdataarea").empty().html(response.data.quotescontent);
                $("#quotesessionid").val(response.data.session_id);
                var quote = response.data.quote_id;
                var docparams = new Array();
                docparams.push({ name: 'quote_id', value: quote });
                var url = '/leadquote/quotepreparesend';
                $.post(url, docparams, function (response){
                    $("#artNextModal").find('div.modal-dialog').css('width','455px');
                    $("#artNextModal").find('.modal-title').empty().html('Send PDF');
                    $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                    $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                    $("#artNextModal").on('hidden.bs.modal', function (e) {
                        $(document.body).addClass('modal-open');
                    })
                    $(".quoteemail_send").unbind('click').click(function(){
                        var quote = $(this).data('quote');
                        send_leadquote(quote);
                    });
                },'json');

            } else {
                show_error();
            }
        },'json');
    });
    $(".quoteactionaddorder.active").unbind('click').click(function () {
        // Save
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'lead', value: $("#quoteleadconnect").val()});
        var url = '/leadquote/quotesave';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".quotesdataarea").empty().html(response.data.quotescontent);
                $("#quotesessionid").val(response.data.session_id);
                var quote = response.data.quote_id;
                var docparams = new Array();
                docparams.push({ name: 'quote_id', value: quote });
                var url = '/leadquote/quoteaddorder';
                $("#loader").show();
                $.post(url, docparams, function (response) {
                    if (response.errors=='') {
                        var callpage = 'leads'
                        $("#loader").hide();
                        $("#leadformModal").modal('hide');
                        $("#artModalLabel").empty().html(response.data.header);
                        $("#artModal").find('div.modal-body').empty().html(response.data.content);
                        $("#artModal").find('div.modal-dialog').css('width','1004px');
                        $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
                        $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
                        init_onlineleadorder_edit();
                        init_rushpast();
                    } else {
                        $("#loader").hide();
                        show_error(response);
                    }
                },'json');
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".quoteactionduplicate.active").unbind('click').click(function () {
        if (confirm('Duplicate Quote ?')==true) {
            // Save
            var params = new Array();
            params.push({name: 'session', value: $("#quotesessionid").val()});
            params.push({name: 'lead', value: $("#quoteleadconnect").val()});
            var url = '/leadquote/quotesave';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".quotesdataarea").empty().html(response.data.quotescontent);
                    $("#quotesessionid").val(response.data.session_id);
                    var quote_id = response.data.quote_id;
                    var docparams = new Array();
                    docparams.push({name: 'quote_id', value: quote_id});
                    var url = '/leadquote/quoteduplicate';
                    $("#loader").show();
                    $.post(url, docparams, function (response){
                        if (response.errors=='') {
                            $("#quotepopupdetails").empty().html(response.data.quotecontent);
                            $("#quotepopupdetails").show();
                            $(".quotepopupclose").show();
                            $(".leadquotenumberlist").unbind('click');
                            $(".quotesaddnew").unbind('click');
                            $("#loader").hide();
                            init_leadquotes_content();
                        } else {
                            $("#loader").hide();
                            show_error(response);
                        }
                    },'json');
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Template
    $(".quotetemplateinpt").find('select').unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteparamchange';
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Common Input
    $(".quotecommondatainpt").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteparamchange';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.totalcalc)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                    $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Notes
    $(".quotenote").unbind('change').change(function (response){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteparamchange';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.totalcalc)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Address Input
    $(".quoteadrinpt").unbind('change').change(function(){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteaddresschange';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.shipcountry)==1) {
                    $("#shipquotecntcode").val(response.data.countrycode);
                    if (parseInt($("#quotemapuse").val())==1) {
                        $(".shipadrrlinearea").empty().html(response.data.address_view);
                        initShipQuoteAutocomplete();
                    }
                }
                if (parseInt(response.data.bilcountry)==1) {
                    $("#billcountrycode").val(response.data.countrycode);
                    if (parseInt($("#quotemapuse").val())==1) {
                        $(".billadrrlinearea").empty().html(response.data.address_view);
                        initBillQuoteAutocomplete();
                    }
                }
                if (parseInt(response.data.shipstate)==1) {
                    $(".quoteshipaddresdistrict").empty().html(response.data.stateview);
                }
                if (parseInt(response.data.billstate)==1) {
                    $(".quotebilladdresdistrict").empty().html(response.data.stateview);
                }
                if (parseInt(response.data.shiprebuild)==1) {
                    // Update zip, city state
                    $(".quoteadrinpt[data-item='shipping_zip']").val(response.data.shipping_zip);
                    $(".quoteadrinpt[data-item='shipping_city']").val(response.data.shipping_city);
                    $(".quoteadrinpt[data-item='shipping_state']").val(response.data.shipping_state);
                }
                if (parseInt(response.data.taxview)==1) {
                    $(".quotetaxarea").empty().html(response.data.taxcontent);
                }
                if (parseInt(response.data.billrebuild)==1) {
                    $(".quoteadrinpt[data-item='billing_zip']").val(response.data.billing_zip);
                    $(".quoteadrinpt[data-item='billing_city']").val(response.data.billing_city);
                    $(".quoteadrinpt[data-item='billing_state']").val(response.data.billing_state);
                }
                if (parseInt(response.data.calcship)==1) {
                    // Update shipping cost
                    $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                    $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                }
                if (parseInt(response.data.totalcalc)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                    $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                }
                $("#shipingcompileaddress").val(response.data.shipaddress);
                $("#billingcompileaddress").val(response.data.billaddress);
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Change rate
    $(".quoteratecheck.choice").unbind('click').click(function(){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'newval', value: $(this).data('shiprate')});
        var url = '/leadquote/quoteratechange';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Update shipping cost
                $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                $(".quotetotalvalue").empty().html(response.data.total);
                $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Tax except
    $(".quotetaxexceptcheck.choice").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        var url = '/leadquote/quotetaxextemp';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Update shipping cost
                $(".quotetaxexceptcheck").empty().html(response.data.content);
                $(".taxexceptreasoninpt").val(response.data.tax_reason);
                $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                $(".quotetotalvalue").empty().html(response.data.total);
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Change Lead Time
    $("select.quoteleadtimeselect").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteleadtimechange';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Update shipping cost
                $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                $(".quoteleadshipcostinpt[data-item='rush_cost']").val(response.data.rush_cost);
                $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                $(".quotetotalvalue").empty().html(response.data.total);
                $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })
    $("input.quouteitem_input").unbind('change').change(function(){
        var itemcolor = $(this).data('item');
        var item = $(this).data('quoteitem');
        var fld = $(this).data('field');
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'item', value: item });
        params.push({name: 'itemcolor', value: itemcolor});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteitemchange';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.refresh)==1) {
                    $(".quoteitemsarea[data-quoteitem='"+item+"']").empty().html(response.data.itemcontent);
                } else {
                    $(".quoteitemrowsubtotal[data-item='"+itemcolor+"'][data-quoteitem='"+item+"']").empty().html(response.data.itemcolor_subtotal);
                }
                if (parseInt(response.data.totals)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                    $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                }
                if (parseInt(response.data.shipping)==1) {
                    $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                    $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                }
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $("select.quouteitem_input").unbind('change').change(function(){
        var itemcolor = $(this).data('item');
        var item = $(this).data('quoteitem');
        var fld = $(this).data('field');
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'item', value: item });
        params.push({name: 'itemcolor', value: itemcolor});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteitemchange';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.refresh)==1) {
                    $(".quoteitemsarea[data-quoteitem='"+item+"']").empty().html(response.data.itemcontent);
                } else {
                    $(".quoteitemrowsubtotal[data-item='"+itemcolor+"'][data-quoteitem='"+item+"']").empty().html(response.data.itemcolor_subtotal);
                }
                if (parseInt(response.data.totals)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                    $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                }
                if (parseInt(response.data.refresh)==1) {
                    init_leadquotes_content();
                }
            } else {
                show_error(response);
            }
        },'json');
    });

    // Add color
    $(".itemcoloradd").unbind('click').click(function () {
        var item = $(this).data('quoteitem');
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'item', value: item });
        var url = '/leadquote/quoteitemaddcolor';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".quoteitemsarea[data-quoteitem='"+item+"']").empty().html(response.data.itemcontent);
                init_leadquotes_content();
            } else {
                show_error(response);
            }
        },'json');
    });

    // Print details
    $(".addprintdetails").unbind('click').click(function () {
        var item = $(this).data('quoteitem');
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'item', value: item });
        var url = '/leadquote/quoteitemprintdetails';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width','1077px');
                $("#artNextModal").find('.modal-title').empty().html('Quote Item Print Details');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                // Init Print details manage
                init_quote_printdetails();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Remove item
    $(".quoteitemremove").find('i').unbind('click').click(function (){
        var itemname = $(this).data('item');
        var item = $(this).data('quoteitem');

        if (confirm('Remove item '+itemname+'?')==true) {
            var params=new Array();
            params.push({name: 'session', value: $("#quotesessionid").val()});
            params.push({name: 'item', value: item });
            var url="/leadquote/removeitem";
            $("#loader").show();
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $(".quoteitemsarea[data-quoteitem='"+item+"']").empty();
                    $(".quoteleadtime").empty().html(response.data.leadtime);
                    $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                    $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                    $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                    $(".quoteleadshipcostinpt[data-item='rush_cost']").val(response.data.rush_cost);
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                    $("#loader").hide();
                    init_leadquotes_content();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    $(".addquoteitem").unbind('click').click(function (){
        show_leadquoteitemsearch();
    });
    $(".billingsameinpt").find('i').unbind('click').click(function (){
        var params=new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        var url = "/leadquote/billingsame";
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.billingsame)==1) {
                    $(".billingsameinpt").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
                    $("select[data-item='billing_country']").prop('disabled', true);
                    $("input.quotebilladdrother").prop('disabled', true);
                    $("input[data-item='billing_zip']").prop('disabled', true);
                    $("input[data-item='billing_city']").prop('disabled', true);
                    $("select[data-item='billing_state']").prop('disabled', true);
                    $(".quotebillcountryarea").addClass('billingsame');
                    $(".quotebilladdressother").addClass('billingsame');
                } else {
                    $(".billingsameinpt").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
                    $("select[data-item='billing_country']").prop('disabled', false);
                    $("input.quotebilladdrother").prop('disabled', false);
                    $("input[data-item='billing_zip']").prop('disabled', false);
                    $("input[data-item='billing_city']").prop('disabled', false);
                    $("select[data-item='billing_state']").prop('disabled', false);
                    $(".quotebillcountryarea").removeClass('billingsame');
                    $(".quotebilladdressother").removeClass('billingsame');
                }
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Copy billing address
    $(".billingaddresscopy").unbind('click').click(function(){
        var element = document.querySelector("#billingcompileaddress");
        copyToClipboard(element);
        $('.quoteaddressinpt[data-item="billing_company"]').focus();
    });
    $(".shipaddrescopy").unbind('click').click(function (){
        var element = document.querySelector("#shipingcompileaddress");
        copyToClipboard(element);
        $('.quoteaddressinpt[data-item="shipping_company"]').focus();
    });
}

function show_leadquoteitemsearch() {
    var params=new Array();
    params.push({name: 'session', value: $("#quotesessionid").val()});
    var url="/leadquote/show_itemsearch";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','455px');
            $("#artNextModal").find('.modal-title').empty().html('Quote Item');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            if (response.data.showother=='1') {
                $("div.quote_itemedit_text").show();
            } else {
                $("div.quote_itemedit_text").hide();
            }
            // $("select#orderitem_id").searchable();
            $('#quoteitem_id').select2({
                dropdownParent: $('#artNextModal'),
                matcher: matchStart,
            });
            // $("select#orderitem_id").focus();
            $("select#quoteitem_id").change(function(){
                var item_id=$("select#quoteitem_id").val();
                switch(item_id) {
                    case '-1':
                        $("div.quote_itemedit_text").show();
                        $("div.quote_itemedit_text label").empty().html($("select#quoteitem_id option:selected").text());
                        break;
                    case '-2':
                        $("div.quote_itemedit_text").show();
                        $("div.quote_itemedit_text label").empty().html($("select#quoteitem_id option:selected").text());
                        break;
                    case '-3':
                        $("div.quote_itemedit_text").show();
                        $("div.quote_itemedit_text label").empty().html($("select#quoteitem_id option:selected").text());
                        break;
                    default:
                        $("div.quote_itemedit_text").hide();
                        break;
                }
            })
            $("div.quote_itemedit_save").click(function(){
                save_leadquoteitem();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function save_leadquoteitem() {
    var params=new Array();
    params.push({name: 'session', value: $("#quotesessionid").val()});
    params.push({name: 'item_id', value:$("select#quoteitem_id").val()});
    params.push({name: 'quote_item', value:$("textarea.quoteitemsvalue").val()});
    var url="/leadquote/addquoteitem";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            // $("#artNextModal").modal('hide');
            $("#quoteitemtabledata").empty().html(response.data.item_content);
            $(".quoteleadtime").empty().html(response.data.leadtime);
            $(".quoteshippingcostarea").empty().html(response.data.shippingview);
            $("input[data-item='sales_tax']").val(response.data.tax);
            $("input[data-item='rush_cost']").val(response.data.rush_cost);
            $("input[data-item='shipping_cost']").val(response.data.shipping_cost);
            $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
            $(".quotetotalvalue").empty().html(response.data.total);
            $("#loader").hide();
            init_leadquotes_content();
            // $(".addprintdetails[data-quoteitem='"+response.data.newitem+"']").trigger('click');
            // Print details
            $("#artNextModal").find('div.modal-dialog').css('width','1077px');
            $("#artNextModal").find('.modal-title').empty().html('Order Item Imprint');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.impritview);
            init_quote_printdetails();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_quote_printdetails() {
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
        params.push({name: 'quotesession', value: $("#quotesessionid").val()});
        // Save Params
        change_quote_printdetails(params);
    });
    $("select.locationtype").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'imprint_type'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'quotesession', value: $("#quotesessionid").val()});
        // Save Params
        change_quote_printdetails(params);
    });
    $("input.imprintrepeatnote").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'repeat_note'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'quotesession', value: $("#quotesessionid").val()});
        // Save Params
        change_quote_printdetails(params);
    });
    $("select.imprintcolorschoice").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: 'num_colors'});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'quotesession', value: $("#quotesessionid").val()});
        // Save Params
        change_quote_printdetails(params);
    });
    $("input.imprintprice").unbind('change').change(function(){
        var params=new Array();
        var details=$(this).data('details');
        params.push({name:'newval', value: $(this).val()});
        params.push({name:'fldname', value: $(this).data('fldname')});
        params.push({name:'details', value: details});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'quotesession', value: $("#quotesessionid").val()});
        // Save Params
        change_quote_printdetails(params);
    });
    // Repeat Note
    $("div.repeatdetail.active").unbind('click').click(function(){
        var detail=$(this).data('details');
        edit_quoteprintnote(detail);
    });
    // Blank
    $("input.quoteblankchk").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params=new Array();
        params.push({name:'fldname', value: 'quote_blank'});
        params.push({name:'newval', value:newval});
        params.push({name:'imprintsession', value: $("input#imprintsession").val()});
        params.push({name: 'quotesession', value: $("#quotesessionid").val()});
        var url = "/leadquote/quoteprintdetails_blankquote";
        $.post(url, params, function (response){
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
                init_quote_printdetails();
            } else {
                show_error(response);
            }
        },'json')
    });
    // Save Print Details
    $("div.saveimprintdetailsdata").unbind('click').click(function(){
        save_quoteprint_details();
    });
}

function edit_quoteprintnote(detail) {
    var params=new Array();
    params.push({name:'imprintsession', value: $("input#imprintsession").val()});
    params.push({name: 'quotesession', value: $("#quotesessionid").val()});
    params.push({name:'details', value: detail});
    $.colorbox({
        opacity: .7,
        transition: 'fade',
        ajax: true,
        width:440,
        href: '/leadquote/edit_repeatnote',
        data: params,
        onComplete: function() {
            $.colorbox.resize();
            init_edit_quoterepeatnote(detail);
        }
    });
}

function init_edit_quoterepeatnote(detail) {
    $("div.order_itemedit_save").unbind('click').click(function(){
        var note=$("input#repeatnotevalue").val();
        if (note=='') {
            alert('Enter Repeat Note');
        } else {
            var params=new Array();
            params.push({name:'detail_id', value: detail});
            params.push({name:'repeat_note',  value: note});
            params.push({name:'imprintsession', value: $("input#imprintsession").val()});
            params.push({name: 'quotesession', value: $("#quotesessionid").val()});
            var url="/leadquote/repeatnote_save";
            $.post(url,params, function(response){
                if (response.errors=='') {
                    $.colorbox.close();
                    $("div.repeatdetail[data-details='"+detail+"']").addClass('full');
                    init_quote_printdetails();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
}
// Save Imprint Details
function save_quoteprint_details() {
    var url='/leadquote/save_imprintdetails';
    var params=new Array();
    params.push({name:'imprintsession', value: $("input#imprintsession").val()});
    params.push({name: 'quotesession', value: $("#quotesessionid").val()});
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $(".quoteitemsarea[data-quoteitem='"+response.data.item_id+"']").empty().html(response.data.itemcontent);
            $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
            $("input[data-item='sales_tax']").val(response.data.tax);
            $(".quotetotalvalue").empty().html(response.data.total);
            init_leadquotes_content();
        } else {
            show_error(response);
        }
    },'json');
}

function change_quote_printdetails(params) {
    var url="/leadquote/quoteprintdetails_change";
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (response.data.fldname=='active') {
                var details=response.data.details;
                var newval=response.data.newval;
                activate_quoteprint_details(details, newval);
            } else if (response.data.fldname=='num_colors') {
                var details=response.data.details;
                var newval=response.data.newval;
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
            init_quote_printdetails();
        } else {
            show_error(response);
        }
    },'json');
}

function activate_quoteprint_details(details, newval) {
    if (newval==1) {
        $("input.quoteblankchk").prop('checked',false);
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

function leadquote_edit(quote_id) {
    var params = new Array();
    params.push({name: 'quote_id', value: quote_id});
    params.push({name: 'edit_mode', value: 0});
    var url = '/leadquote/quoteedit';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#quotepopupdetails").empty().html(response.data.quotecontent);
            $("#quotepopupdetails").show();
            $(".quotepopupclose").show();
            $(".leadquotenumberlist").unbind('click');
            $(".quotesaddnew").unbind('click');
            $(".datarow[data-leadquote='"+quote_id+"']").children('div').addClass('active');
            init_leadquotes_view();
        } else {
            show_error(response);
        }
    },'json');
}

function init_leadquotes_view() {
    $(".quotepopupclose").unbind('click').click(function (){
        var quoteactive = $("#quoteleadnumber").val();
        $("#quotepopupdetails").empty();
        $("#quotepopupdetails").hide();
        $(".quotepopupclose").hide();
        $(".datarow[data-leadquote='"+quoteactive+"']").children('div').removeClass('active');
        $(".leadquotenumberlist").unbind('click').click(function(){
            var quote_id = $(this).data('leadquote');
            leadquote_edit(quote_id);
        })
        $(".quotesaddnew").unbind('click').click(function () {
            addnewcustomquote();
        });
    });
    $(".leadquoteeditbtn").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'quote_id', value: $("#quoteleadnumber").val()});
        params.push({name: 'edit_mode', value: 1});
        var url = '/leadquote/quoteedit';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#quotepopupdetails").empty().html(response.data.quotecontent);
                $("#quotepopupdetails").show();
                $(".quotepopupclose").show();
                $(".leadquotenumberlist").unbind('click');
                $(".quotesaddnew").unbind('click');
                if (parseInt($("#quotemapuse").val())==1) {
                    initShipQuoteAutocomplete();
                    initBillQuoteAutocomplete();
                }
                // init_billingaddress_copy();
                init_leadquotes_content();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".quoteactionduplicate.active").unbind('click').click(function () {
        if (confirm('Duplicate Quote ?')==true) {
            var quote_id = $(this).data('quote');
            var params = new Array();
            params.push({name: 'quote_id', value: quote_id});
            var url = '/leadquote/quoteduplicate';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $("#quotepopupdetails").empty().html(response.data.quotecontent);
                    $("#quotepopupdetails").show();
                    $(".quotepopupclose").show();
                    $(".leadquotenumberlist").unbind('click');
                    $(".quotesaddnew").unbind('click');
                    $("#loader").hide();
                    init_leadquotes_content();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    $(".quoteactionpdfdoc.active").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'quote_id', value: $(this).data('quote')});
        var url = '/leadquote/quotepdfdoc';
        $.post(url, params, function (response){
            if (response.errors=='') {
                var newWin = window.open(response.data.docurl,"Quoute PDF","width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
            } else {
                show_error(response);
            }
        },'json')
    });
    $(".quoteactionaddorder.active").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'quote_id', value: $(this).data('quote')});
        var url = '/leadquote/quoteaddorder';
        $("#loader").show();
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#loader").hide();
                var callpage = 'leads';
                $("#leadformModal").modal('hide');
                $("#artModalLabel").empty().html(response.data.header);
                $("#artModal").find('div.modal-body').empty().html(response.data.content);
                $("#artModal").find('div.modal-dialog').css('width','1004px');
                $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
                $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_onlineleadorder_edit();
                init_rushpast();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $(".quoteactionsend.active").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'quote_id', value: $(this).data('quote')});
        var url = '/leadquote/quotepreparesend';
        $.post(url, params, function (response){
            $("#artNextModal").find('div.modal-dialog').css('width','455px');
            $("#artNextModal").find('.modal-title').empty().html('Send PDF');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $(".quoteemail_send").unbind('click').click(function(){
                var quote = $(this).data('quote');
                send_leadquote(quote);
            });
        },'json');
    });
}

function init_billingaddress_copy() {
    var copyTextareaBtn = document.querySelector('.billingaddresscopy');

    copyTextareaBtn.addEventListener('click', function(event) {
        var copyTextarea = document.getElementById('billingcompileaddress');

        copyTextarea.focus();
        copyTextarea.select();

        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Msg '+msg);
        } catch (err) {
            console.log('Oops, unable to copy');
        }
    });
}

function send_leadquote(quote) {
    var params = new Array();
    params.push({name: 'quote_id', value: quote});
    params.push({name: 'quoteemail_from', value: $("#quoteemail_from").val()});
    params.push({name: 'quoteemail_to', value: $("#quoteemail_to").val()});
    params.push({name: 'quoteemail_subject', value: $("#quoteemail_subject").val()});
    params.push({name: 'quoteemail_message', value: $("#quoteemail_message").val()});
    var url = '/leadquote/quotesend';
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#loader").hide();
            $("#artNextModal").modal('hide');
            $(".lead_history").empty().html(response.data.history);
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function copyToClipboard(element) {
    // var $temp = $("<textarea>");
    // var brRegex = /<br\s*[\/]?>/gi;
    // $("body").append($temp);
    // $temp.val($(element).html().replace(brRegex, "\r\n")).select();
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
    // document.execCommand("copy");
    $(element).hide();
}
