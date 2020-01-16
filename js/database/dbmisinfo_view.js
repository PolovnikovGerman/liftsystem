// var itemsperpage=425;
// var itemsperpage=200;
var maxheight=560;
function init_misinfo_view() {
    initDBMisinfoPagination();
    $("#itemnum").unbind('click').click(function(){
        missing_sort('v.item_number','itemnum');
    });
    $("#itemname").unbind('click').click(function(data){
        missing_sort('v.item_name','itemname');
    });
    $("#missinfo").unbind('click').click(function(){
        missing_sort('missings','missinfo');
    });
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
    })
    $("select#vendorselect").change(function(){
        search_data();
    })
};

/**
 * Initialisation function for pagination
 */
function initDBMisinfoPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrecmisinfo').val();
    var perpage = $("#perpagemisinfo").val();
    var curpage=$("#curpagemisinfo").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#misinfoPagination").empty();
        $("input#curpagemisinfo").val(0);
        pageMisInfoCallback(0);
    } else {
        // Create content inside pagination element
        $("#misinfoPagination").mypagination(num_entries, {
            current_page:curpage,
            callback: pageMisInfoCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageMisInfoCallback(page_index, jq){
    var params=new Array();
    params.push({name:'offset', valuue:page_index})
    params.push({name:'limit', value:$("#perpagemisinfo").val()})
    params.push({name:'order_by', value:$("#orderbymisinfo").val()});
    params.push({name:'direction', value:$("#directionmisinfo").val()});
    params.push({name:'search', value:$("#search").val()});
    params.push({name:'vendor_id',value:$("select#vendorselect").val()});
    $("#curpagemisinfo").val(page_index);
    $("#loader").css('display','block');
    var url='/database/misinfodat';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $('#dbmisinfotabinfo').empty().html(response.data.content);
            var infoh=parseInt($('div#tabinfo').css('height'));
            if (infoh<maxheight) {
                $("div#tabinfo").css('overflow','hidden');
                $("div#tabinfo tr").css('width','996px');
                $('div#tabinfo .last_col').css('width','672px');
            } else {
                $("div#tabinfo").css('overflow-y','auto');
            }
            $('div#tabinfo').find("tr:last").find('td').addClass('last_row');
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function missing_sort(colsort,itemsort) {
    var cursort = $("#orderbymisinfo").val();
    var direction = $("#directionmisinfo").val();
    if (colsort==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    $("#orderbymisinfo").val(colsort);
    $("#directionmisinfo").val(direction);
    $(".missing_head").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $("#"+itemsort).removeClass('gradient1').addClass('gradient2');

    initDBMisinfoPagination();
}

function search_data() {
    var search=$("#searchtemplate").val();
    if (search=='Enter keyword or item #') {
        search='';
    }
    var vend=$("select#vendorselect").val();
    $.post('/database/searchcount', {'search':search, 'vendor_id':vend}, function(response){
        if (response.errors=='') {
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
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_search() {
    $("#searchtemplate").val('Enter keyword or item #');
    $("#search").val('');
    $("select#vendorselect").val('');
    $.post('/database/searchcount', {'search':''}, function(response){
        if (response.errors=='') {
            $("#curpage").val(0);
            $("#totalrec").val(response.data.result);
            initPagination();
        } else {
            show_error(response);
        }
    },'json');
}