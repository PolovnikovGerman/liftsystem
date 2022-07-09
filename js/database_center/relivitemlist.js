$(document).ready(function () {
    init_relievers_items();
})

function init_relievers_items() {
    // count entries inside the hidden content
    var num_entries = $('#relieverstotal').val();
    // var perpage = itemsperpage;
    var perpage = $("#reliveitemsperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#relieveritemsPaginator").empty();
        pageReliveItemsCallback(0);
    } else {
        var curpage = $("#relieveitemscurpage").val();
        // Create content inside pagination element
        $("#relieveritemsPaginator").mypagination(num_entries, {
            current_page: curpage,
            callback: pageReliveItemsCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageReliveItemsCallback(page_index) {
    var params = prepare_relieveritem_filters();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#reliveitemsperpage").val()});
    var url = "/dbitems/relieve_itemslist";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div#relieversitemdata").empty().html(response.data.content);
            if ($(".itemnamesearch").val()=='') {
                $(".itemclearsearch").attr('disabled',true).removeClass('active');
            } else {
                $(".itemclearsearch").attr('disabled',false).addClass('active');
            }
            init_relieveritems_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function prepare_relieveritem_filters() {
    var params = new Array();
    params.push({name: 'category', value: $(".itemcategoryfilter").val()});
    params.push({name: 'search', value : $(".itemnamesearch").val()});
    params.push({name: 'status', value: $(".itemstatusfilter").val()});
    params.push({name: 'vendor', value: $(".itemvendorfilter").val()});
    params.push({name: 'misinfo', value: $(".itemmisinfofilter").val()});
    return params;
}

function init_relieveritems_content() {
    $(".relivecategorybtn").unbind('click').click(function () {
        var category = $(this).data('category');
        $(".relivecategorybtn").each(function () {
            $(this).removeClass('active');
        });
        $(".relivecategorybtn[data-category='"+category+"']").addClass('active');
        $(".itemcategoryfilter").val(category);
        relieveritem_search();
    });
    $(".itemcategoryfilter").unbind('change').change(function () {
        var category = $(".itemcategoryfilter").val();
        $(".relivecategorybtn").each(function () {
            $(this).removeClass('active');
        });
        $(".relivecategorybtn[data-category='"+category+"']").addClass('active');
        relieveritem_search();
    });
    // $(".itemnamesearch")
    $(".itemnamesearch").keypress(function(event){
        if (event.which == 13) {
            relieveritem_search();
        }
    });
    // Clear
    $(".itemclearsearch.active").unbind('click').click(function () {
        $(".itemnamesearch").val('');
        relieveritem_search();
    });
    $(".itemstatusfilter").unbind('change').change(function(){
        relieveritem_search();
    });
    $(".itemvendorfilter").unbind('change').change(function () {
        relieveritem_search();
    });
    $(".itemmisinfofilter").unbind('change').change(function () {
        relieveritem_search();
    });
}

function relieveritem_search() {
    var params = prepare_relieveritem_filters();
    var url = '/dbitems/relieve_itemsearch';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#relieverstotal").val(response.data.totals);
            if (parseInt(response.data.totals)==0) {
                $(".tabledatataotalvalue").empty();
            } else {
                $(".tabledatataotalvalue").empty().html(response.data.totals+' items');
            }
            $(".tabledatatitle").empty().html(response.data.category);
            init_relievers_items();
        } else {
            show_error(response);
        }
    },'json');
}