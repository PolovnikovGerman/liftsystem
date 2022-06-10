// var itemsperpage=425;
//var itemsperpage=200;
var maxheight=530;
function init_profit_view() {
    initDbProfitPagination();
    $(".profit_head").find('.cellsort').unbind('click').click(function(){
        var fld = $(this).data('sortcell');
        sort_profitdata(fld);
    });
    $("#dbprofitfind_it").unbind('click').click(function(){
        search_profitdata();
    });
    $("#searchdbprofit").keypress(function(event){
        if (event.which == 13) {
            search_profitdata();
        }
    });
    $("#dbprofitclear_it").unbind('click').click(function(){
        clear_profitsearch();
    })
    $("#dbprofitprofitprefs").unbind('change').change(function(){
        initDbProfitPagination();
    });
    $("select#dbprofitvendorselect").unbind('change').change(function(){
        search_profitdata();
    });
    // Change Brand
    // $("#itemprofitbrandmenu").find("div.left_tab").unbind('click').click(function(){
    //     var brand = $(this).data('brand');
    //     $("#itemprofitbrand").val(brand);
    //     $("#itemprofitbrandmenu").find("div.left_tab").removeClass('active');
    //     $("#itemprofitbrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
    //     search_profitdata();
    // });
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
    params.push({name: 'brand', value: $("#itemprofitbrand").val()});
    var url="/database/profitdat";
    $("#curpage").val(page_index);
    $("#loader").css('display','block');
    $.post(url, params, function(response){
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
            init_profitcontent_view();
        } else {
            show_errors(response);
        }
    },'json');
    return false;
}

function init_profitcontent_view() {
    $(".profittablerow").find('.itemtitle').popover({
        placement: 'right',
        trigger: 'hover',
        html: true,
    });
    $('.profittablerow').find(".editcoll").unbind('click').click(function(){
        var item_id=$(this).data('item');
        var brand = $("#itempricebrand").val();
        view_itemdetails(item_id, brand);
    })
}

function sort_profitdata(fld) {
    var cursort = $("#orderbydbprofit").val();
    var direction = $("#directiondbprofit").val();

    if (fld==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    $(".profit_head").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $(".profit_head").find(".cellsort[data-sortcell='"+fld+"']").removeClass('gradient1').addClass('gradient2');
    $("#orderbydbprofit").val(fld);
    $("#directiondbprofit").val(direction);
    initDbProfitPagination();
}

function search_profitdata() {
    var url = '/database/searchcount';
    var params = new Array();
    params.push({name: 'search', value: $("#searchdbprofit").val()});
    params.push({name: 'vendor_id', value: $("select#dbprofitvendorselect").val()});
    params.push({name: 'brand', value: $("#itemprofitbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#curpagedbprofit").val(0);
            $("#totalrecdbprofit").val(response.data.result);
            initDbProfitPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}
function clear_profitsearch() {
    $("#searchdbprofit").val('');
    $("select#dbprofitvendorselect").val('');
    var url = '/database/searchcount';
    var params = new Array();
    params.push({name: 'search', value: ''});
    params.push({name: 'vendor_id', value: ''});
    params.push({name: 'brand', value: $("#itemprofitbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#curpagedbprofit").val(0);
            $("#totalrecdbprofit").val(response.data.result);
            initDbProfitPagination();
        } else {
            show_error(response);
        }
    },'json');

}
