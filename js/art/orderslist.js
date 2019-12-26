var mainurl="/art";
var emptysearch='Enter order #, customer';
function init_orderlist_management() {
    /* Search Form */
    $("#clear_arts").click(function(){
        clear_ordartsearch();
    });
    $("#find_arts").click(function(){
        search_ordartdata();
    });
    $("#filter_options").change(function(){
        search_ordartdata();
    })
    $("select#order_options").change(function(){
        search_ordartdata();
    })
    $("input#artsearch").hover(
        function(){
            if ($("input#artsearch").val()==emptysearch) {
                $("input#artsearch").val('').removeClass('monitorsearch_input').addClass('monitorsearch_input_active');
            }
        },
        function() {
            if ($("input#artsearch").val()=='') {
                $("input#artsearch").val(emptysearch).removeClass('monitorsearch_input_active').addClass('monitorsearch_input');
            }
        }
    );
    $("input#artsearch").keypress(function(event){
        if (event.which == 13) {
            search_ordartdata();
        }
    });
}
/* Open when page activated */
function init_orderlist() {
    initArtPagination();
}

/* Paginaton */
function initArtPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalart').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpageart").val();

    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#orderlist .Pagination").empty();
        pageArtCallback(0);
    } else {
        var curpage = $("#curpageart").val();
        // Create content inside pagination element
        $("div#orderlist .Pagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageArtCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageArtCallback(page_index) {
    /* Search */
    var search=$("#artsearch").val();
    if (search==emptysearch) {
        search='';
    }
    var params=new Array();
    params.push({name:'limit',value:$("#perpageart").val()});
    params.push({name:'order_by',value:$("#orderbyart").val()});
    params.push({name:'direction',value:$("#directionart").val()});
    params.push({name:'maxval',value:$('#totalart').val()});
    params.push({name:'search',value:search});
    params.push({name:'offset',value:page_index});
    params.push({name:'filter',value:$("#filter_options").val()});
    params.push({name:'add_filtr',value:$("#order_options").val()});

    $("#loader").css('display','block');
    var url=mainurl+'/order_listdata';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").css('display','none');
            $("div#orderlistdata").empty().html(response.data.content);
            init_orderlistcontent();
            $("input.curpageart").val(page_index);
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
}

function init_orderlistcontent() {
    /* make full row */
    var blockheigh=parseInt($("div.content-art-table").css('height').replace('px', ''));
    var maxheigh=parseInt($("div.content-art-table").css('max-height').replace('px',''));
    if (blockheigh<maxheigh) {
        $("div.content-art-table div.ordart_rowdata").addClass('fullrow');
    }
    // $("div.artnoteshow").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 220,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "most",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    // $("div.ordart_code-data").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 180,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "top",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    // $("div.artlastmessageview").each(function(){
    //     $(this).bt({
    //         ajaxCache: false,
    //         fill : '#1DCD19',
    //         cornerRadius: 10,
    //         width: 220,
    //         padding: 10,
    //         strokeWidth: '2',
    //         positions: "most",
    //         strokeStyle : '#000000',
    //         strokeHeight: '18',
    //         cssClass: 'art_tooltip',
    //         ajaxPath: ["$(this).data('messageview')"]
    //     });
    // });

    $("div.ordart_note-dat img").unbind('click').click(function(){
        edit_artordnote(this);
    })
    /* Edit Order data */
    $("a.edit_artorder").unbind('click').click(function(){
        edit_artorder(this);
    });
    $("input.ordartblanccheck").unbind('change').change(function(){
        var order_id=$(this).data('orderid');
        change_ordartblank(order_id);
    })
    $("div.artstage").unbind('click').click(function(){
        var ordrid=$(this).data('orderid');
        order_artstage(ordrid);
    })
    $("div.ordart_ordernum-dat").click(function(){
        var order_id=$(this).data('orderid');
        order_artstage(order_id);
    })
    var rowid='';
    $("div.ordart_rowdata").hover(
        function(){
            rowid=$(this).data('orderid');
            $("div.ordart_rowdata[data-orderid="+rowid+"]").addClass("current_row");
        },
        function(){
            rowid=$(this).data('orderid');
            $("div.ordart_rowdata[data-orderid="+rowid+"]").removeClass("current_row");
        }
    );
}

/* Edit ART order */
function edit_artorder(obj) {
    var orderid=obj.id.substr(3);
    var url=mainurl+"/order_editdata";
    $.post(url, {'order_id':orderid}, function(data){
        if (data.errors=='') {
            $("a.edit_artorder").unbind('click');
            $("div.ordart_rowdata[data-orderid="+orderid+"]").empty().html(data.data.content);
            /* Init Calendar */
            $("input#order_date").datepicker();
            /* Init Selector */
            $("select#order_items").searchable();
            $("select#order_items").unbind('change').change(function(){
                // Get new value
                var item_id=$("select#order_items").val();
                var orderitem=$("select#order_items :selected").text();
                $("input#order_items").val(orderitem);
                $("input#item_id").val(item_id);
                if (parseInt(item_id)>0) {
                } else {
                    var label=orderitem;
                    edit_otheritem_details(item_id);
                }
            })
            $("div.editotheritem").click(function(){
                var item_id=$("input#item_id").val();
                edit_otheritem_details(item_id);
            });
            /* Init save & cancel btns */
            $("a.saveedit").click(function(){
                saveartorder();
            });
            $("a.cancedit").click(function(){
                cancelartorderedit();
            })
        } else {
            show_error(data);
        }
    }, 'json');
}
/* Cancel Save */
function cancelartorderedit() {
    var order_id=$("input#order_id").val();
    var url=mainurl+"/order_canceledit";
    $.post(url,{'order_id':order_id},function(response){
        if (response.errors=='') {
            $("div.ordart_rowdata[data-orderid="+order_id+"]").empty().html(response.data.content);
            init_orderlistcontent();
        } else {
            show_error(response);
        }
    },'json')
}
/* Save order */
function saveartorder() {
    var dat=$("#orderedit").serializeArray();
    var item_id=$("input#item_id").val();
    dat.push({name:'item_id', value:item_id});
    var order_items=$("input#order_items").val();
    dat.push({name:'order_items', value:order_items});
    var order_id=$("input#order_id").val();
    var url=mainurl+"/save_orderdata";
    $("#loader").show();
    $.post(url, dat, function(data){
        if (data.errors!='') {
            $("#loader").hide();
            show_error(data)
        } else {
            $("#loader").hide();
            if (order_id==0) {
                $("#totalart").val(data.data.total);
                initArtPagination();
            } else {
                $("div.ordart_rowdata[data-orderid="+order_id+"]").empty().html(data.data.content);
                init_orderlistcontent();
            }
        }
    }, 'json');
}
/* Edit current (in Edit mode) Other Item Details */
function edit_otheritem_details(item_id) {
    var order_itemnumber=$("input#order_itemnumber").val();
    var order_items=$("input#order_items").val();
    var url=mainurl+"/order_otheritemedit";
    $.post(url, {'item_id':item_id, 'order_items': order_items}, function(response){
        if (response.errors=='') {
            show_popup('editmail_form');
            $("div#pop_content").empty().html(response.data.content);
            $("a#popupContactClose").click(function(){
                disablePopup();
            })
            $("div.artord_otheritemsave").click(function(){
                var order_items=$("textarea.otheritemvaluetext").val();
                $("input#order_items").val(order_items);
                disablePopup();
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_ordartsearch() {
    $("input#artsearch").val('Enter order #, customer');
    $("#curpageart").val(0);

    /* Recalculate total number */
    var url=mainurl+"/order_search";
    $.post(url, {}, function(response){
        if (response.errors==='') {
            $("input#totalart").val(response.data.totals);
            initArtPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Search functions */
function search_ordartdata() {
    var search=$("input#artsearch").val();
    if (search=='Enter order #, customer') {
        search='';
    }
    var params=new Array();
    params.push({name:'search', value:search});
    params.push({name:'filter', value:$("#filter_options").val()});
    params.push({name:'add_filtr', value:$("#order_options").val()});
    /* Recalculate total number */
    var url=mainurl+"/order_search";

    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#totalart").val(response.data.totals);
            initArtPagination();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

/* List view management */
function change_ordartblank(order_id) {
    var newval='';
    if ($("input.ordartblanccheck[data-orderid="+order_id+"]").prop('checked')==true) {
        newval=1;
    } else {
        newval=0;
    }
    var url=mainurl+"/order_changeblank";
    $.post(url,{'order_id':order_id, 'order_blank':newval}, function(response){
        if (response.errors=='') {
            $("div.ordart_rowdata[data-orderid="+order_id+"]").empty().html(response.data.content);
            init_orderlistcontent();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (newval==1) {
                    $("input.ordartblanccheck[data-orderid="+order_id+"]").prop('checked',false);
                } else {
                    $("input.ordartblanccheck[data-orderid="+order_id+"]").prop('checked',true);
                }
            }
        }
    },'json');
}


/* ART Note */
function edit_artordnote(obj) {
    var order_id=obj.id.substr(7);
    var url=mainurl+"/order_openartnote";
    $.post(url, {'order_id':order_id}, function(response){
        if(response.errors=='') {
            show_popup("edit_area");
            $("div#pop_content").empty().html(response.data.content);
            $("div#pop_content div.saveordernote").click(function(){
                save_ordartnote();
            })
            $("a#popupContactClose").unbind('click').click(function(){
                disablePopup();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_ordartnote() {
    var order_id=$("div#pop_content input#order_id").val();
    var art_note=$("div#pop_content #art_note").val();
    var url=mainurl+"/order_saveartnote";
    $("#loader").css('display','block');
    $.post(url, {'order_id':order_id,'art_note':art_note}, function(response){
        if(response.errors=='') {
            $("#loader").css('display','none');
            $("div.ordart_rowdata[data-orderid="+order_id+"]").empty().html(response.data.content);
            init_orderlistcontent();
            disablePopup();
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    }, 'json');
}
