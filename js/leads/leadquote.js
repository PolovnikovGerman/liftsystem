function addnewcustomquote() {
    var lead_num=$("div.lead_popup_number").text();
    var msg="You will now save the updates of the "+lead_num+" by creating the quote.  Ok?";
    if (confirm(msg)==true) {
        var url="/leadmanagement/lead_addquote";
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
        $("#quotepopupdetails").empty();
        $("#quotepopupdetails").hide();
        $(".quotepopupclose").hide();
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
        var url = '/leadmanagement/quotesave';
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
        var url = '/leadmanagement/quoteitemchange';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.refresh)==1) {
                    $(".quoteitemsarea[data-quoteitem='"+item+"']").empty().html(response.data.itemcontent);
                    init_leadquotes_content();
                } else {
                    $(".quoteitemrowsubtotal[data-item='"+itemcolor+"'][data-quoteitem='"+item+"']").empty().html(response.data.itemcolor_subtotal);
                }
                if (parseInt(response.data.totals)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.item_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                }
            } else {
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
        var url = '/leadmanagement/quoteitemchange';
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
        var url = '/leadmanagement/quoteitemaddcolor';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".quoteitemsarea[data-quoteitem='"+item+"']").empty().html(response.data.itemcontent);
                init_leadquotes_content();
            } else {
                show_error(response);
            }
        },'json');

    });
}

function leadquote_edit(quote_id) {
    var params = new Array();
    params.push({name: 'quote_id', value: quote_id});
    params.push({name: 'edit_mode', value: 0});
    var url = '/leadmanagement/quoteedit';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#quotepopupdetails").empty().html(response.data.quotecontent);
            $("#quotepopupdetails").show();
            $(".quotepopupclose").show();
            $(".leadquotenumberlist").unbind('click');
            $(".quotesaddnew").unbind('click');
            init_leadquotes_view();
        } else {
            show_error(response);
        }
    },'json');
}

function init_leadquotes_view() {
    $(".quotepopupclose").unbind('click').click(function (){
        $("#quotepopupdetails").empty();
        $("#quotepopupdetails").hide();
        $(".quotepopupclose").hide();
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