function init_orders() {
    $("select#artorder_options").unbind('change').change(function(){
        search_artorderdata();
    })
    $("select#artordfilter_options").unbind('change').change(function(){
        search_artorderdata();
    })

    $("#artordsearch").keypress(function(event){
        if (event.which == 13) {
            search_artorderdata();
        }
    });

    $("#artordclear_ord").unbind('click').click(function(){
        $("#artordsearch").val('');
        $("#artorder_options").val('');
        $("#artordfilter_options").val('');
        search_artorderdata();
    });

    $("#artordfind_ord").unbind('click').click(function(){
        search_artorderdata();
    });

    $("div.addneworder").unbind('click').click(function(){
        order_artstage(0,'artorderlist');
    });
    // Change Brand
    $("#artordersviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#artordersviewbrand").val(brand);
        $("#artordersviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#artordersviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#artordersviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_artorderdata();
    });
    initGeneralPagination();
}

/* Init pagination */
function initGeneralPagination() {
    // count entries inside the hidden content
    var num_entries=$('#artordtotalrec').val();
    var perpage = $("#artordperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".artOrderPagination").empty();
        pageArtOrderCallback(0);
    } else {
        var curpage=$("#artordcurpage").val();
        // Create content inside pagination element
        $(".artOrderPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageArtOrderCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageArtOrderCallback(page_index){
    // var perpage = itemsperpage;
    var search=$("input#artordsearch").val();
    var params=new Array();
    params.push({name:'search', value:search});
    params.push({name:'offset', value:page_index})
    params.push({name:'filter', value: $("select#artordfilter_options").val()});
    params.push({name:'add_filtr', value: $("select#artorder_options").val()});
    params.push({name:'limit', value:$("#artordperpage").val()});
    params.push({name:'order_by', value:$("#artordorderby").val()});
    params.push({name:'direction', value:$("#artorddirection").val()});
    params.push({name:'maxval', value:$('#artordtotalrec').val()});
    params.push({name: 'brand', value: $("input#artordersviewbrand").val()});

    var url='/art/order_data';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('div.tabledata').empty().html(response.data.content);
            $("#artordcurpage").val(page_index);
            artorders_view_init();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function artorders_view_init() {
    $("div.artnoteshow").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom right',
            at: 'top left',
        },
        style: 'qtip-light'
    });
    $("div.ordercode").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom right',
            at: 'top left',
        },
        style: 'qtip-light'
    });
    $('div.artlastmessageview').qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('messageview') // Use href attribute as URL
                }).then(function(content) {
                        // Set the tooltip content upon successful retrieval
                        api.set('content.text', content);
                    }, function(xhr, status, error) {
                        // Upon failure... set the tooltip content to error
                        api.set('content.text', status + ': ' + error);
                    });
                return 'Loading...'; // Set some initial text
            }
        },
        style: 'art_lastmessage'
    });
    $("div.ordernum").unbind('click').click(function(){
        var order_id=$(this).data('orderid');
        order_artstage(order_id,'artorderlist');
    })
    var rowid='';
    $("div.rowdata").hover(
        function(){
            rowid=$(this).data('orderid');
            $("div.rowdata[data-orderid="+rowid+"]").addClass("current_row");
        },
        function(){
            rowid=$(this).data('orderid');
            $("div.rowdata[data-orderid="+rowid+"]").removeClass("current_row");
        }
    );
}

/* Search functions */
function search_artorderdata() {
    var params = new Array();
    params.push({name: 'search', value: $("input#artordsearch").val()});
    params.push({name: 'filter', value: $("select#artordfilter_options").val()});
    params.push({name: 'add_filtr', value: $("select#artorder_options").val()});
    params.push({name: 'brand', value: $("input#artordersviewbrand").val()});
    /* Recalculate total number */
    var url="/art/search_orders";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artordtotalrec").val(response.data.totals);
            initGeneralPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}
