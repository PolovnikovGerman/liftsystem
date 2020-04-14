function init_orderview() {
    initOnlinePagination();
    $("#find_onlines").click(function(){
        search_onlineorderdata();
    })
    $("#clear_it").click(function(){
        $("#online_replica").val('');
        $("#online_confirm").val('');
        $("#online_customer").val('');
        search_onlineorderdata();
    });
    $("#online_replica").keypress(function(event){
        if (event.which == 13) {
            var search=$("#online_replica").val();
            if (search!='') {
                search_onlineorderdata();
            }
        }
    });
    $("#online_confirm").keypress(function(event){
        if (event.which == 13) {
            var search=$("#online_confirm").val();
            if (search!='') {
                search_onlineorderdata();
            }
        }
    });
    $("#online_customer").keypress(function(event){
        if (event.which == 13) {
            var search=$("#online_customer").val();
            if (search!='') {
                search_onlineorderdata();
            }
        }
    });
    // Change Brand
    $("#onlineordersbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#onlineordersbrand").val(brand);
        $("#onlineordersbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#onlineordersbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#onlineordersbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_onlineorderdata();
    });
}

/* Init pagination */
function initOnlinePagination() {
    // count entries inside the hidden content
    var num_entries = parseInt($('#online_totalrec').val());
    var perpage = parseInt($("input#online_perpage").val());
    var curpage = $("#online_curpage").val();
    if (num_entries < perpage) {
        $("#onlinePagination").empty();
        onlinepagesCallback(0);
    } else {
        // Create content inside pagination element
        $("#onlinePagination").mypagination(num_entries, {
            current_page: curpage,
            callback: onlinepagesCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 7,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function onlinepagesCallback(page_index, jq){
    var dat=new Array();
    dat.push({name:'offset', value :page_index});
    dat.push({name:'limit', value: $("input#online_perpage").val()});
    dat.push({name:'order_by', value: $("#online_orderby").val()});
    dat.push({name:'direction', value: $("#online_direction").val()});
    dat.push({name:'replica', value: $("#online_replica").val()});
    dat.push({name:'confirm', value: $("#online_confirm").val()});
    dat.push({name:'customer', value: $("#online_customer").val()});
    dat.push({name: 'brand', value: $("#onlineordersbrand").val()});
    $("#loader").css('display','block');
    $.post('/orders/onlineorderdata',dat,function(response) {
        if (response.errors=='') {
            $("#loader").css('display','none');
            $('#onlinetabinfo').empty().append(response.data.content);
            $("#online_curpage").val(page_index);
            $('div.orderdetailslnk').unbind('click').click(function(){
                var order = $(this).data('order')
                showorderdetails(order);
            })
        } else {
            show_error(response);
        }
    },'json');
    return false;
}


function showorderdetails(objid) {
    /* Get Email Details */
    var url='/orders/online_details';
    $.post(url, {'order_id':objid}, function(response){
        if (response.errors=='') {
            /* Fill data into Dialog */
            // show_popup('order_details_dialog');
            $("#pageModal").find('div.modal-dialog').css('width','1025px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});

            $("a.uplattach").click(function(){
                var linkHref = $(this).attr('href');
                $.fileDownload('/orders/upload_attach', {httpMethod : "POST", data: {url : linkHref}});
                return false; //this is critical to stop the click event which will trigger a normal file download!
            });
            $("div.orderdat_button-save").click(function(){
                save_orderdetails();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function close_orderdetails() {
    $("#popupContactClose").show();
    disablePopup();
}

function save_orderdetails() {
    if (confirm('Are you sure ?')) {
        var enterclass = $("#order_rep").attr('class');
        if (enterclass == 'blur') {
            $("#order_rep").val('');
        }
        enterclass = $("#order_num").attr('class');
        if (enterclass == 'blur') {
            $("#order_num").val('');
        }
        /* Get Form Fields */
        var postdat = $("#order-details").serializeArray();
        var url = "/orders/saveorderdetails";
        $.post(url, postdat, function(response) {
            if (response.errors=='') {
                /* All OK - close popup, rebuild data */
                $("#popupContactClose").show();
                disablePopup();
                if (response.data.infomsg!='') {
                    $.flash(response.data.infomsg, {timeout:5000});
                }
                $("#newmails").val(response.data.new_orders);
                if (response.data.new_orders== '0') {
                    $("#imgorders").attr('src', '/img/orders-grey.png');
                } else {
                    $("#imgorders").attr('src', '/img/m-orders-orange.gif');
                }
                initPagination();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}

function search_onlineorderdata() {
    var params = new Array();
    params.push({name: 'brand', value: $("#onlineordersbrand").val()});
    params.push({name: 'replica', value: $("#online_replica").val()});
    params.push({name: 'confirm', value: $("#online_confirm").val()});
    params.push({name: 'customer', value: $("#online_customer").val()});
    var url="/orders/onlinesearch";
    $("#loader").show();
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("#online_totalrec").val(response.data.total_rec);
            $("#curpage").val(0);
            initOnlinePagination();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function clean_filter() {
}
