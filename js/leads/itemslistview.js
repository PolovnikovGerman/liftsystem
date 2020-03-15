function init_leaditems() {
    initLeadItemsPagination();
    // Search
    $("input.leaditem_searchdata").keypress(function(event){
        if (event.which == 13) {
            search_leaditems();
        }
    });
    $("div.leaditem_find").unbind('click').click(function(){
        search_leaditems();
    });
    $("div.leaditem_clear").unbind('click').click(function(){
        $("input.leaditem_searchdata").val('');
        search_leaditems();
    });

    $('select.leaditem_filterselect').unbind('change').change(function(){
        search_leaditems();
    });
    // Change Brand
    $("#itemslistbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#itemslistbrand").val(brand);
        $("#itemslistbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#itemslistbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#itemslistbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_leaditems();
    });
}

function initLeadItemsPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalleaditems').val();
    var perpage = $("#leaditemperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.leaditem_pagination").empty();
        pageLeaditemCallback(0);
    } else {
        var curpage = $("#leaditempage").val();
        // Create content inside pagination element
        $("div.leaditem_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeaditemCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeaditemCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value:$("#leaditemperpage").val()});
    params.push({name:'maxval', value:$('#totalleaditems').val()});
    params.push({name:'offset', value: page_index});
    params.push({name:'vendor_id', value: $("select.leaditemsvendor").val()});
    params.push({name:'search', value: $("input.leaditem_searchdata").val()});
    params.push({name:'priority', value: $("select.leaditemspriority").val()});
    params.push({name: 'brand', value: $("#itemslistbrand").val()});
    var url="/leads/itemslist_data";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.leaditems_dataarea").empty().html(response.data.content);
            // Init new content manage
            init_leaditems_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_leaditems_content() {

}

function search_leaditems() {
    var params=new Array();
    params.push({name:'vendor_id', value: $("select.leaditemsvendor").val()});
    params.push({name:'search', value: $("input.leaditem_searchdata").val()});
    params.push({name: 'brand', value: $("#itemslistbrand").val()});
    var url="/leads/leaditems_count";
    $.post(url,params, function(response){
        if (response.errors=='') {
            $('#totalleaditems').val(response.data.total);
            initLeadItemsPagination();
        } else {
            show_error(response);
        }
    },'json');
}