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
        $.post(url, dat, function (response) {
            if (response.errors=='') {
                $("#quotepopupdetails").empty().html(response.data.quotecontent);
                $("#quotepopupdetails").show();
                $(".quotepopupclose").show();
                $(".leadquotenumberlist").unbind('click');
                $(".quotesaddnew").unbind('click');
                init_leadquotes_content();
            } else {
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
                $("#quotepopupdetails").empty();
                $("#quotepopupdetails").hide();
                $(".quotepopupclose").hide();
                $(".quotesdataarea").empty().html(response.data.quotescontent);
                $(".leadquotenumberlist").unbind('click').click(function(){
                    var quote_id = $(this).data('leadquote');
                    leadquote_edit(quote_id);
                })
                $(".quotesaddnew").unbind('click').click(function () {
                    addnewcustomquote();
                });
            } else {
                show_error(response);
            }
        },'json');
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
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Address Input
    $(".quoteaddressinpt").unbind('change').change(function(){
        var params = new Array();
        params.push({name: 'session', value: $("#quotesessionid").val()});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leadquote/quoteaddresschange';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.shiprebuild)==1) {
                    // Update zip, city state
                    $(".quoteaddressinpt[data-item='shipping_zip']").val(response.data.shipping_zip);
                    $(".quoteaddressinpt[data-item='shipping_city']").val(response.data.shipping_city);
                    $(".quoteaddressinpt[data-item='shipping_state']").val(response.data.shipping_state);
                }
                if (parseInt(response.data.calcship)==1) {
                    // Update shipping cost
                    $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                    $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                }
                if (parseInt(response.data.totalcalc)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                }
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
                $("#loader").hide();
                init_leadquotes_content();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');

    });
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
                    init_leadquotes_content();
                } else {
                    $(".quoteitemrowsubtotal[data-item='"+itemcolor+"'][data-quoteitem='"+item+"']").empty().html(response.data.itemcolor_subtotal);
                }
                if (parseInt(response.data.totals)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.items_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
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
    })
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
            init_quote_printdetails();
        } else {
            show_error(response);
        }
    },'json');
}

function activate_quoteprint_details(details, newval) {
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
        var url = '/leadmanagement/quoteedit';
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#quotepopupdetails").empty().html(response.data.quotecontent);
                $("#quotepopupdetails").show();
                $(".quotepopupclose").show();
                $(".leadquotenumberlist").unbind('click');
                $(".quotesaddnew").unbind('click');
                init_leadquotes_content();
            } else {
                show_error(response);
            }
        },'json');
    });
}