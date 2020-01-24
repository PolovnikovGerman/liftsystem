// View content
function init_itemdetails_view() {
    var attribtemplate='<div class="popover blue_background"  role="tooltip"><div class="arrow"></div><div class="popover-content attrib_tooltip"></div></div>';
    $("#slider").easySlider({
        nextText : '',
        prevText : '',
        vertical : false
    });
    $(".closeitemdetails").click(function(){
        close_view();
    });
    $(".bottomtxtlnk").click(function(){
        var item=$(this).data('item');
        show_bottom_text(item)
    })
    $(".commontermslnk").click(function(){
        var item=$(this).data('item');
        show_common_terms(item);
    })
    $("div.checkoutspeciallnk").unbind('click').click(function(){
        var item=$(this).data('item');
        show_checkout_special(item);
    });
    $("div.location_upload").unbind('click').click(function () {
        var imgsrc = $(this).data('srclink');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    })
    $("div.pictures").find('.pic').unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".imprintpopover").popover({
        html: true,
        trigger: 'hover',
        placement: 'top'
    });
    $("td.competitorname").popover({
        html: true,
        trigger: 'hover',
        placement: 'right'
    });
    $(".tooltip-descript").popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        template: attribtemplate
    });
    $("div.shipcondinlink").click(function(){
        var item=$(this).data('item');
        show_shipping(item);
    });
    $("div.activate_btn").click(function(){
        var item=$(this).data('item');
        activate_edit(item);
    });
    // $(".viewvideo").click(function(){
    //     show_video();
    // })
}
// VIEW Functions
/* Close Preview */
function close_view() {
    var url='/database/restore_databaseview'
    $.post(url, {}, function (response) {
        if (response.errors=='') {
            var pagename = response.data.pagename;
            if (pagename=='categview') {
                init_page('itemcategoryview');
            } else {
                var start = $(".maincontentmenu_item:first").data('link');
                init_page(start);
            }
        } else {
            show_errors(response);
        }
    },'json');
}

function show_bottom_text(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'param', value: 'bottom_text'});
    var url='/itemdetails/view_footer';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            // editModal
            $("#editModalLabel").empty().html('View Bottom Text');
            $("#editModal").find('.modal-dialog').css('width','492px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');
}

function show_common_terms(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'param', value: 'common_terms'});
    var url='/itemdetails/view_footer';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            // editModal
            $("#editModalLabel").empty().html('View Common Terms');
            $("#editModal").find('.modal-dialog').css('width','352px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');
}

function show_checkout_special(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    var url="/itemdetails/view_specialcheck";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#editModalLabel").empty().html('View Checkout Specials');
            $("#editModal").find('.modal-dialog').css('width','564px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');
}

function show_shipping(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    var url='/itemdetails/view_shipping';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#editModalLabel").empty().html('View Shipping Details');
            $("#editModal").find('.modal-dialog').css('width','468px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');
}

// Activate Edit
function activate_edit(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    var url='/database/edit_item';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".dbcontentarea").hide();
            $("#itemdetailsview").show().empty().html(response.data.content);
            init_itemdetails_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function init_itemdetails_edit() {
    $(".closeitemdetails").click(function(){
        if (confirm('You realy want to exit without saving?')==true) {
            console.log('Exit');
            close_view();
        }
    });
    $("#slider").easySlider({
        nextText : '',
        prevText : '',
        vertical : false
    });
    $("input.itemactiveinput").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.itemactiveselect").unbind('change').change(function() {
        var params=new Array();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.checkoutspeciallnk").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/edit_specialcheck";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('View Checkout Specials');
                $("#editModal").find('.modal-dialog').css('width','564px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal('show');
                init_specialcheck_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_specialcheck_manage() {
    $("select.specialcheckout_selecttype").change(function(){
        if ($(this).val()==1) {
            $("div.specialcheckout_options").fadeIn(200);
        } else {
            $("div.specialcheckout_options").fadeOut(200);
        }
    })
    // $("input.specialsetupinpt").change(function(){
    //     if ($("input.specialsetupinpt").val()=='') {
    //         $("input#old_special_setup").val('');
    //     } else {
    //         newval=parseFloat($("input.specialsetupinpt").val());
    //         if (isNaN(newval)) {
    //             $("input.specialsetupinpt").val($("input#old_special_setup").val());
    //             $.flash('Incorrect Value', {timeout:3000});
    //         } else {
    //             newval=newval.toFixed(2);
    //             $("input.specialsetupinpt").val(newval);
    //             $("input#old_special_setup").val(newval);
    //         }
    //     }
    // })
    // $("input.specpriceqty").change(function(){
    //     /* specpriceqty0 */
    //     newval=$("#"+this.id).val();
    //     objid=this.id.substr(12);
    //     if (newval=='') {
    //         $("input#old_specpriceqty"+objid).val('');
    //         $("input#specpriceval"+objid).val('');
    //         $("input#old_specpriceval"+objid).val('');
    //         $("input#old_specamount"+objid).val('');
    //         $("div#amountrow"+objid).empty();
    //         $("input#profitval"+objid).val('');
    //         $("div#profitrow"+objid).empty();
    //         $("div#profperc"+objid).removeClass('white').removeClass('green').removeClass('orange').removeClass('red').removeClass('maroon').removeClass('black').empty();
    //     } else {
    //         newval=parseInt(newval);
    //         if (isNaN(newval)) {
    //             $("input#"+this.id).val($("input#old_specpriceqty"+objid).val());
    //             $.flash('Incorrect Value', {timeout:3000});
    //         } else {
    //             $("input#"+this.id).val(newval);
    //             $("input#old_specpriceqty"+objid).val(newval);
    //             recalc_specamount(objid);
    //         }
    //     }
    // })
    // $("input.specpriceval").change(function(){
    //     /* specpriceqty0 */
    //     newval=$("#"+this.id).val();
    //     objid=this.id.substr(12);
    //     if (newval=='') {
    //         $("input#old_specpriceval"+objid).val('');
    //         $("input#old_specamount"+objid).val('');
    //         $("div#amountrow"+objid).empty();
    //         $("input#profitval"+objid).val('');
    //         $("div#profitrow"+objid).empty();
    //         $("div#profperc"+objid).removeClass('white').removeClass('green').removeClass('orange').removeClass('red').removeClass('maroon').removeClass('black').empty();
    //     } else {
    //         newval=parseFloat(newval);
    //         if (isNaN(newval)) {
    //             $("input#"+this.id).val($("input#old_specpriceval"+objid).val());
    //             $.flash('Incorrect Value', {timeout:3000});
    //         } else {
    //             newval=newval.toFixed(2);
    //             $("input#"+this.id).val(newval);
    //             $("input#old_specpriceval"+objid).val(newval);
    //             recalc_specamount(objid);
    //         }
    //     }
    // })
    // $("div.savespecialcheckout").click(function(){
    //     save_specialcheckout();
    // })
}
