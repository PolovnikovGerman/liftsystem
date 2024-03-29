$(document).ready(function () {
    init_relievers_items();
    $(".tabledataheader").find('div.sortable').unbind('click').click(function (){
        var fld = $(this).data('sortcell');
        sort_sritems(fld);
    });
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
    params.push({name: 'order_by', value: $('#sritemsorder').val()});
    params.push({name: 'direction', value: $('#sritemsorderdirect').val()});
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
            $('#relieversitemdata').find("div.tabledataarea").scrollpanel({
                'prefix' : 'sp-'
            });
            init_relieveritems_content();
            leftmenu_alignment();
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
    $(".relivitemedit").unbind('click').click(function () {
        var item = $(this).data('item');
        edit_reliever_item(item);
    });
    $("#addnewrelievers").unbind('click').click(function () {
        // var item = 0;
        // edit_reliever_item(item);
        prepare_newsritem();
    })
}

function prepare_newsritem() {
    var params = new Array();
    params.push({name: 'brand', value: 'SR'});
    var url="/dbitems/addnewitemform";
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".sritemnewaddarea").empty().html(response.data.content).show();
            init_addnewsritem();
        } else {
            show_error(response);
        }
    },'json');
}

function init_addnewsritem() {
    $(".sritemnewaddarea").find('div.canceladd').click(function () {
        $(".sritemnewaddarea").empty();
        $(".sritemnewaddarea").hide();
    });
    $(".sritemnewaddarea").find('div.procedaddnewitem').unbind('click').click(function () {
        var params = new Array();
        var category = $("#itemnewcategory").val();
        params.push({name: 'category', value: category});
        params.push({name: 'itemname', value: $("#itemnewname").val()});
        params.push({name: 'brand', value: 'SR'});
        var url = "/dbitems/addnewitem";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".sritemnewaddarea").empty().hide();
                // $(".btcategorybtn").removeClass('active');
                // $(".btcategorybtn[data-category='" + category + "']").removeClass('locked').addClass('active');
                // $(".itemcategoryfilter option[data-categ='" + category + "']").prop('disabled', false);
                // $(".itemcategoryfilter").val(category);
                var item = response.data.newitem;
                var itmparams = new Array();
                itmparams.push({name: 'item_id', value: item});
                itmparams.push({name: 'editmode', value: 1});
                itmparams.push({name: 'brand', value: 'SR'});
                var url = '/dbitems/relieve_item_edit';
                $.post(url, itmparams, function (response) {
                    if (response.errors == '') {
                        $("#itemDetailsModalLabel").empty().html(response.data.header);
                        $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                        $("#itemDetailsModal").modal({backdrop: 'static', keyboard: false, show: true});
                        init_relievitemdetails_edit();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            } else {
                show_error(response);
            }
        }, 'json');
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

function edit_reliever_item(item) {
    var params = new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'brand', value: 'SR'});
    var url = '/dbitems/relieve_item_edit';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#itemDetailsModalLabel").empty().html(response.data.header);
            $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
            $("#itemDetailsModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(response.data.editmode)==1) {
                // image_slider_init();
                // $(".displayprice").css('cursor','pointer');
                // $(".template-checkbox").css('cursor','pointer');
                // $(".implintdatavalue.sellopt").css('cursor','pointer');
                // init_vectorfile_upload();
                // init_item_similar();
                init_relievitemdetails_edit();
            } else {
                init_relievitemdetails_view(item);
            }
        } else {
            show_error(response);
        }
    },'json');
}

function sort_sritems(fld) {
    var cursort = $('#sritemsorder').val();
    var curdirec = $('#sritemsorderdirect').val();
    $(".tabledataheader").find("div.ascsort").remove();
    $(".tabledataheader").find("div.descsort").remove();
    if (cursort==fld) {
        // Change direction
        if (curdirec=='asc') {
            $(".tabledataheader").find('div[data-sortcell="'+fld+'"]').append('<div class="descsort">&nbsp;</div>');
            $('#sritemsorderdirect').val('desc');
        } else {
            $(".tabledataheader").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
            $('#sritemsorderdirect').val('asc');
        }
    } else {
        $(".tabledataheader").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
        $('#sritemsorderdirect').val('asc');
        $('#sritemsorder').val(fld);
    }
    var pageindex = $('#relieveitemscurpage').val();
    pageReliveItemsCallback(pageindex);
}