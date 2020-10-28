// var itemsperpage=425;
var maxheight=560;
function init_dbcategory_view() {
    initDBCategoryPagination();
    // Change Brand
    // $("#itemcategorybrandmenu").find("div.left_tab").unbind('click').click(function(){
    //     var brand = $(this).data('brand');
    //     $("#itemcategorybrand").val(brand);
    //     $("#itemcategorybrandmenu").find("div.left_tab").removeClass('active');
    //     $("#itemcategorybrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
    //     search_dbcategdata();
    // });
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
    params.push({name:'vendor_id', value:$("select#vendordbcateg").val()});
    params.push({name: 'brand', value: $("#itemcategorybrand").val()});
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
            init_dbcategory_content();
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function init_dbcategory_content() {
    $("#pagemanage").unbind('click').click(function(){
        lockpage();
    });
    $(".dbcategory_table_head").find('.sortcell').unbind('click').click(function(){
        var fld=$(this).data('sortfld');
        sort_categorydat(fld);
    });
    $("#dbcategorfind_it").unbind('click').click(function(){
        search_dbcategdata();
    });
    $("#searchdbcategory").keypress(function(event){
        if (event.which == 13) {
            search_dbcategdata();
        }
    });
    $("#dbcategorclear_it").unbind('click').click(function(){
        clearsearch_dbcategory();
    })
    $("select#vendordbcateg").unbind('change').change(function(){
        search_dbcategdata();
    });
    $(".categoryname select").unbind('change').change(function(){
        var item_category = $(this).val();
        var item_id = $(this).parent('div.categoryname').data('itemid');
        var categ_num = $(this).parent('div.categoryname').data('categorynum');
        var itemcateg_id = $(this).data('itemcategid');
        change_category(item_id, itemcateg_id, item_category, categ_num);
    });
    $(".categorydatarow").find(".itemtitle").popover({
        placement: 'right',
        trigger: 'hover',
        html: true,
    });
    $(".categorydatarow").find('.editcoll').unbind('click').click(function () {
        var item_id=$(this).data('item');
        view_itemdetails(item_id);
    });
}

function sort_categorydat(fld) {
    var cursort = $("#orderbydbcateg").val();
    var direction = $("#directiondbcateg").val();
    if (fld=='count_up') {
        direction='desc';
        $("#orderbydbcateg").val('cnt');
    } else if (fld=='count_down') {
        direction='asc';
        $("#orderbydbcateg").val('cnt');
    } else {
        if (fld==cursort) {
            if (direction=='asc') {
                direction='desc';
            } else {
                direction='asc';
            }
        } else {
            direction='asc';
        }
        $("#orderbydbcateg").val(fld);
    }
    $("#directiondbcateg").val(direction);
    $(".dbcategory_table_head").removeClass('gradient2');
    $(".dbcategory_table_head .sortcell").removeClass('gradient2').addClass('gradient1')
    $(".dbcategory_table_head").find('div[data-sortfld="'+fld+'"]').addClass('gradient2');
    initDBCategoryPagination();
}


function lockpage() {
    var itclass=$("#pagemanage").attr('class');
    if (itclass=='pagelocked') {
        $("#pagemanage").attr('class','pageunlocked');
        $("#btntext").text('Page Unlocked');
        $(".categdatacell").find("select").attr('disabled',false);
    } else {
        $("#pagemanage").attr('class','pagelocked');
        $("#btntext").text('Page Locked');
        $(".categdatacell").find("select").attr('disabled',true);
    }
}

// function change_category(obj) {
//     var elid=obj.id;
//     var value=$("#"+elid).val();
//     var celid=$("#"+elid).parent().attr('id');
//     var url='/database/updcat';
//     $.post(url,{'id':elid,'value':value},function(response){
//         if (response.errors=='') {
//             $("#"+celid).empty().html(response.data.content);
//         } else {
//             $("#"+celid).empty().html(response.data.content);
//             alert(data.error);
//         }
//
//     },'json');
//
// }

function change_category(item_id, itemcateg_id, item_category, categ_num) {
    var params = new Array();
    params.push({name: 'item_id', value: item_id});
    params.push({name: 'itemcategory_id', value: itemcateg_id});
    params.push({name: 'item_category', value: item_category});
    var url='/database/upditemcategory';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $('.categorydatarow').find('.categoryname[data-itemid="'+item_id+'"][data-categorynum="'+categ_num+'"]').empty().html(response.data.content);
            init_dbcategory_content();
        } else {
            show_error(response);
        }
    },'json');
}

function search_dbcategdata() {
    var url='/database/searchcount';
    var params = new Array();
    params.push({name: 'search', value: $("#searchdbcategory").val()});
    params.push({name: 'vendor_id', value: $("select#vendordbcateg").val()});
    params.push({name: 'brand', value: $("#itemcategorybrand").val()});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("#curpagedbcateg").val(0);
            $("#totalrecdbcateg").val(response.data.result);
            initDBCategoryPagination();
        }
    }, 'json');
    //
}

function clearsearch_dbcategory() {
    $("#searchtemplate").val('Enter keyword or item #');
    $("#search").val('');
    $("select#vendordbcateg").val('');
    var url='/database/searchcount';
    var params = new Array();
    params.push({name: 'search', value: ''});
    params.push({name: 'vendor_id', value: ''});
    params.push({name: 'brand', value: $("#itemcategorybrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#curpagedbcateg").val(0);
            $("#totalrecdbcateg").val(response.data.result);
            initDBCategoryPagination();
        } else {
            show_error(response);
        }
    },'json');

}