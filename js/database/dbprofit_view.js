// var itemsperpage=425;
//var itemsperpage=200;
var maxheight=530;
function init_profit_view() {
    initDbProfitPagination();
    $("#itemnum").unbind('click').click(function(){
        profit_sort('item_number','itemnum');
    });
    $("#itemname").unbind('click').click(function(){
        profit_sort('item_name','itemname');
    });
    $("#vendorcost").unbind('click').click(function(){
        profit_sort('vendor_item_cost','vendorcost');
    })
    $("#vendorname").unbind('click').click(function(){
        profit_sort('vendor_name','vendorname');
    })
    $("#dbprofitfind_it").unbind('click').click(function(){
        search_data();
    });
    $("#searchdbprofit").keypress(function(event){
        if (event.which == 13) {
            search_data();
        }
    });
    $("#dbprofitclear_it").unbind('click').click(function(){
        clear_search();
    })
    $("#dbprofitprofitprefs").unbind('change').change(function(){
        initDbProfitPagination();
    });
    $("select#dbprofitvendorselect").unbind('change').change(function(){
        search_data();
    })
};

/**
 * Initialisation function for pagination
 */
function initDbProfitPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrecdbprofit').val();
    var perpage = $("#perpagedbprofit").val();
    var curpage=$("#curpagedbprofit").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#dbprofitPagination").empty();
        $("input#curpagedbprofit").val(0);
        pageDbProfitCallback(0);
    } else {
        // Create content inside pagination element
        $("#dbprofitPagination").mypagination(num_entries, {
            callback: pageDbProfitCallback,
            current_page: curpage,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageDbProfitCallback(page_index){
    var params=new Array();
    params.push({name:'offset', value:page_index});
    params.push({name:'limit', value: $("#perpagedbprofit").val()});
    params.push({name:'order_by', value:$("#orderbydbprofit").val()});
    params.push({name:'direction', value:$("#directiondbprofit").val()});
    params.push({name:'search', value:$("#searchdbprofit").val()});
    params.push({name:'profitprefs', value:$("#dbprofitprofitprefs").val()});
    params.push({name:'vendor_id',value:$("select#dbprofitvendorselect").val()});
    var url="/database/profitdat";
    $("#curpage").val(page_index);
    $("#loader").css('display','block');
    $.post(url,params,function(response){
        $("#loader").css('display','none');
        if (response.errors=='') {
            $('#dbprofittabinfo').empty().append(response.data.content);
            var infoh=parseInt($('div#dbprofittabinfo').css('height'));
            if (infoh<parseInt(maxheight)) {
                $("div#dbprofittabinfo").css('overflow','hidden');
                $("div#dbprofittabinfo tr").css('width','996px');
                $('div#dbprofittabinfo .last_col').css('width','52px');
            } else {
                $("div#dbprofittabinfo").css('overflow-y','auto');
            }
            $('#dbprofittabinfo').find("tr:last").find('td').addClass('last_row');
        } else {
            show_errors(response);
        }
    },'json');
    return false;
}

function profit_sort(colsort,itemsort) {
    var cursort = $("#orderbydbprofit").val();
    var direction = $("#directiondbprofit").val();

    if (colsort==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    $(".profit_head").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $("#"+itemsort).removeClass('gradient1').addClass('gradient2');
    $("#orderbydbprofit").val(colsort);
    $("#directiondbprofit").val(direction);
    initDbProfitPagination();
}

function search_data() {
    var search=$("#searchdbprofit").val();
    var vend=$("select#dbprofitvendorselect").val();
    $.post('/database/searchcount', {'search':search, 'vendor_id':vend}, function(response){
        if (response.errors=='') {
            $("#curpagedbprofit").val(0);
            $("#totalrecdbprofit").val(response.data.result);
            initDbProfitPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}
function clear_search() {
    $("#searchdbprofit").val('');
    $("select#dbprofitvendorselect").val('');
    $.post('/database/searchcount', {'search':''}, function(response){
        if (response.errors=='') {
            $("#curpagedbprofit").val(0);
            $("#totalrecdbprofit").val(response.data.result);
            initDbProfitPagination();
        } else {
            show_error(response);
        }
    },'json');

}
