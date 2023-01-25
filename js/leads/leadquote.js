$(document).ready(function (){
    // Add new Custom Quote
    $(".quotesaddnew").unbind('click').click(function () {
        addnewcustomquote();
    });

});


function addnewcustomquote() {
    var lead_num=$("div.lead_popup_number").text();
    var msg="You will now save the updates of the "+lead_num+" by creating the quote.  Ok?";
    if (confirm(msg)==true) {
        var url=mainurl+"/lead_addquote";
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
    });
    $(".leadquotesavebtn").unbind('click').click(function (){
       // Save quote
    });
    $(".leadquoteeditbtn").unbind('click').click(function (){
        // Edit current quote
    });
    $("input.quouteitem_input").unbind('change').change(function(){
        var item = $(this).data('item');
        var itemcolor = $(this).data('quoteitem');
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
                if (parseInt(response.data.totals)==1) {
                    $(".quoteitemsubtotalvalue").empty().html(response.data.item_subtotal);
                    $(".quotetotalvalue").empty().html(response.data.total);
                }
            } else {

            }
        },'json');
    });
}