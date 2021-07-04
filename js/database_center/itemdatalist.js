function init_itemslist_view(brand) {
    initItemsListPagination(brand);
    $(".itemlist-tablehead").find(".sortable").unbind('click').click(function () {
        var fld=$(this).data('sortcell');
        sort_itemlist(fld, brand);
    });
    $(".search_input[data-brand='"+brand+"']").keypress(function(event){
         if (event.which == 13) {
             search_itemlists(brand);
         }
    });
    $('.searchlist-btn[data-brand="'+brand+'"]').unbind('click').click(function(){
        search_itemlists(brand);
    });
    $('.clearsearchlist-btn[data-brand="'+brand+'"]').unbind('click').click(function(){
        $(".search_input[data-brand='"+brand+"']").val('');
        search_itemlists(brand);
    })
    $(".vendorfilter[data-brand='"+brand+"']").unbind('change').change(function(){
        search_itemlists(brand);
    });
    $(".itemlistatusfilter[data-brand='"+brand+"']").unbind('change').change(function(){
        search_itemlists(brand);
    });
    $(".listaction").unbind('click').click(function(){
        edit_itemlist(0, brand);
    });
    init_itemlist_manage(brand);
}

function init_itemlist_manage(brand) {
    $(".categorymanagebtn.locked[data-brand='"+brand+"']").unbind('click').click(function () {
        $("select.itemlist_category[data-brand='"+brand+"']").prop('disabled',false);
        $(this).removeClass('locked').addClass('unlocked');
        init_item_categorychange(brand);
    });
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
    var params = new Array();
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
    $.post('/dbitems/itemlistsdata', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('.itemlist-tablebody[data-brand="'+brand+'"]').empty().html(response.data.content);
            $('.itemspagenum[data-brand="'+brand+'"]').val(page_index);
            $(".categorymanagebtn[data-brand='"+brand+"']").removeClass('unlocked').addClass('locked');
            init_itemlist_manage(brand);
            init_itemlist_content(brand);
        } else {
            show_error(response);
        }
    },'json');
}

function sort_itemlist(fld, brand) {
    var cursort = $('.itemsorder[data-brand="'+brand+'"]').val();
    var curdirec = $('.itemsorderdirect[data-brand="'+brand+'"]').val();
    if (cursort==fld) {
        // Change direction
        if (curdirec=='asc') {
            $(".itemlist-tablehead[data-brand='"+brand+"']").find("div.ascsort").remove();
            $(".itemlist-tablehead[data-brand='"+brand+"']").find('div[data-sortcell="'+fld+'"]').append('<div class="descsort">&nbsp;</div>');
            $('.itemsorderdirect[data-brand="'+brand+'"]').val('desc');
        } else {
            $(".itemlist-tablehead[data-brand='"+brand+"']").find("div.descsort").remove();
            $(".itemlist-tablehead[data-brand='"+brand+"']").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
            $('.itemsorderdirect[data-brand="'+brand+'"]').val('asc');
        }
    } else {
        $(".itemlist-tablehead[data-brand='"+brand+"']").find("div.ascsort").remove();
        $(".itemlist-tablehead[data-brand='"+brand+"']").find("div.descsort").remove();
        $(".itemlist-tablehead[data-brand='"+brand+"']").find("div[data-sortcell='"+cursort+"']").removeClass('active');
        $(".itemlist-tablehead[data-brand='"+brand+"']").find("div[data-sortcell='"+fld+"']").addClass('active');
        $(".itemlist-tablehead[data-brand='"+brand+"']").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
        $('.itemsorderdirect[data-brand="'+brand+'"]').val('asc');
        $('.itemsorder[data-brand="'+brand+'"]').val(fld);
    }
    var pageindex = $('.itemspagenum[data-brand="'+brand+'"]').val();
    pageItemsListCallback(pageindex, brand);
}

