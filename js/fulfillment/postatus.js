function init_statuspage() {
    initStatusPagination();
    $("#clear_status").unbind('click').click(function(){
        $("input#statussearch").val('');
        search_statusdata();
    });
    $("#find_status").unbind('click').click(function(){
        search_statusdata();
    });
    $("select#status_datselect").unbind('change').change(function(){
        search_statusdata();
    })
    $("select#status_options").unbind('change').change(function(){
        search_statusdata();
    })
    $("select#status_orderselect").unbind('change').change(function(){
        search_statusdata();
    })
    $("input#statussearch").keypress(function(event){
        if (event.which == 13) {
            search_statusdata();
        }
    });
    // Change Brand
    $("#postatusviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#postatusviewbrand").val(brand);
        $("#postatusviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#postatusviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#postatusviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_statusdata();
    });

}

function initStatusPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalproj').val();
    var perpage = $("#perpageproj").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#statusview .Pagination").empty();
        pageStatusCallback(0);
    } else {
        var curpage = $("#curpageproj").val();
        // Create content inside pagination element
        $("div#statusview .Pagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageStatusCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageStatusCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value:$("#perpageproj").val()});
    params.push({name:'order_by', value:$("#orderbyproj").val()});
    params.push({name:'direction', value:$("#directionproj").val()});
    params.push({name:'maxval', value:$('#totalproj').val()});
    params.push({name:'addsort', value:$("select#status_orderselect").val()});
    params.push({name:'search', value: $("input#statussearch").val()});
    params.push({name:'date_filter', value:$("select#status_datselect").val()});
    params.push({name:'options_filter', value:$("select#status_options").val()});
    params.push({name: 'brand', value: $("#postatusviewbrand").val()});

    $("#loader").show();
    var url='/fulfillment/statusdata';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").css('display','none');
            $("div#statustableinfo").empty().html(response.data.content);
            // init_fulfillmentview();
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
}

function search_statusdata() {
    var params=new Array();
    params.push({name:'search', value: $("input#statussearch").val()});
    params.push({name:'date_filter', value:$("select#status_datselect").val()});
    params.push({name:'options_filter', value:$("select#status_options").val()});
    params.push({name: 'brand', value: $("#postatusviewbrand").val()});
    var url='/fulfillment/statussearchdata';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#totalproj").val(response.data.totalrec);
            $("input#curpageproj").val(0);
            initStatusPagination();
        } else {
            show_error(response);
        }
    },'json');
}
