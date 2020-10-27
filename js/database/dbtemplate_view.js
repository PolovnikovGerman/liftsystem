// var itemsperpage=425;
// var itemsperpage=200;
var maxheight=523;
function init_templates_view() {
    initDbTemplPagination();
    $("#itemnum").unbind('click').click(function(){
        template_sort('item_number','itemnum');
    });
    $("#itemname").unbind('click').click(function(data){
        template_sort('item_name','itemname');
    });
    $("#dbtemplatfind_it").unbind('click').click(function(){
        search_data();
    });
    $("#searchdbtemplat").keypress(function(event){
        if (event.which == 13) {
            search_data();
        }
    });
    $("#dbtemplatclear_it").unbind('click').click(function(){
        clear_search();
    })
    $("select#dbtemplatvendorselect").unbind('change').change(function(){
        search_data();
    });
    // Change Brand
    // $("#itemtemplatesbrandmenu").find("div.left_tab").unbind('click').click(function(){
    //     var brand = $(this).data('brand');
    //     $("#itemtemplatesbrand").val(brand);
    //     $("#itemtemplatesbrandmenu").find("div.left_tab").removeClass('active');
    //     $("#itemtemplatesbrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
    //     search_data();
    // });
}

function make_hover() {
    $(".templatedatarow").hover(
        function () {
            $(this).find(".player").attr("src",'/img/database/play-white.png');
            $(this).find(".templatesource").removeClass('text-7').addClass('green-stroka');
            $(this).find(".itemtitle").addClass('green-bor3');
            $(this).find(".itemnum").addClass('green-bor2');
            $(this).find(".editcoll").addClass('green-bor2');
            $(this).find(".numinlist").addClass('green-bor1');
        },
        function () {
            $(this).find(".player").attr("src",'/img/database/play-green.png');
            $(this).find(".templatesource").removeClass('green-stroka').addClass('text-7');
            $(this).find(".itemtitle").removeClass('green-bor3');
            $(this).find(".itemnum").removeClass('green-bor2');
            $(this).find(".editcoll").removeClass('green-bor2');
            $(this).find(".numinlist").removeClass('green-bor1');
        }
    );
    $(".updateimprintradio").unbind('change').change(function(){
        var item=$(this).data('item');
        var updateimpr=$(this).val();
        if (updateimpr==1) {
            $(".aiupdatestatus[data-item='"+item+"']").removeClass('updated').addClass('partialupdate');
        } else {
            $(".aiupdatestatus[data-item='"+item+"']").removeClass('partialupdate').addClass('updated');
        }
        var url="/database/update_imprint";
        $.post(url,{'item_id':item,'imprint_update':updateimpr}, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}

/**
 * Initialisation function for pagination
 */
function initDbTemplPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalrecdbtempl').val();
    var perpage = $("#perpagedbtempl").val();
    var curpage=$("#curpagedbtempl").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#dbtemplatPagination").empty();
        $("input#curpagedbtempl").val(0);
        pageDbTemplCallback(0);
    } else {
        // Create content inside pagination element
        $("#dbtemplatPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageDbTemplCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageDbTemplCallback(page_index, jq){
    var params=new Array();
    var search=$("#searchdbtemplat").val();
    params.push({name:'offset', value:page_index});
    params.push({name:'limit', value:$("#perpagedbtempl").val()});
    params.push({name:'order_by', value:$("#orderbydbtempl").val()});
    params.push({name:'direction', value:$("#directiondbtempl").val()});
    params.push({name:'search', value:search});
    params.push({name:'vendor_id', value:$("select#dbtemplatvendorselect").val()});
    params.push({name: 'brand', value: $("#itemtemplatesbrand").val()});
    $("#curpagedbtempl").val(page_index);
    $("#loader").css('display','block');
    var url='/database/templatedat';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $('#dbtempltabinfo').empty().html(response.data.content);
            var infoh=parseInt($('div#dbtempltabinfo').css('height').replace('px',''));
            if (infoh<maxheight) {
                $("div#dbtempltabinfo").css('overflow','hidden');
            } else {
                $("div#dbtempltabinfo").css('overflow-y','auto');
            }
            make_hover();
            /*$('#tabinfo').find("tr:last").find('td').addClass('last_row');*/
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function template_sort(colsort,itemsort) {


    var cursort = $("#orderbydbtempl").val();
    var direction = $("#directiondbtempl").val();
    if (colsort==cursort) {
        if (direction=='asc') {
            direction='desc';
        } else {
            direction='asc';
        }
    } else {
        direction='asc';
    }
    /* template_head */
    $(".template_head").find('.gradient2').removeClass('gradient2').addClass('gradient1');
    $("#"+itemsort).removeClass('gradient1').addClass('gradient2');
    $("#orderbydbtempl").val(colsort);
    $("#directiondbtempl").val(direction);
    initDbTemplPagination();
}

function empty_vectorfile() {
    alert('This Item has no template file');
}


function openai(imgurl) {
    window.open(imgurl,'mywindow','width=400,height=200');
}

function search_data() {
    var url = '/database/searchcount';
    var params = new Array();
    params.push({name: 'search', value: $("#searchdbtemplat").val()});
    params.push({name: 'vendor_id', value: $("select#dbtemplatvendorselect").val()});
    params.push({name: 'brand', value: $("#itemtemplatesbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#curpagedbtempl").val(0);
            $("#totalrecdbtempl").val(response.data.result);
            initDbTemplPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_search() {
    $("#searchdbtemplat").val('');
    $("select#dbtemplatvendorselect").val('');
    $.post('/database/searchcount', {'search':''}, function(response){
        if (response.errors=='') {
            $("#curpagedbtempl").val(0);
            $("#totalrecdbtempl").val(response.data.result);
            initDbTemplPagination();
        } else {
            show_error(response);
        }
    },'json');
}