function search_itemlists(brand) {
    var params = new Array();
    params.push({name: 'search', value: $('.search_input[data-brand="'+brand+'"]').val()});
    params.push({name: 'vendor', value: $('.vendorfilter[data-brand="'+brand+'"]').val()});
    params.push({name: 'itemstatus',  value: $('.itemlistatusfilter[data-brand="'+brand+'"]').val()});
    params.push({name: 'brand', value: brand});
    $("#loader").show();
    $.post('/dbitems/itemlistsearch', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('.itemstotals[data-brand="'+brand+'"]').val(response.data.totals);
            $('.itemspagenum[data-brand="'+brand+'"]').val(0);
            $('.itemslisttotalsview[data-brand="'+brand+'"]').empty().html(response.data.totals_view);
            initItemsListPagination(brand);
        } else {
            show_error(response);
        }
    },'json');

}

function init_itemlist_content(brand) {
    $(".itemlist-tablerow").hover(function(){
        $(this).addClass('itemlistactiverow');
    }, function() {
        $(this).removeClass('itemlistactiverow');
    });
    $(".itemlist-tablerow").find("div.listvendor").each(function(){
        var vendid=$(this).prop('id');
        $("#"+vendid).fu_popover(
            {
                content: $(this).data('content'),
                dismissable: true,
                autoHide: true,
                autoHideDelay: 7000,
                placement:'top',
                trigger: 'hover',
                width: '180px',
                themeName:'Theme_blue'
            }
        );
        $("#"+vendid).hover(function () {
        }, function () {
            $("#"+vendid).fu_popover("hide");
        });
    });
    $(".itemlist-tablerow").find("div.listmissinginfo.missing").each(function() {
        var missid=$(this).prop('id');
        $("#"+missid).fu_popover(
            {
                content: $(this).data('content'),
                dismissable: true,
                autoHide: true,
                autoHideDelay: 20000,
                placement:'top',
                trigger: 'hover',
                width: '180px',
                themeName:'theme_red'
            }
        );
        $("#"+missid).hover(function () {
        }, function () {
            $("#"+missid).fu_popover("hide");
        });
    });
    $("div.listitempreview").hover(
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
    $(".listitemedit").unbind('click').click(function () {
        var item = $(this).data('item');
        edit_itemlist(item, brand);
    })
}

function init_item_categorychange(brand) {
    $("select.itemlist_category[data-brand='" + brand + "']").unbind('change').change(function () {
        var newval = $(this).val();
        if (parseInt(newval) == 0) {
            $(this).removeClass('selected');
        } else {
            $(this).addClass('selected');
        }
        // Collect data
        var item = $(this).data('item');
        var categ1 = $(".itemlist_category[data-brand='" + brand + "'][data-item='" + item + "'][data-categ='category1']").val()
        var categ2 = $(".itemlist_category[data-brand='" + brand + "'][data-item='" + item + "'][data-categ='category2']").val()
        var categ3 = $(".itemlist_category[data-brand='" + brand + "'][data-item='" + item + "'][data-categ='category3']").val()
        var params = new Array();
        params.push({name: 'brand', value: brand});
        params.push({name: 'item_id', value: item});
        params.push({name: 'category1', value: categ1});
        params.push({name: 'category2', value: categ2});
        params.push({name: 'category3', value: categ3});
        var url = '/database/itemlistcategory';
        $.post(url, params, function (response) {
            if (response.errors == '') {
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".categorymanagebtn.unlocked[data-brand='" + brand + "']").unbind('click').click(function () {
        $(this).removeClass('unlocked').addClass('locked');
        $("select.itemlist_category[data-brand='" + brand + "']").prop('disabled', true);
        init_itemlist_manage(brand);
    });
}

function edit_itemlist(item, brand) {
    var params = new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'brand', value: brand});
    var url = '/dbitems/itemlistdetails';
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("#itemDetailsModalLabel").empty().html(response.data.header);
            $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
            $("#itemDetailsModal").find('div.modal-dialog').css('width','1345px');
            // $("#pageModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#itemDetailsModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(response.data.editmode)==1) {
                image_slider_init();
                $(".displayprice").css('cursor','pointer');
                $(".template-checkbox").css('cursor','pointer');
                $(".implintdatavalue.sellopt").css('cursor','pointer');
                init_vectorfile_upload();
                init_item_similar();
                init_itemlist_details_edit();
            } else {
                init_itemlist_details_view();
            }
            //
        } else {
            show_error(response);
        }
    },'json');
}