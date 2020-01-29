function init_orders() {
    $("select#generalorder_options").unbind('change').change(function(){
        search_generaldata();
    })
    $("select#generalfilter_options").unbind('change').change(function(){
        search_generaldata();
    })

    $("#monitorsearch").keypress(function(event){
        if (event.which == 13) {
            search_generaldata();
        }
    });

    $("#clear_ord").unbind('click').click(function(){
        $("#monitorsearch").val('');
        clear_generalsearch();
    });

    $("#find_ord").unbind('click').click(function(){
        search_generaldata();
    });

    $("a#addgeneralnew").unbind('click').click(function(){
        add_neworder();
    })
    initGeneralPagination();
}

/* Init pagination */
function initGeneralPagination() {
    // count entries inside the hidden content
    var num_entries=$('#totalrec').val();
    var perpage = $("#perpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".artOrderPagination").empty();
        pageGeneralCallback(0);
    } else {
        var curpage=$("#curpage").val();
        // Create content inside pagination element
        $(".artOrderPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageGeneralCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageGeneralCallback(page_index){
    // var perpage = itemsperpage;
    var search=$("input#monitorsearch").val();
    if (search=='Enter order #, customer') {
        search='';
    }
    var params=new Array();
    params.push({name:'search', value:search});
    params.push({name:'offset', value:page_index})
    params.push({name:'filter', value:$("select#generalfilter_options").val()});
    params.push({name:'add_filtr', value:$("select#generalorder_options").val()});
    params.push({name:'limit', value:$("#perpage").val()});
    params.push({name:'order_by', value:$("#orderby").val()});
    params.push({name:'direction', value:$("#direction").val()});
    params.push({name:'maxval', value:$('#totalrec').val()});

    var url='/art/order_data';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $('div.tabledata').empty().html(response.data.content);
            $("#curpage").val(page_index);
            general_view_init();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function general_view_init() {
    var arttemplate='<div class="popover green_background"  role="tooltip"><div class="arrow"></div><div class="popover-content art_tooltip"></div></div>';
    $("div.artnoteshow").popover({
        html: true,
        trigger: 'hover',
        placement: 'left'
    });
    $("div.ordercode").popover({
        html: true,
        trigger: 'hover',
        placement: 'left'
    });
    $('div.artlastmessageview').hover(
        function(){
            var e=$(this);
            $.get(e.data('messageview'),function(d) {
                e.popover({
                    content: d,
                    placement: 'left',
                    html: true,
                    template: arttemplate
                }).popover('show');
            });
        },
        function(){
            $(this).popover('hide');
        }
    );
    $("div.ordernum").unbind('click').click(function(){
        var order_id=$(this).data('orderid');
        order_artstage(order_id);
    })
    var rowid='';
    $("div.rowdata").hover(
        function(){
            rowid=$(this).data('orderid');
            $("div.rowdata[data-orderid="+rowid+"]").addClass("current_row");
        },
        function(){
            rowid=$(this).data('orderid');
            $("div.rowdata[data-orderid="+rowid+"]").removeClass("current_row");
        }
    );
    /* edit, etc */
    $("a.genord_editord").unbind('click').click(function(){
        var order_id=$(this).data('orderid');
        edit_generalorder(order_id);
    });

    $("input.genordartblanccheck").unbind('change').change(function(){
        var order_id=$(this).data('orderid');
        change_generalblanc(order_id);
    })

    $("input.calcccfee").unbind('change').change(function(){
        var order_id=$(this).data('orderid');
        change_generalcc(order_id);
    })

    // $("div.artstage").unbind('click').click(function(){
    //     var order_id=$(this).data('orderid');
    //     order_artstage(order_id);
    // })

    $("div.rowdata").find('div.ordernote').unbind('click').click(function(){
        var order_id=$(this).data('orderid');
        edit_generalartnote(order_id);
    });
    $("div.addneworder").unbind('click').click(function(){
        // var order_id=$(this).data('orderid');
        order_artstage(0);
    });
}
/* Inline Edit */
function edit_generalorder(orderid) {
    /* Change view */
    // $("select#viewshow_select").val('');
    // show_fullview();
    $("a#addgeneralnew").unbind('click');
    $("a.genord_editord").unbind('click');
    var url="/generalorders/edit_order";
    $.post(url, {'order_id':orderid}, function(response){
        if (response.errors=='') {
            /* Unbinnd NEW button & Edit */
            $("a#addgeneralnew").unbind('click');
            $("a.editgeneralorder").unbind('click');
            $("div.genord_rowdat[data-orderid="+orderid+"]").empty().html(response.data.content);
            init_genorderedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_genorderedit() {
    /* Init Calendar */
    $("input#order_date").datepicker();
    /* Item search */
    $("select#order_item").searchable();
    $("select#order_item").change(function(){
        var item_id=$(this).val();
        var olditem=$("input#item_id").val();
        $("input#item_id").val(item_id);
        if (item_id=='-1' || item_id=='-2') {
            if (item_id!=olditem) {
                $("div.genord_itemdatedit").addClass('editotheritem').empty().html('<img src="/img/itemedit_icon.png" alt="Other"/>').click(function(){
                    edit_otheritemvalue();
                });
            }
        } else {
            $("div.genord_itemdatedit").removeClass('editotheritem').empty().unbind('click');
        }
    });
    $("div.editotheritem").click(function(){
        edit_otheritemvalue();
    })
    /* Init save & cancel btns */
    $("a.saveedit").click(function(){
        savegeneralorder();
    });
    $("a.cancedit").click(function(){
        // initGeneralPagination();
        cancelgeneralorder();
    })
}

function edit_otheritemvalue() {
    // Get Item Id
    var item_id=$("input#item_id").val();
    var order_items=$("input#order_items").val();
    var url="/generalorders/edit_otheritemval";
    $.post(url, {'item_id':item_id, 'order_items':order_items}, function(response){
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

function cancelgeneralorder() {
    var order_id=$("input#order_id").val();
    if (order_id==0) {
        initGeneralPagination();
    } else {
        var url="/generalorders/order_canceledit";
        $.post(url, {'order_id':order_id}, function(response){
            if (response.errors=='') {
                $("div.genord_rowdat[data-orderid="+order_id+"]").empty().html(response.data.content);
                general_view_init();
                $("a#addgeneralnew").click(function(){
                    add_neworder();
                })
            } else {
                show_error(response);
            }
        }, 'json');
    }
}

function add_neworder() {
    /* Change view */
    $("select#viewshow_select").val('');
    show_fullview();
    var url="/generalorders/edit_order";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $("#orderprofit0").empty().html(response.data.content);
            $("input#order_date").datepicker();
            /* Searchable  */
            $("select#order_items").searchable();
            /* Init save & cancel btns */
            $("a.saveedit").click(function(){
                savegeneralorder();
            });
            $("a.cancedit").click(function(){
                initGeneralPagination();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function savegeneralorder() {
    var dat=$("#orderedit").serializeArray();
    dat.push({name:'item_id', value:$("input#item_id").val()});
    dat.push({name:'order_items',value:$("input#order_items").val()});
    var url="/generalorders/save_order";
    var order_id=$("input#order_id").val();
    $.post(url, dat, function(response){
        if (response.errors!='') {
            show_error(response);
        } else {
            if (order_id==0) {
                search_generaldata();
            } else {
                $("div.genord_rowdat[data-orderid="+order_id+"]").empty().html(response.data.content);
                general_view_init();
                $("a#addgeneralnew").click(function(){
                    add_neworder();
                })
            }
        }
    }, 'json');
}

/* Search functions */
function search_generaldata() {
    var search=$("input#monitorsearch").val();
    if (search=='Enter order #, customer') {
        search='';
    }
    var filter=$("select#generalfilter_options").val();
    var add_filtr=$("select#generalorder_options").val();
    /* Recalculate total number */
    var url="/art/search_orders";
    $.post(url, {'search':search, 'filter':filter, 'add_filtr':add_filtr}, function(response){
        if (response.errors=='') {
            $("#totalrec").val(response.data.totals);
            initGeneralPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function clear_generalsearch() {
    // $("input#monitorsearch").val('Enter order #, customer');
    $("#curpage").val(0);
    var datqry=new Date().getTime();

    /* Recalculate total number */
    var url="/generalorders/search_orders";
    $.post(url, {'dat':datqry}, function(response){
        if (!response.errors) {
            $("#totalrec").val(response.data.totals);
            initGeneralPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_generalsort(sortname,sortclass) {
    var cur_sort=$("#orderby").val();
    var cur_dir=$("#direction").val();

    $(".title-table").children().each(function(i) {
        $(this).removeClass('activesortdesc');
        $(this).removeClass('activesortasc');
    })

    if (cur_sort==sortname) {
        if (cur_dir=='asc') {
            $("#direction").val('desc');
            $("."+sortclass).addClass('activesortdesc');
        } else {
            $("#direction").val('asc');
            $("."+sortclass).addClass('activesortasc');
        }
    } else {
        $("#direction").val('desc');
        $("#orderby").val(sortname);
        $("."+sortclass).addClass('activesortdesc');
    }
    initGeneralPagination();
}


/* Blank Order */
function change_generalblanc(order_id) {
    var newval='';
    if ($("input.genordartblanccheck[data-orderid="+order_id+"]").prop('checked')==true) {
        newval=1;
    } else {
        newval=0;
    }
    var url="/generalorders/order_changeblank";
    $.post(url,{'order_id':order_id, 'order_blank':newval}, function(response){
        if (response.errors=='') {
            $("div.genord_rowdat[data-orderid="+order_id+"]").empty().html(response.data.content);
            general_view_init();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (newval==1) {
                    $("input.genordartblanccheck[data-orderid="+order_id+"]").prop('checked',false);
                } else {
                    $("input.genordartblanccheck[data-orderid="+order_id+"]").prop('checked',true);
                }
            }
        }
    },'json');
}


function change_generalcc(order_id) {
    var newval='';
    if ($("input.calcccfee[data-orderid="+order_id+"]").prop('checked')==true) {
        newval=1;
    } else {
        newval=0;
    }
    var url="/generalorders/order_changeccfee";
    $.post(url, {'order_id':order_id,'ccfee':newval}, function(response){
        if (response.errors=='') {
            $("input[data-orderid="+order_id+"]").attr('title',response.data.ccfee);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (newval==1) {
                    $("input[data-orderid="+order_id+"]").prop('checked',false);
                } else {
                    $("input[data-orderid="+order_id+"]").prop('checked',true);
                }
            }
        }
    }, 'json');
}

function edit_generalartnote(order_id) {
    var url="/generalorders/order_artnote";
    $.post(url, {'order_id':order_id}, function(response){
        if(response.errors=='') {
            show_popup("edit_area");
            $("div#pop_content").empty().html(response.data.content);
            $("div#pop_content div.saveordernote").click(function(){
                save_generalartnote();
            })
            $("a#popupContactClose").unbind('click').click(function(){
                disablePopup();
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function save_generalartnote() {
    var order_id=$("div#pop_content input#order_id").val();
    var art_note=$("div#pop_content #art_note").val();
    var url="/generalorders/save_orderartnote";
    $.post(url, {'order_id':order_id,'art_note':art_note}, function(response){
        if(response.errors=='') {
            disablePopup();
            initGeneralPagination();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');

}

function change_pageview() {
    var viewtype=$("select#viewshow_select").val();
    if (viewtype=='') {
        show_fullview();
    } else if (viewtype=='fin') {
        show_finance();
    } else if (viewtype=='art') {
        show_art();
    }
}

function show_fullview() {
    $("#loader").css('display','block');
    /* Show ART part */
    $("div.tdart").css('display','block');
    $("div.tdart-dat").css('display','block');
    $("div.tdredrawn").css('display','block');
    $("div.tdredrawn-dat").css('display','block');
    $("div.tdvector").css('display','block');
    $("div.tdvector-dat").css('display','block');
    $("div.tdproofed").css('display','block');
    $("div.tdproofed-dat").css('display','block');
    $("div.tdapprove").css('display','block');
    $("div.tdapprove-dat").css('display','block');
    $("div.tdapplicat").css('display','block');
    $("div.tdapplicat-dat").css('display','block');
    $("div.tdcode").css('display','block');
    $("div.tdcode-dat").css('display','block');
    $("div.tdnote").css('display','block');
    $("div.tdnote-dat").css('display','block');
    /* Show FINANCE */
    $("div.tdrevenue").css('display','block');
    $("div.tdrevenue-dat").css('display','block');
    $("div.tdshipping").css('display','block');
    $("div.tdshipping-dat").css('display','block');
    $("div.tdtax").css('display','block');
    $("div.tdtax-dat").css('display','block');
    $("div.tdccfee").css('display','block');
    $("div.tdccfee-dat").css('display','block');
    /*$("div.").css('display','block');
    $("div.").css('display','block');*/
    $("#loader").css('display','none');
}
function show_finance() {
    $("#loader").css('display','block');
    $("div.tdart").css('display','none');
    $("div.tdart-dat").css('display','none');
    $("div.tdredrawn").css('display','none');
    $("div.tdredrawn-dat").css('display','none');
    $("div.tdvector").css('display','none');
    $("div.tdvector-dat").css('display','none');
    $("div.tdproofed").css('display','none');
    $("div.tdproofed-dat").css('display','none');
    $("div.tdapprove").css('display','none');
    $("div.tdapprove-dat").css('display','none');
    $("div.tdapplicat").css('display','none');
    $("div.tdapplicat-dat").css('display','none');
    $("div.tdcode").css('display','none');
    $("div.tdcode-dat").css('display','none');
    $("div.tdnote").css('display','none');
    $("div.tdnote-dat").css('display','none');
    /* Show FINANCE */
    $("div.tdrevenue").css('display','block');
    $("div.tdrevenue-dat").css('display','block');
    $("div.tdshipping").css('display','block');
    $("div.tdshipping-dat").css('display','block');
    $("div.tdtax").css('display','block');
    $("div.tdtax-dat").css('display','block');
    $("div.tdccfee").css('display','block');
    $("div.tdccfee-dat").css('display','block');
    $("#loader").css('display','none');
}
function show_art() {
    $("#loader").css('display','block');
    $("div.tdart").css('display','block');
    $("div.tdart-dat").css('display','block');
    $("div.tdredrawn").css('display','block');
    $("div.tdredrawn-dat").css('display','block');
    $("div.tdvector").css('display','block');
    $("div.tdvector-dat").css('display','block');
    $("div.tdproofed").css('display','block');
    $("div.tdproofed-dat").css('display','block');
    $("div.tdapprove").css('display','block');
    $("div.tdapprove-dat").css('display','block');
    $("div.tdapplicat").css('display','block');
    $("div.tdapplicat-dat").css('display','block');
    $("div.tdcode").css('display','block');
    $("div.tdcode-dat").css('display','block');
    $("div.tdnote").css('display','block');
    $("div.tdnote-dat").css('display','block');
    /* Show FINANCE */
    $("div.tdrevenue").css('display','none');
    $("div.tdrevenue-dat").css('display','none');
    $("div.tdshipping").css('display','none');
    $("div.tdshipping-dat").css('display','none');
    $("div.tdtax").css('display','none');
    $("div.tdtax-dat").css('display','none');
    $("div.tdccfee").css('display','none');
    $("div.tdccfee-dat").css('display','none');
    $("#loader").css('display','none');
}
