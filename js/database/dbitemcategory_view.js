// var itemsperpage=425;
// var itemsperpage=200;
var maxheight=560;
function init_dbcategory_view() {
    initDBCategoryPagination();
    $("#pagemanage").unbind('click').click(function(){
        lockpage();
    });
    $("#itemnum").unbind('click').click(function(){
        category_sort('i.item_number','itemnum');
    });
    $("#itemname").unbind('click').click(function(){
        category_sort('i.item_name','itemname');
    });
    $("#categup").unbind('click').click(function() {
        category_sort('count_up','categup');
    });
    $("#categdwn").unbind('click').click(function() {
        category_sort('count_dwn','categdwn');
    });
    $("#dbcategorfind_it").unbind('click').click(function(){
        search_data();
    });
    $("#searchdbcategory").keypress(function(event){
        if (event.which == 13) {
            search_data();
        }
    });
    $("#dbcategorclear_it").unbind('click').click(function(){
        clear_search();
    })
    $("select#vendorselect").unbind('change').change(function(){
        search_data();
    });
}

/**
 * Initialisation function for pagination
 */
function initDBCategoryPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrecdbcateg').val();
    var perpage = $("#perpagedbcateg").val();
    var curpage=$("#curpagedbcateg").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#dbcategoryPagination").empty();
        $("input#curpagedbcateg").val(0);
        pageDbCategselectCallback(0);
    } else {
        // Create content inside pagination element
        $("#dbcategoryPagination").mypagination(num_entries, {
            current_page:curpage,
            callback: pageDbCategselectCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_display_entries : 5,
            num_edge_entries : 1,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}


function pageDbCategselectCallback(page_index, jq){
    var params=new Array();
    var pagelock=1;
    var itclass=$("#pagemanage").attr('class');
    if (itclass!='pagelocked') {
        pagelock=0;
    }
    params.push({name:'pagelock', value:pagelock});
    params.push({name:'offset', value: page_index});
    params.push({name:'limit', value: $("#perpagedbcateg").val()});
    params.push({name:'order_by', value:$("#orderbydbcateg").val()});
    params.push({name:'direction', value:$("#directiondbcateg").val()});
    params.push({name:'search', value:$("#searchdbcategory").val()});
    params.push({name:'vendor_id', value:$("select#vendorselect").val()});

    $("#curpagedbcateg").val(page_index);
    $("#loader").css('display','block');
    var url='/database/categorydat';
    $.post(url, params,function(response){
        if (response.errors=='') {
            $('#dbcategtabinfo').empty().html(response.data.content);
            var infoh=parseInt($('div#dbcategtabinfo').css('height').replace('px',''));
            if (infoh<maxheight) {
                /* Hide overflow */
                $("#dbcategtabinfo").css('overflow','hidden');
                $("div#dbcategtabinfo tr").css('width','996px');
                $('div#dbcategtabinfo td.last_col').children().css('width','112px');
                $('div#dbcategtabinfo td.last_col').children().children().css('width','112px');
            } else {
                $(".tabinfo").css('overflow-y','auto');
            }
            $('#dbcategtabinfo').find("tr:last").find('td').addClass('last_row');
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function category_sort(colsort,itemsort) {
    var perpage = itemsperpage;
    var cursort = $("#orderby").val();
    var direction = $("#direction").val();
    var curpage = $("#curpage").val();
    var search=$("#search").val();
    if (colsort=='count_up') {
        direction='desc';
    } else if (colsort=='count_down') {
        direction='asc';
    } else {
        if (colsort==cursort) {
            if (direction=='asc') {
                direction='desc';
            } else {
                direction='asc';
            }
        } else {
            direction='asc';
        }
    }
    var itclass=$("#pagemanage").attr('class');
    var pagelock=1;
    if (itclass!='pagelocked') {
        pagelock=0;
    }
    $("#orderby").val(colsort);
    $("#direction").val(direction);
    /* */
    $(".categories_head").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $("#"+itemsort).removeClass('gradient1').addClass('gradient2');
    $("#dbitemloader").css('display','block');

    var params=new Array();
    params.push({name:'pagelock', value:pagelock});
    params.push({name:'offset', value: $("#curpage").val()});
    params.push({name:'limit', value: itemsperpage});
    params.push({name:'order_by', value:$("#orderby").val()});
    params.push({name:'direction', value:$("#direction").val()});
    params.push({name:'search', value:$("#search").val()});
    params.push({name:'vendor_id', value:$("select#vendorselect").val()});

    $.post('/dbitemview/categorydat',params,function(data){
        $('#tabinfo').empty().append(data.data.content);
        var infoh=parseInt($('div#tabinfo').css('height').replace('px',''));
        if (infoh<maxheight) {
            $("div#tabinfo").css('overflow','hidden');
            $("div#tabinfo tr").css('width','996px');
            $('div#tabinfo td.last_col').children().css('width','112px');
            $('div#tabinfo td.last_col').children().children().css('width','112px');
        } else {
            $("div#tabinfo").css('overflow-y','auto');
        }
        $('#tabinfo').find("tr:last").find('td').addClass('last_row');
        $("#dbitemloader").css('display','none');
        /* New Direction & column sort */
    },'json');
}


function lockpage() {
    var itclass=$("#pagemanage").attr('class');
    if (itclass=='pagelocked') {
        $("#pagemanage").attr('class','pageunlocked');
        $("#btntext").text('Page Unlocked');
        $("select").attr('disabled',false);
    } else {
        $("#pagemanage").attr('class','pagelocked');
        $("#btntext").text('Page Locked');
        $("select").attr('disabled',true);
    }
}

function change_category(obj) {
    var elid=obj.id;
    var value=$("#"+elid).val();
    var url='/dbitemview/updcat';
    var celid=$("#"+elid).parent().attr('id');

    $.post(url,{'id':elid,'value':value},function(data){
        if (data.error!='') {
            alert(data.error);
            $("#"+celid).empty().html(data.content);
        } else {
            $("#"+celid).empty().html(data.content);
        }

    },'json');

}

function search_data() {
    var search=$("#searchtemplate").val();
    if (search=='Enter keyword or item #') {
        search='';
    }
    var vend=$("select#vendorselect").val();
    var url='/dbitemview/searchcount';
    $.post(url, {'search':search,'vendor_id':vend}, function(response){
        if (response.errors=='') {
            if (response.data.result==0) {
                alert('No search result');
                $("#searchtemplate").val('');
                $("select#vendorselect").val('');
            } else {
                $("#search").val(search);
                $("#curpage").val(0);
                $("#totalrec").val(response.data.result);
                initDBCategoryPagination();
            }

        }
    }, 'json');


    //
}

function clear_search() {
    $("#searchtemplate").val('Enter keyword or item #');
    $("#search").val('');
    $("select#vendorselect").val('');
    $.post('/dbitemview/searchcount', {'search':''}, function(response){
        if (response.errors=='') {
            $("#curpage").val(0);
            $("#totalrec").val(response.data.result);
            initDBCategoryPagination();
        } else {
            show_error(response);
        }
    },'json');

}