var itemsperpage = 80;
function init_dbsequence_view() {
    initSequencePagination();
    // Change Brand
    // $("#itemsequencebrandmenu").find("div.left_tab").unbind('click').click(function(){
    //     var brand = $(this).data('brand');
    //     $("#itemsequencebrand").val(brand);
    //     $("#itemsequencebrandmenu").find("div.left_tab").removeClass('active');
    //     $("#itemsequencebrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
    //     search_sequence();
    // });
}

function initSequencePagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrecdbseq').val();
    var perpage = itemsperpage;
    var curpage = $("#seqpagenum").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#dbseqPagination").empty();
        $("input#seqpagenum").val(0);
        pageSequenceCallback(0);
    } else {
        // Create content inside pagination element
        $("#dbseqPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageSequenceCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '&laquo;',
            next_text : '&raquo;'
        });
    }
}

function pageSequenceCallback(page_index) {
    var params = new Array();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: itemsperpage});
    params.push({name: 'search', value: $("#searchdbseq").val()});
    params.push({name: 'vendor_id', value: $("#dbseqvendorselect").val()});
    params.push({name: 'itemperrow', value: $("#iteminrowselect").val()});
    params.push({name: 'total', value: $('#totalrecdbseq').val()});
    params.push({name: 'brand', value: $("#itemsequencebrand").val()});
    var url = '/database/itemsequence_data';
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $(".pagination-legend").empty().html(response.data.label);
            $('#dbseqtabinfo').empty().html(response.data.content);
            $("#loader").hide();
            $("#seqpagenum").val(page_index);
            // init sort, etc
            init_sequence_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
    return false;
}

function init_sequence_content() {
    if ($("#searchdbseq").val()=='' && $("select#dbseqvendorselect").val()=='') {
        $("#dbitemsortable").sortable().on('sortupdate', function (e, ui) {
            var data = $("#sortitemsequence").serializeArray();
            data.push({name: 'offset', value: $("#seqpagenum").val()});
            $("#loader").show();
            // POST to server using $.post or $.ajax
            $.ajax({
                data: data,
                type: 'POST',
                url: '/database/itemsequence_sort'
            }).success(function () {
                var curpage = $("#seqpagenum").val();
                pageSequenceCallback(curpage);
                $("#loader").hide();
            });
        });
    };
    $("select#dbseqvendorselect").unbind('change').change(function(){
        if ($("select#dbseqvendorselect").val()=='') {
            $("select#dbseqvendorselect").removeClass('activevendor');
        } else {
            $("select#dbseqvendorselect").addClass('activevendor');
        }
        $(this).blur();
        search_sequence()
    });
    $("#searchdbseq").keypress(function(event){
        if (event.which == 13) {
            search_sequence();
        }
    });
    $("#dbseqclear_it").unbind('click').click(function() {
        $("#searchdbseq").val('');
        search_sequence();
    });
    $("#dbseqfind_it").unbind('click').click(function(){
        search_sequence();
    });
    $("#iteminrowselect").unbind('change').change(function(){
        var curpage=$("#seqpagenum").val();
        pageSequenceCallback(curpage);
    });
    $("input.salechkbox").unbind('change').change(function () {
        var item = $(this).data('item');
        var newval=1;
        if ($(this).prop('checked')==false) {
            newval=0;
        }
        update_item_prop(item, newval, 'item_sale');
    });
    $("input.newitemchkbox").unbind('change').change(function () {
        var item = $(this).data('item');
        var newval=1;
        if ($(this).prop('checked')==false) {
            newval=0;
        }
        update_item_prop(item, newval, 'item_new');
    });
    $("input.moveitemseq").unbind('change').change(function(){
        var item = $(this).data('item');
        var newval=$(this).val();
        update_item_sequence(item, newval);
    })
}

function search_sequence() {
    var params=new Array();
    params.push({name: 'search', value: $("#searchdbseq").val()});
    params.push({name: 'vendor_id', value: $("#dbseqvendorselect").val()});
    params.push({name: 'brand', value: $("#itemsequencebrand").val()});
    var url = '/database/itemsequence_search';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('#totalrecdbseq').val(response.data.total);
            $("#seqpagenum").val(0);
            initSequencePagination();
        } else {
            show_error(response);
        }
    },'json');
}

function update_item_prop(item, newval, prop) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'newval', value: newval});
    params.push({name: 'property', value: prop});
    var url = '/database/itemsequence_updateitem';
    $.post(url, params, function(response){
        if (response.errors=='') {
        } else {
            show_error(response);
        }
    },'json');
}

function update_item_sequence(item, newval) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'newval', value: newval});
    var url = '/database/itemsequence_updateseq';
    $("#dbitemloader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            var curpage=$("#seqpagenum").val();
            pageSequenceCallback(curpage);
            $("#dbitemloader").hide();
        } else {
            $("#dbitemloader").hide();
            show_error(response);
        }
    },'json');

}