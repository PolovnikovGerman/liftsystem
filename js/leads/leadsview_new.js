function init_leadsview() {
    initLeaddataPagination();
}

function initLeaddataPagination() {
    var num_entries = $('#leadviewtotalrec').val();
    var perpage = $("#perpagelead").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#mainleadpagination").empty();
        $("#leadviewcurpage").val(0);
        pageLeaddataCallback(0);
    } else {
        var curpage = $("#curpagelead").val();
        // Create content inside pagination element
        $("div.leadlist_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeaddataCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeaddataCallback(page_index) {
    var params = leadpaginationparams(page_index);
    var url = 'leads/leadview_data';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadslistdata").empty().html(response.data.content);
            $("#leadviewcurpage").val(page_index);
            // init_leadpage_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}

function leadpaginationparams(page_index) {
    var params = new Array();
    params.push({name: 'limit', value: $("#leadviewperpage").val()});
    params.push({name: 'maxval', value: $("#leadviewtotalrec").val()});
    params.push({name: 'offset', value: page_index});
    params.push({name: 'seach', value: $("input.lead_searchinput").val()});
    params.push({name: 'brand', value: $("#leadviewbrand").val()});

    return params;
}