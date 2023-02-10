function init_leadquotes() {
    initLeadQuotesPagination();
}

function initLeadQuotesPagination() {
    // count entries inside the hidden content
    var num_entries = $('#leadquotestotal').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpageleadqoutes").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.leadqutespaginator").empty();
        $("#curpageleadquote").val(0);
        pageLeadQuotesCallback(0);
    } else {
        var curpage = $("#curpageleadquote").val();
        // Create content inside pagination element
        $("div.leadqutespaginator").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeadQuotesCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeadQuotesCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value: $("#perpageleadqoutes").val()});
    params.push({name:'maxval', value: $('#leadquotestotal').val()});
    params.push({name:'offset', value: page_index});
    params.push({name: 'brand', value: $("#leadquotesbrand").val()});
    var url='/leads/leadquotesdata';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#leadquote_tabledat").empty().html(response.data.content);
            $("#curpageleadquote").val(page_index);
            // init_leadpage_manage();
            if (parseInt($('#leadquotestotal').val()) > 25) {
                $(".leadquote_tabledat").scrollpanel({
                    'prefix' : 'sp-'
                });
                leftmenu_alignment();
            }
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}