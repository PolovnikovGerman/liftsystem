function init_btitemslist_view(brand) {
    initItemsListPagination();
    $(".itemlist-tablehead").find(".sortable").unbind('click').click(function () {
        var fld=$(this).data('sortcell');
        // sort_itemlist(fld, brand);
    });
    $(".itemcategoryfilter").unbind('change').change(function () {
        var newcat = $(this).val();
        $(".btcategorybtn").removeClass('active');
        $(".btcategorybtn[data-category='"+newcat+"']").addClass('active');
        search_itemlists();
    });
    $(".btcategorybtn").unbind('click').click(function(){
        if ($(this).hasClass('locked')) {
        } else {
            var newcat = $(this).data('category');
            $(".btcategorybtn").removeClass('active');
            $(".btcategorybtn[data-category='"+newcat+"']").addClass('active');
            $(".itemcategoryfilter").val(newcat);
            search_itemlists();
        }
    })
    $(".itemnamesearch").keypress(function(event){
        if (event.which == 13) {
            search_itemlists();
        }
    });
    $('.itemsearchbtn').unbind('click').click(function(){
        search_itemlists();
    });
    $('.itemclearsearch').unbind('click').click(function(){
        $(".itemnamesearch").val('');
        search_itemlists();
    })

    $(".itemvendorfilter").unbind('change').change(function(){
        search_itemlists();
    });
    $(".itemstatusfilter").unbind('change').change(function(){
        search_itemlists();
    });
    $(".itemmisinfofilter").unbind('change').change(function(){
        search_itemlists();
    });

    $(".listaction").unbind('click').click(function(){
        edit_itemlist(0, brand);
    });
}

function initItemsListPagination() {
    // count entries inside the hidden content
    var num_entries = $('#btitemstotals').val();
    // var perpage = itemsperpage;
    var perpage = $('#btitemsperpage').val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $('#btitemsPaginator').empty();
        pageBTItemsListCallback(0);
    } else {
        var curpage = $('#btitemspagenum').val();
        $('#btitemsPaginator').mypagination(num_entries, {
            current_page: curpage,
            callback: pageBTItemsListCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '&laquo;',
            next_text : '&raquo;'
        });
        // Create content inside pagination element
    }
}
function pageBTItemsListCallback(page_index) {
    var params = prepare_search_params();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $('#btitemsperpage').val()});
    params.push({name: 'order_by', value: $('#btitemsorder').val()});
    params.push({name: 'direction', value: $('#btitemsorderdirect').val()});
    params.push({name: 'maxval', value: $('#btitemstotals').val()});
    $("#loader").show();
    $.post('/dbitems/itemlistsdata', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('#btitemdata').empty().html(response.data.content);
            $('#btitemspagenum').val(page_index);
            // init_itemlist_manage(brand);
            init_itemlist_content();
        } else {
            show_error(response);
        }
    },'json');
}

function search_itemlists() {
    var params = prepare_search_params();
    $("#loader").show();
    $.post('/dbitems/itemlistsearch', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('#btitemstotals').val(response.data.totals);
            $('#btitemspagenum').val(0);
            $(".tabledatatitle").empty().html(response.data.category_total);
            $('.tabledatataotalvalue').empty().html(response.data.totals_view);
            initItemsListPagination();
        } else {
            show_error(response);
        }
    },'json');
}

function prepare_search_params() {
    var params = new Array();
    params.push({name: 'search', value: $('.itemnamesearch').val()});
    params.push({name: 'vendor', value: $('.itemvendorfilter').val()});
    params.push({name: 'itemstatus',  value: $('.itemstatusfilter').val()});
    params.push({name: 'missinfo', value: $(".itemmisinfofilter").val()});
    params.push({name: 'category', value: $(".itemcategoryfilter").val()});
    return params;
}

function init_itemlist_content() {
    $("#addnewbtitems").unbind('click').click(function(){
        var item=0;
        edit_btitem(item);
    });
    $(".btitemedit").unbind('click').click(function () {
        var item=$(this).data('item');
        edit_btitem(item);
    })
}

function edit_btitem(item) {
    var params = new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'brand', value: 'BT'});
    var url = '/dbitems/itemlistdetails';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#itemDetailsModalLabel").empty().html(response.data.header);
            $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
            $("#itemDetailsModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(response.data.editmode)==1) {
                // init_btitemdetails_edit();
            } else {
                init_btitemdetails_view(item);
            }
        } else {
            show_error(response);
        }
    },'json');
}
