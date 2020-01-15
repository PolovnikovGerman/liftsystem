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
            var pagenum=$("#curpagedbprice").val();
            $("#orderby").val('update_time');
            if (datesort=='upd-desc') {
                $("#direction").val('desc');
            } else {
                $("#direction").val('asc');
            }
            pageDbpriceselectCallback(pagenum);
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
        initDBPricePagination();
    })
    $("select#vendorselect").unbind('change').change(function(){
        search_data();
    })
    $("input.pricecomparechk").unbind('change').change(function(){
        search_data();
    })
    $("#dbpricefind_it").unbind('click').click(function(){
        search_data();
    });
    $("#searchdbprice").keypress(function(event){
        if (event.which == 13) {
            search_data();
        }
    });
    $("#dbpriceclear_it").unbind('click').click(function(){
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
            profit_init();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function profit_init() {
    var greenpopover_template='<div class="popover green_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
    var whitepopover_template='<div class="popover white_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
    var redpopover_template='<div class="popover red_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
    var orangepopover_template='<div class="popover orange_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
    var blackpopover_template='<div class="popover black_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
    var maroonpopover_template='<div class="popover maroon_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';

    $("a.greenprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: greenpopover_template,
    });
    $("a.whiteprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: whitepopover_template,
    });
    $("a.redprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: redpopover_template,
    });
    $("a.orangeprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: orangepopover_template,
    });
    $("a.blackprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: blackpopover_template,
    });
    $("a.maroonprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: maroonpopover_template,
    });
    $(".itemtitle").popover({
        placement: 'right',
        trigger: 'hover',
        html: true,
    })
}
/**
 * Initialisation function for pagination
 */
/* Sort function */
function price_sort(colsort,colid) {
    var cursort = $("#orderbydbprice").val();
    var direction = $("#directiondbprice").val();
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
    $("#orderbydbprice").val(colsort);
    $("#directiondbprice").val(direction);
    $("select#sortupdatetim").val('');
    initDBPricePagination();
}

function search_data() {
    var search=$("#searchdbprice").val();
    var vend=$("select#vendorselect").val();
    $.post('/database/searchcount', {'search':search, 'vendor_id':vend}, function(response){
        if (response.data.result==0) {
            alert('No search result');
            $("#searchdbprice").val('');
            $("select#vendorselect").val('');
        } else {
            $("#searchdbprice").val(search);
            $("#curpagedbprice").val(0);
            $("#totalrecdbprice").val(response.data.result);
            initDBPricePagination();
        }
    }, 'json');
}

function clear_search() {
    $("#searchdbprice").val('');
    // $("#search").val('');
    $("select#vendorselect").val('');
    $.post('/database/searchcount', {'search':''}, function(response){
        $("#curpagedbprice").val(0);
        $("#totalrec").val(response.data.result);
        initDBPricePagination();
    },'json');
}