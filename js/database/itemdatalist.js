function init_itemslist_view(brand) {
    initItemsListPagination(brand);
    // $(".newvendor").live('click',function(){
    //     add_vendor();
    // });
}

function initItemsListPagination(brand) {
    // count entries inside the hidden content
    var num_entries = $('.itemstotals[data-brand="'+brand+'"]').val();
    // var perpage = itemsperpage;
    var perpage = $('.itemsperpage[data-brand="'+brand+'"]').val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $('.itemlistpagination[data-brand="'+brand+'"]').empty();
        pageItemsListCallback(0, brand);
    } else {
        var curpage = $('.itemspagenum[data-brand="'+brand+'"]').val();
        var callbackname = ''
        if (brand=='SB') {
            $('.itemlistpagination[data-brand="'+brand+'"]').mypagination(num_entries, {
                current_page: curpage,
                callback: pageSBItemsListCallback,
                items_per_page: perpage, // Show only one item per page
                load_first_page: true,
                num_edge_entries : 1,
                num_display_entries : 5,
                prev_text : '&laquo;',
                next_text : '&raquo;'
            });
        } else {
            $('.itemlistpagination[data-brand="'+brand+'"]').mypagination(num_entries, {
                current_page: curpage,
                callback: pageBTItemsListCallback,
                items_per_page: perpage, // Show only one item per page
                load_first_page: true,
                num_edge_entries : 1,
                num_display_entries : 5,
                prev_text : '&laquo;',
                next_text : '&raquo;'
            });
        }
        // Create content inside pagination element
    }
}

function pageSBItemsListCallback(page_index) {
    pageItemsListCallback(page_index, 'SB');
}

function pageBTItemsListCallback(page_index) {
    pageItemsListCallback(page_index, 'BT');
}

function pageItemsListCallback(page_index, brand) {
    console.log('Brand  '+brand);
    params = new Array();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $('.itemsperpage[data-brand="'+brand+'"]').val()});
    params.push({name: 'order_by', value: $('.itemsorder[data-brand="'+brand+'"]').val()});
    params.push({name: 'direction', value: $('.itemsorderdirect[data-brand="'+brand+'"]').val()});
    params.push({name: 'maxval', value: $('.itemstotals[data-brand="'+brand+'"]').val()});
    params.push({name: 'search', value: $('.search_input[data-brand="'+brand+'"]').val()});
    params.push({name: 'vendor', value: $('.vendorfilter[data-brand="'+brand+'"]').val()});
    params.push({name: 'itemstatus',  value: $('.itemlistatusfilter[data-brand="'+brand+'"]').val()});
    params.push({name: 'brand', value: brand});
    $("#loader").show();
    $.post('/database/itemlistsdata', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('.itemlist-tablebody[data-brand="'+brand+'"]').empty().html(response.data.content);
            // init_vendor_content();
        } else {
            show_error(response);
        }
    },'json');

}