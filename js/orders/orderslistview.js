function init_leadorderlist() {
    leftmenu_alignment();
    initLeadOrdListPagination();
    $("select#leadordlistperpage").unbind('change').change(function(){
        initLeadOrdListPagination();
    });
    $("div.leadorderlist_save").unbind('click').click(function(){
        save_allordersqty();
    });
    // Search
    $("input.leadordlst_searchdata").keypress(function(event){
        if (event.which == 13) {
            leadordlst_searchdata();
        }
    });
    $("div.leadorderlst_findall").unbind('click').click(function(){
        leadordlst_searchdata();
    });
    $("div.leadorderlst_clear").unbind('click').click(function(){
        $("input.leadordlst_searchdata").val('');
        leadordlst_searchdata();
    });
    $("select.leadordlistselect").unbind('change').change(function(){
        leadordlst_searchdata();
    });
    // Change Brand
    $("#orderlistsviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#orderlistsviewbrand").val(brand);
        $("#orderlistsviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#orderlistsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#orderlistsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        leadordlst_searchdata();
    });
}

function initLeadOrdListPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalordlists').val();
    var perpage = $("#leadordlistperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#leadordlist_pagination").empty();
        pageLeadordlistCallback(0);
    } else {
        var curpage = $("#leadordlistpage").val();
        // Create content inside pagination element
        $("div#leadordlist_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeadordlistCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeadordlistCallback(pagenum) {
    var params=new Array();
    params.push({name:'limit',value:$("#leadordlistperpage").val()});
    params.push({name:'maxval', value:$('#totalordlists').val()});
    params.push({name:'offset', value: pagenum});
    params.push({name:'search', value: $("input.leadordlst_searchdata").val()});
    params.push({name:'order_qty', value:$("select.leadordlistselect").val()});
    params.push({name:'listdata', value: 1});
    params.push({name: 'brand', value: $("#orderlistsviewbrand").val()});
    var url="/orders/leadorder_data";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.leadordlist_dataarea").empty().html(response.data.content);
            // Init new content manage
            init_leadordlist_content();
            $("input#leadordlistpage").val(pagenum);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_leadordlist_content() {
    $("input.leadordlistorderqty").unbind('change').change(function(){
        var order=$(this).data('orderqty');
        $("div.leadorderform_save[data-order='"+order+"']").css('visibility','visible');
        $("div.leadorderlist_save").show();
    });
    $("div.leadorderform_save").unbind('click').click(function(){
        var order=$(this).data('order');
        save_orderqty(order);
    });
}

function save_orderqty(order) {
    var params=new Array();
    params.push({name: 'order_id', value: order});
    params.push({name: 'order_qty', value: parseInt($("input.leadordlistorderqty[data-orderqty='"+order+"']").val())});
    params.push({name: 'brand', value: $("#orderlistsviewbrand").val()});
    var url="/orders/leadorder_qtysave";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.leadorderform_save[data-order='"+order+"']").css('visibility','hidden');
            var numedt=0;
            $("div.leadordlist_datarow").each(function(){
                if (parseInt($(this).data('edit'))==1) {
                    numedt++;
                }
            });
            $("div.leadordlist_datatotal").empty().html(response.data.totals);
            if (numedt===0) {
                $("div.leadorderlist_save").hide();
            } else {
                $("div.leadorderlist_save").show();
            }
        } else {
            show_error(response);
        }
    },'json');
}

function save_allordersqty() {
    var saverow='';
    var order='';
    var newval=0;
    $("div.leadordlist_datarow").each(function(){
        order=$(this).data('order');
        if ($("div.leadorderform_save[data-order='"+order+"']").css('visibility')=='visible') {
            newval=$("input.leadordlistorderqty[data-orderqty='"+order+"']").val();
            saverow=saverow+order+'-'+newval+'|';
        }
    });
    if (saverow!='') {
        var url="/orders/leadorder_qtysaveall";
        var params = new Array();
        params.push({name:'brand', value: $("#orderlistsviewbrand").val()});
        params.push({name:'saverow', value: saverow});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.leadorderlist_save").hide();
                var numpage=$("input#leadordlistpage").val();
                $("div.leadordlist_datatotal").empty().html(response.data.totals);
                pageLeadordlistCallback(numpage);
            } else {
                show_error(response);
            }
        },'json');
    }
}

function leadordlst_searchdata() {
    var params=new Array();
    params.push({name:'search', value: $("input.leadordlst_searchdata").val()});
    params.push({name:'order_qty', value: $("select.leadordlistselect").val()});
    params.push({name:'brand', value: $("#orderlistsviewbrand").val()});
    var url="/orders/leadorder_count";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('#totalordlists').val(response.data.total);
            if (isNaN(response.data.show_totals)==false) {
                $("div.leadordlist_datatotal").empty().html(response.data.total_view);
            }
            initLeadOrdListPagination();
        } else {
            show_error(response);
        }
    },'json');
}