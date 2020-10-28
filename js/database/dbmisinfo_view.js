var maxheight=560;
function init_misinfo_view() {
    initDBMisinfoPagination();
    $(".missing_head").find(".sortcell").unbind('click').click(function () {
        var fld=$(this).data('sortcell');
        sort_missinfo(fld);
    });
    $("#misinfofind_it").unbind('click').click(function(){
        search_missinfodata();
    });
    $("#searchmisinfo").keypress(function(event){
        if (event.which == 13) {
            search_missinfodata();
        }
    });
    $("#misinfoclear_it").unbind('click').click(function(){
        clear_searchmissinfo();
    })
    $("select#vendorselectmisinfo").unbind('change').change(function(){
        search_missinfodata();
    });
    // Change Brand
    // $("#itemmisinfobrandmenu").find("div.left_tab").unbind('click').click(function(){
    //     var brand = $(this).data('brand');
    //     $("#itemmisinfobrand").val(brand);
    //     $("#itemmisinfobrandmenu").find("div.left_tab").removeClass('active');
    //     $("#itemmisinfobrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
    //     search_missinfodata();
    // });
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
    params.push({name:'search', value:$("#searchmisinfo").val()});
    params.push({name:'vendor_id',value:$("select#vendorselectmisinfo").val()});
    params.push({name: 'brand', value: $("#itemmisinfobrand").val()});
    $("#loader").css('display','block');
    var url='/database/misinfodat';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#curpagemisinfo").val(page_index);
            $('#dbmisinfotabinfo').empty().html(response.data.content);
            var infoh=parseInt($('div#dbmisinfotabinfo').css('height'));
            if (infoh<maxheight) {
                $("div#dbmisinfotabinfo").css('overflow','hidden');
                $("div#dbmisinfotabinfo tr").css('width','996px');
                $('div#dbmisinfotabinfo .last_col').css('width','672px');
            } else {
                $("div#dbmisinfotabinfo").css('overflow-y','auto');
            }
            $('div#dbmisinfotabinfo').find("tr:last").find('td').addClass('last_row');
            $("#loader").css('display','none');
            init_missinfo_content();
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function init_missinfo_content() {
    $('.missinfodatarow').find(".itemtitle").popover({
        placement: 'right',
        trigger: 'hover',
        html: true,
    });
}

function sort_missinfo(fld) {
    var cursort = $("#orderbymisinfo").val();
    var direction = $("#directionmisinfo").val();
    if (fld==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    $("#orderbymisinfo").val(fld);
    $("#directionmisinfo").val(direction);
    $(".missing_head").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $(".missing_head").find(".sortcell[data-sortcell='"+fld+"']").removeClass('gradient1').addClass('gradient2');
    initDBMisinfoPagination();
}

function search_missinfodata() {
    var url = '/database/searchcount';
    var params = new Array();
    params.push({name: 'search', value: $("#searchmisinfo").val()});
    params.push({name: 'vendor_id', value: $("select#vendorselectmisinfo").val()});
    params.push({name: 'brand', value: $("#itemmisinfobrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#curpagemisinfo").val(0);
            $("#totalrecmisinfo").val(response.data.result);
            initDBMisinfoPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_searchmissinfo() {
    $("#searchmisinfo").val('');
    $("select#vendorselectmisinfo").val('');
    $.post('/database/searchcount', {'search':''}, function(response){
        if (response.errors=='') {
            $("#curpagemisinfo").val(0);
            $("#totalrecmisinfo").val(response.data.result);
            initDBMisinfoPagination();
        } else {
            show_error(response);
        }
    },'json');
}