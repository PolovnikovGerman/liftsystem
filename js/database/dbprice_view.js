//var itemsperpage=425;
var itemsperpage=200;
function init_dbprice_view() {
    initDBPricePagination();
    $("#itemnum").unbind('click').click(function(){
        price_sort('item_number','itemnum');
    });
    $("#itemname").unbind('click').click(function(){
        price_sort('item_name','itemname');
    });
    $("#price25").unbind('click').click(function(){
        price_sort('item_price_25','price25')
    });
    $("#price50").unbind('click').click(function(){
        price_sort('item_price_50','price50')
    });
    $("#price150").unbind('click').click(function(){
        price_sort('item_price_150','price150')
    });
    $("#price250").unbind('click').click(function(){
        price_sort('item_price_250','price250')
    });
    $("#price500").unbind('click').click(function(){
        price_sort('item_price_500','price500')
    });
    $("#price1000").unbind('click').click(function(){
        price_sort('item_price_1000','price1000')
    });
    $("#price2500").unbind('click').click(function(){
        price_sort('item_price_2500','price2500')
    });
    $("#price5000").unbind('click').click(function(){
        price_sort('item_price_5000','price5000')
    });
    $("#price10000").unbind('click').click(function(){
        price_sort('item_price_10000','price10000')
    });
    $("#price20000").unbind('click').click(function(){
        price_sort('item_price_20000','price20000')
    });
    $("#price_setup").unbind('click').click(function(){
        price_sort('item_price_setup','price_setup')
    });
    $("#sortupdatetim").unbind('change').change(function(){
        var datesort=$(this).val();
        if (datesort=='') {
            price_sort('item_number','itemnum');
        } else {
            $(".table-price").find('.gradient2').removeClass('gradient2').addClass('gradient1');
            var pagenum=$("#curpage").val();
            $("#orderby").val('update_time');
            if (datesort=='upd-desc') {
                $("#direction").val('desc');
            } else {
                $("#direction").val('asc');
            }
            pageselectCallback(pagenum);
        }
    });
    $("select#compareprefs").unbind('change').change(function(){
        var vals=$(this).val();
        switch(vals) {
            case '':
                $(this).removeClass('red').removeClass('orange').removeClass('pink').removeClass('white');
                break;
            case 'red':
                $(this).removeClass('orange').removeClass('pink').removeClass('white').addClass('red');
                break;
            case 'pink':
                $(this).removeClass('red').removeClass('orange').removeClass('white').addClass('pink');
                break;
            case 'white':
                $(this).removeClass('red').removeClass('orange').removeClass('pink').addClass('white');
                break;
        }
        initPagination();
    })
    $("select#vendorselect").unbind('change').change(function(){
        search_data();
    })
    $("input.pricecomparechk").unbind('change').change(function(){
        search_data();
    })
    $("#find_it").unbind('click').click(function(){
        var search=$("#searchtemplate").val();
        if (search!='Enter keyword or item #' && search!='') {
            search_data();
        }
    });
    $("#searchtemplate").keypress(function(event){
        if (event.which == 13) {
            var search=$("#searchtemplate").val();
            if (search!='Enter keyword or item #' && search!='') {
                search_data();
            }
        }
    });
    $("#clear_it").unbind('click').click(function(){
        clear_search();
    });
}

function initDBPricePagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrecdbprice').val();
    var perpage = $("#perpagedbprice").val();
    var curpage = $("#curpagedbprice").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#dbpricePagination").empty();
        $("input#curpagedbprice").val(0);
        pageDbpriceselectCallback(0);
    } else {
        // Create content inside pagination element
        $("#dbpricePagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageDbpriceselectCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function prepare_dbpriceparams() {
    var params=new Array();
    params.push({name:'offset', value: $("#curpagedbprice").val()});
    params.push({name:'limit',value: $("#perpagedbprice").val()});
    params.push({name:'order_by', value: $("#orderbydbprice").val()});
    params.push({name:'direction',value: $("#directiondbprice").val()});
    params.push({name:'search', value:$.trim($("#searchdbprice").val())});
    params.push({name:'compareprefs', value:$("#compareprefs").val()});
    params.push({name:'vendor_id', value:$("select#vendorselect").val()});
    $("input.pricecomparechk").each(function(){
        var dat=$(this).data('othvendor');
        var name='otherved'+dat;
        var chk=$(this).prop('checked');
        if (chk==true) {
            params.push({name:name, value:1});
        } else {
            params.push({name:name, value:0});
        }
    })
    return params;
}

function pageDbpriceselectCallback(page_index){
    var params=prepare_dbpriceparams();
    var url='/database/pricedat';
    $("#loader").css('display','block');
    $.post(url,params,function(response){
        if (response.errors=='') {
            $('#dbpricetabinfo').empty().html(response.data.content);
            var infoh=parseInt($('div#dbpricetabinfo').css('height'));
            if (infoh<541) {
                $(".tabinfo").css('overflow','hidden');
                $(".table-row").css('width','996px');
                $('div#tabinfo .last_col').css('width','72px');
            } else {
                $(".tabinfo").css('overflow-y','auto');
            }
            $("#curpagedbprice").val(page_index);
            $('#dbpricetabinfo').find("div.table-row:last").addClass('last_row');
            $("#loader").css('display','none');
            // profit_init();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function profit_init() {
    $("a.greenprof").bt({
        fill : '#86FF80',
        cornerRadius: 10,
        width: 90,
        padding: 10,
        strokeWidth: '2',
        positions: "top",
        strokeStyle : '#FFFFFF',
        strokeHeight: '18',
        cssClass: 'green_tooltip',
        cssStyles: {color: '#FFFFF'}
    });
    $("a.whiteprof").bt({
        fill : '#FFFFFF',
        cornerRadius: 10,
        width: 90,
        padding: 10,
        strokeWidth: '2',
        positions: "top",
        strokeStyle : '#000000',
        strokeHeight: '18',
        cssClass: 'white_tooltip',
        cssStyles: {color: '#000000'}
    })
    $("a.redprof").bt({
        fill : '#FF0000',
        cornerRadius: 10,
        width: 90,
        padding: 10,
        strokeWidth: '2',
        positions: "top",
        strokeStyle : '#000000',
        strokeHeight: '18',
        cssClass: 'red_tooltip',
        cssStyles: {color: '#000000'}
    })
    $("a.orangeprof").bt({
        fill : '#FFA500',
        cornerRadius: 10,
        width: 90,
        padding: 10,
        strokeWidth: '2',
        positions: "top",
        strokeStyle : '#000000',
        strokeHeight: '18',
        cssClass: 'orange_tooltip',
        cssStyles: {color: '#000000'}
    })
    $("a.blackprof").bt({
        fill : '#000000',
        cornerRadius: 10,
        width: 90,
        padding: 10,
        strokeWidth: '2',
        positions: "top",
        strokeStyle : '#FFFFFF',
        strokeHeight: '18',
        cssClass: 'black_tooltip',
        cssStyles: {color: '#FFFFFF'}
    });
    $("a.maroonprof").bt({
        fill : '#800000',
        cornerRadius: 10,
        width: 90,
        padding: 10,
        strokeWidth: '2',
        positions: "top",
        strokeStyle : '#FFFFFF',
        strokeHeight: '18',
        cssClass: 'maroon_tooltip',
        cssStyles: {color: '#FFFFFF'}
    })

}
/**
 * Initialisation function for pagination
 */
/* Sort function */
function price_sort(colsort,colid) {
    var perpage = itemsperpage;
    var cursort = $("#orderby").val();
    var direction = $("#direction").val();
    var curpage = $("#curpage").val();
    var search = $.trim($("#search").val());
    $(".table-price").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $("#"+colid).removeClass('gradient1').addClass('gradient2');
    if (colsort==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    $("#orderby").val(colsort);
    $("#direction").val(direction);
    $("select#sortupdatetim").val('');
    initPagination();

//    $("#dbitemloader").css('display','block');
//    $.post('/dbitemview/pricedat',{'offset':curpage,'limit':perpage,'order_by':colsort,'direction':direction,'search':search},function(data){
//        $('#tabinfo').empty().append(data.content);
//        var infoh=parseInt($('div#tabinfo').css('height').replace('px',''));
//        if (infoh<541) {
//            $(".tabinfo").css('overflow','hidden');
//            $(".table-row").css('width','996px');
//            $('div#tabinfo .last_col').css('width','72px');
//        } else {
//            $(".tabinfo").css('overflow-y','auto');
//        }
//        $('#tabinfo').find("div.table-row:last").addClass('last_row');
//        $("#dbitemloader").css('display','none');
//        /* class for profit */
//        profit_init();
//    },'json');
}

function search_data() {
    var search=$("#searchtemplate").val();
    if (search=='Enter keyword or item #') {
        search='';
    }
    var vend=$("select#vendorselect").val();
    $.post('/dbitemview/searchcount', {'search':search, 'vendor_id':vend}, function(response){
        if (response.data.result==0) {
            alert('No search result');
            $("#searchtemplate").val('');
            $("select#vendorselect").val('');
        } else {
            $("#search").val(search);
            $("#curpage").val(0);
            $("#totalrec").val(response.data.result);
            initPagination();
        }
    }, 'json');
}

function clear_search() {
    $("#searchtemplate").val('Enter keyword or item #');
    $("#search").val('');
    $("select#vendorselect").val('');
    $.post('/dbitemview/searchcount', {'search':''}, function(response){
        $("#curpage").val(0);
        $("#totalrec").val(response.data.result);
        initPagination();
    },'json');

}