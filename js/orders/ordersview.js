var timerId;
var timeout=10000;
var timeoutlock=60000;
function init_ordersviewdata() {
    initLeadOrderPagination();
    $('select.usrreplica').unbind('change').change(function(){
        search_leadorders();
    });
    $('select#leadorderperpage').unbind('change').change(function(){
        search_leadorders();
    });
    $("div.lead_neworder").unbind('click').click(function(){
        var brand = $("input#ordersviewbrand").val();
        if (brand=='ALL') {
            show_brand_select();
        } else {
            add_leadorder(brand);
        }
    });
    // $("div.lead_neworder").unbind('click').click(function(){
    //     edit_leadorder(0);
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
    var perpage = $("#leadorderperpage").val();
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
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeadorderCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value:$("#leadorderperpage").val()});
    params.push({name:'maxval', value:$('#totalleadorders').val()});
    params.push({name:'offset', value: page_index});
    params.push({name:'user_replic', value: $("select.usrreplica").val()});
    params.push({name:'search', value: $("input.leadord_searchdata").val()});
    params.push({name: 'brand', value: $("input#ordersviewbrand").val()});
    var url="/orders/leadorder_data";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.leadorder_dataarea").empty().html(response.data.content);
            // Init new content manage
            init_leadorder_content();
            $("#leadorderpage").val(page_index);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

// Init Data Content
function init_leadorder_content() {
    // Check Active Search
    var activesearch=$("input#leadorderactivate").val();
    if (parseInt(activesearch)>0) {
        $("input#leadorderactivate").val('');
        edit_leadorder(activesearch);
    }
    // $("select.selectreplic").unbind('change').change(function(){
    //     var repl=$(this).val();
    //     var order=$(this).data('order');
    //     var url="/orders/leadorder_apply";
    //     $.post(url,{'replic': repl,'order_id':order}, function(response){
    //         if (response.errors!='') {
    //             show_error(response);
    //         }
    //     },'json');
    // });
    // Edit Order
    $("div.ordernum").unbind('click').click(function(){
        var order=$(this).parent('div').data('order');
        edit_leadorder(order);
    });
    // View full item Color
    $("div.itemcolor.wide").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'center right',
            at: 'center left',
        },
        style: 'itemcolor_tooltip'
    })
}


// Search Order
function search_leadorders() {
    var params=new Array();
    params.push({name:'user_replic', value: $("select.usrreplica").val()});
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

// Edit Order
function edit_leadorder(order) {
    var callpage = 'orderslist';
    var brand = $("input#ordersviewbrand").val();
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            // $("#artModal").modal('show');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true})
            if (parseInt(order)==0) {
                init_onlineleadorder_edit();
                init_rushpast();
            } else {
                navigation_init();
            }
        } else {
            show_error(response);
        }
    },'json');
}

// Ask system before create new order
function show_brand_select() {
    var url = '/orders/order_brand';
    $.post(url,{}, function (response) {
        if (response.errors=='') {
            $("#pageModalLabel").empty().html('Choose Brand for New Order');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','380px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("button#savebrand").unbind('click').click(function () {
                var brand = $("#neworderbrand").val();
                $("#pageModal").modal('hide');
                add_leadorder(brand);
            })
        } else {
            show_error(response);
        }
    },'json');
}

function add_leadorder(brand) {
    var callpage = 'orderslist';
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: 0});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 1});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_onlineleadorder_edit();
            init_rushpast();
        } else {
            show_error(response);
        }
    },'json');
}