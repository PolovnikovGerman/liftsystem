function init_btitemslist_view() {
    $(".btitemnewaddarea").hide();
    initItemsListPagination();
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
    $(".tabledataheader").find('div.sortable').unbind('click').click(function (){
        var fld = $(this).data('sortcell');
        sort_btitems(fld);
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
            $('#btitemdata').find("div.tabledataarea").scrollpanel({
                'prefix' : 'sp-'
            });
            init_itemlist_content();
            jQuery.balloon.init();
            leftmenu_alignment();
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
        // var item=0;
        // edit_btitem(item);
        prepare_newbtitem();
    });
    $(".btitemedit").unbind('click').click(function () {
        var item=$(this).data('item');
        edit_btitem(item);
    })
}

function prepare_newbtitem() {
    var params = new Array();
    params.push({name: 'brand', value: 'BT'});
    var url="/dbitems/addnewitemform";
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".btitemnewaddarea").empty().html(response.data.content).show();
            init_addnewbtitem();
        } else {
            show_error(response);
        }
    },'json');
}

function init_addnewbtitem() {
    $(".btitemnewaddarea").find('div.canceladd').click(function (){
        $(".btitemnewaddarea").empty();
        $(".btitemnewaddarea").hide();
    });
    $("#itemnewcategory").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'category_id', value: $("#itemnewcategory").val()});
        var url = '/dbitems/subcategories_list';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#itemnewsubcategory").empty();
                $("#itemnewsubcategory").append('<option value=""> </option>');
                var list = response.data.subcategories;
                for (i=0; i<list.length; i++) {
                    $("#itemnewsubcategory").append('<option value="'+list[i]['id']+'">'+list[i]['name']+'</option>');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("#itemnewsubcategory").unbind('change').change(function (){
        if ($("#itemnewsubcategory").val()=='-1') {
            var params = new Array();
            params.push({name: 'category_id', value: $("#itemnewcategory").val()});
            var url = '/dbitems/prepare_newsubcateg';
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $(".btitemnewaddarea").hide();
                    $(".btitemnewsucategarea").empty().html(response.data.content).show();
                    init_addnewsubcategory();
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    $(".btitemnewaddarea").find('div.procedaddnewitem').unbind('click').click(function (){
        var params = new Array();
        var category = $("#itemnewcategory").val();
        params.push({name: 'category', value: category });
        params.push({name: 'subcategory', value: $("#itemnewsubcategory").val()});
        params.push({name: 'itemname', value: $("#itemnewname").val()});
        params.push({name: 'brand', value: 'BT'});
        var url="/dbitems/addnewitem";
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".btitemnewaddarea").empty().hide();
                $(".btcategorybtn").removeClass('active');
                $(".btcategorybtn[data-category='"+category+"']").removeClass('locked').addClass('active');
                $(".itemcategoryfilter option[data-categ='"+category+"']").prop('disabled',false);
                $(".itemcategoryfilter").val(category);
                var item = response.data.newitem;
                var itmparams = new Array();
                itmparams.push({name: 'item_id', value: item});
                itmparams.push({name: 'editmode', value: 1});
                itmparams.push({name: 'brand', value: 'BT'});
                var url = '/dbitems/itemlistdetails';
                $.post(url, itmparams, function (response) {
                    if (response.errors=='') {
                        $("#itemDetailsModalLabel").empty().html(response.data.header);
                        $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                        $("#itemDetailsModal").modal({backdrop: 'static', keyboard: false, show: true});
                        init_btitemdetails_edit();
                    } else {
                        show_error(response);
                    }
                },'json');
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_addnewsubcategory() {
    $(".btitemnewsucategarea").find('div.cancelsubcateg').click(function (){
        $(".btitemnewsucategarea").empty()
        $(".btitemnewsucategarea").hide();
        $(".btitemnewaddarea").show();
        $("#itemnewsubcategory").val('');
        init_addnewbtitem();
    });
    $(".procedaddnewsubcateg").unbind('click').click(function(){
        var params = new Array();
        params.push({name: 'category_id', value: $("#itemnewcategory").val()});
        params.push({name: 'subcateg_code', value: $("#newsubcategcode").val()});
        params.push({name: 'subcateg_name', value: $("#newsubcategname").val()});
        var url = '/dbitems/addsubcategory';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#itemnewsubcategory").empty();
                $("#itemnewsubcategory").append('<option value=""> </option>');
                var list = response.data.subcategories;
                for (i=0; i<list.length; i++) {
                    $("#itemnewsubcategory").append('<option value="'+list[i]['id']+'">'+list[i]['name']+'</option>');
                }
                $("#itemnewsubcategory").val(response.data.subcategory);
                $(".btitemnewsucategarea").empty()
                $(".btitemnewsucategarea").hide();
                $(".btitemnewaddarea").show();
                $("#itemnewname").focus();
                init_addnewbtitem();
            }
        },'json');
    });
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
                init_btitemdetails_edit();
            } else {
                init_btitemdetails_view(item);
            }
        } else {
            show_error(response);
        }
    },'json');
}

function sort_btitems(fld) {
    var cursort = $('#btitemsorder').val();
    var curdirec = $('#btitemsorderdirect').val();
    $(".tabledataheader").find("div.ascsort").remove();
    $(".tabledataheader").find("div.descsort").remove();
    if (cursort==fld) {
        // Change direction
        if (curdirec=='asc') {
            $(".tabledataheader").find('div[data-sortcell="'+fld+'"]').append('<div class="descsort">&nbsp;</div>');
            $('#btitemsorderdirect').val('desc');
        } else {
            $(".tabledataheader").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
            $('#btitemsorderdirect').val('asc');
        }
    } else {
        $(".tabledataheader").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
        $('#btitemsorderdirect').val('asc');
        $('#btitemsorder').val(fld);
    }
    var pageindex = $('#btitemspagenum').val();
    pageBTItemsListCallback(pageindex);
}