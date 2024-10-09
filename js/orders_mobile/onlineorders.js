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
}

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
            num_display_entries : 3,
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
    // $("#loader").css('display','block');
    $.post('/orders/onlineorderdata',dat,function(response) {
        if (response.errors=='') {
            // $("#loader").css('display','none');
            $('.table-onlineorders').find('tbody').empty().html(response.data.content);
            $("#online_curpage").val(page_index);
            // $('div.orderdetailslnk').unbind('click').click(function(){
            //     var order = $(this).data('order')
            //     showorderdetails(order);
            // })
        } else {
            show_error(response);
        }
    },'json');
    return false;
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
