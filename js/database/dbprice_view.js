//var itemsperpage=425;
// var itemsperpage=200;
var greenpopover_template='<div class="popover green_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
var whitepopover_template='<div class="popover white_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
var redpopover_template='<div class="popover red_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
var orangepopover_template='<div class="popover orange_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
var blackpopover_template='<div class="popover black_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
var maroonpopover_template='<div class="popover maroon_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';

function init_dbprice_view() {
    initDBPricePagination();
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
    params.push({name:'limit',value: $("#perpagedbprice").val()});
    params.push({name:'order_by', value: $("#orderbydbprice").val()});
    params.push({name:'direction',value: $("#directiondbprice").val()});
    params.push({name:'search', value:$.trim($("#searchdbprice").val())});
    params.push({name:'compareprefs', value:$("#compareprefs").val()});
    params.push({name:'vendor_id', value:$("select#vendordbprice").val()});
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
    params.push({name:'offset', value: page_index});
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
            pricetablecontent_init();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function pricetablecontent_init() {
    $(".greenprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: greenpopover_template,
    });
    $(".whiteprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: whitepopover_template,
    });
    $(".redprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: redpopover_template,
    });
    $(".orangeprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: orangepopover_template,
    });
    $(".blackprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: blackpopover_template,
    });
    $(".maroonprof").popover({
        placement: 'left',
        trigger: 'hover',
        html: true,
        template: maroonpopover_template,
    });
    $('.pricetable-row').find(".itemtitle").popover({
        placement: 'right',
        trigger: 'hover',
        html: true,
    });
    $(".table-price-header").find('.cellsort').unbind('click').click(function(){
        var fld=$(this).data('sortfld');
        price_sortdata(fld);
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
            case 'orange':
                $(this).removeClass('red').removeClass('white').removeClass('pink').addClass('orange');
                break;

        }
        initDBPricePagination();
    });

    $("select#vendordbprice").unbind('change').change(function(){
        search_pricedata();
    })

    $("input.pricecomparechk").unbind('change').change(function(){
        search_pricedata();
    })

    $("#dbpricefind_it").unbind('click').click(function(){
        search_pricedata();
    });

    $("#searchdbprice").keypress(function(event){
        if (event.which == 13) {
            search_pricedata();
        }
    });

    $("#dbpriceclear_it").unbind('click').click(function(){
        clear_pricesearch();
    });

    $("#sortupdatetim").unbind('change').change(function(){
        var datesort=$(this).val();
        if (datesort=='') {
            price_sortdata('item_number');
        } else {
            $(".table-price").find('.gradient2').removeClass('gradient2').addClass('gradient1');
            $(".table-price-header").find('.cellsort[data-sortfld="update_time"]').addClass('gradient2');
            $("#orderbydbprice").val('update_time');
            if (datesort=='upd-desc') {
                $("#directiondbprice").val('desc');
            } else {
                $("#directiondbprice").val('asc');
            }
            initDBPricePagination();
        }
    });
    $('.pricetable-row').find(".editcoll").unbind('click').click(function(){
        var item_id=$(this).data('item');
        view_itemdetails(item_id);
    })

}

function price_sortdata(fld) {
    var cursort = $("#orderbydbprice").val();
    var direction = $("#directiondbprice").val();
    $(".table-price").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $(".table-price-header").find('.cellsort[data-sortfld="'+fld+'"]').addClass('gradient2');
    if (fld==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    $("#orderbydbprice").val(fld);
    $("#directiondbprice").val(direction);
    $("select#sortupdatetim").val('');
    initDBPricePagination();
}

function search_pricedata() {
    var search=$("#searchdbprice").val();
    var vend=$("select#vendordbprice").val();
    $.post('/database/searchcount', {'search':search, 'vendor_id':vend}, function(response){
        if (response.errors=='') {
            $("#curpagedbprice").val(0);
            $("#totalrecdbprice").val(response.data.result);
            initDBPricePagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_pricesearch() {
    $("#searchdbprice").val('');
    $("select#vendordbprice").val('');
    $.post('/database/searchcount', {'search':''}, function(response){
        if (response.errors=='') {
            $("#curpagedbprice").val(0);
            $("#totalrecdbprice").val(response.data.result);
            initDBPricePagination();
        } else {
            show_error(response);
        }
    },'json');
}