function init_ordersviewdata() {
    initLeadOrderPagination();
    $('select.usrreplica').unbind('change').change(function(){
        search_leadorders();
    });
    $('select#leadorderperpage').unbind('change').change(function(){
        search_leadorders();
    });
    // $("div.lead_neworder").unbind('click').click(function(){
    //     var brand = $("input#ordersviewbrand").val();
    //     if (brand=='ALL') {
    //         show_brand_select();
    //     } else {
    //         add_leadorder(brand);
    //     }
    // });
    // Search
    $("input.leadord_searchdata").keypress(function(event){
        if (event.which == 13) {
            search_leadorders();
        }
    });
    $("div.leadorder_findall").unbind('click').click(function(){
        search_leadorders();
    });
    $("div.leadorder_clear").unbind('click').click(function(){
        $("input.leadord_searchdata").val('');
        search_leadorders();
    });
    // Change Brand
    $("#ordersviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#ordersviewbrand").val(brand);
        $("#ordersviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#ordersviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#ordersviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_leadorders();
    });
}

function initLeadOrderPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalleadorders').val();
    var perpage = $("select#leadorderperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.leadorder_pagination").empty();
        pageLeadorderCallback(0);
    } else {
        var curpage = $("#leadorderpage").val();
        // Create content inside pagination element
        $("div.leadorder_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeadorderCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeadorderCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value:$("select#leadorderperpage").val()});
    params.push({name:'maxval', value:$('#totalleadorders').val()});
    params.push({name:'offset', value: page_index});
    // params.push({name:'user_replic', value: $("select.usrreplica").val()});
    params.push({name:'search', value: $("input.leadord_searchdata").val()});
    params.push({name: 'brand', value: $("input#ordersviewbrand").val()});
    var url="/orders/leadorder_data";
    // $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('table.table-orders').find('tbody').empty().html(response.data.content);
            // Init new content manage
            init_leadorder_content();
            $("#leadorderpage").val(page_index);
            // $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_leadorder_content() {

}

function search_leadorders() {
    var params=new Array();
    // params.push({name:'user_replic', value: $("select.usrreplica").val()});
    params.push({name:'search', value: $("input.leadord_searchdata").val()});
    params.push({name: 'brand', value: $("input#ordersviewbrand").val()});
    var url="/orders/leadorder_count";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('#totalleadorders').val(response.data.total);
            $("#leadorderpage").val(0);
            initLeadOrderPagination();
        } else {
            show_error(response);
        }
    },'json');
}
