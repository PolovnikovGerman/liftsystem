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
            show_error(response);
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
    $(".closeitemdetails").unbind('click').click(function(){
        if (confirm('You realy want to exit without saving?')==true) {
            close_view();
        }
    });
    // Save
    $("div.saveedit_btn").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/save_itemdetails";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                close_view();
            } else {
                show_error(response);
            }
        },'json');

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
                $("#editModalLabel").empty().html('Edit Checkout Specials');
                $("#editModal").find('.modal-dialog').css('width','564px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal('show');
                init_specialcheck_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".bottomtxtlnk").click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url='/itemdetails/edit_footer';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Edit Bottom Text');
                $("#editModal").find('.modal-dialog').css('width','492px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal('show');
                init_bottomtext_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.shipcondinlink").click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url='/itemdetails/edit_shipping';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Edit Shipping Details');
                $("#editModal").find('.modal-dialog').css('width','468px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal('show');
                shipping_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".commontermslnk").click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url='/itemdetails/edit_commons';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                // editModal
                $("#editModalLabel").empty().html('Edit Common Terms');
                $("#editModal").find('.modal-dialog').css('width','352px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal('show');
                commonterms_manage();
            } else {
                show_error(response);
            }
        },'json');
    })
    // Imprint
    $("div.location_upload").unbind('click').click(function () {
        var imgsrc = $(this).data('srclink');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".location_del").unbind('click').click(function(){
        var title=$(this).data('title');
        if (confirm('Delete Imprint Location '+title+'?')==true) {
            var params=new Array();
            params.push({name: 'session_id', value: $("#session_id").val()});
            params.push({name: 'imprint_key', value: $(this).data('idx')});
            var url="/itemdetails/del_imprintlocation";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#imprintlocdata").empty().html(response.data.content);
                    init_itemdetails_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $("div.locationedit").unbind('click').click(function(){
        var params = new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'imprint_key', value: $(this).data('idx')});
        var url = "/itemdetails/edit_imprintlocation";
        $.post(url, params, function (response) {
            $("#editModalLabel").empty().html('Edit Imprint Location');
            $("#editModal").find('.modal-dialog').css('width','493px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
            itemlocation_manage();
        },'json');
    });
    $("input.editimprint").unbind('change').change(function(){
        var newval =0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params = new Array();
        params.push({name: 'entity', value: 'imprints'});
        params.push({name: 'fld', value: 'item_imprint_mostpopular'});
        params.push({name: 'newval', value: newval});
        params.push({name: 'idx', value: $(this).data('idx')});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".picture-none").each(function(){
        var img = $(this).prop('id');
        var item = $(this).data('idx');
        var uploader = new qq.FileUploader({
            element: document.getElementById(img),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session_id', value: $("#session_id").val()});
                    params.push({name: 'entity', value: 'item_images'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'src'});
                    params.push({name: 'idx', value: item});
                    var url="/itemdetails/change_parameter";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#pictures_slade").empty().html(response.data.content);
                            init_itemdetails_edit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    });
    $(".close-x").unbind('click').click(function(){
        if (confirm('Delete Image?')==true) {
            var params=new Array();
            params.push({name: 'session_id', value: $("#session_id").val()});
            params.push({name: 'idx', value: $(this).data('idx')});
            var url="/itemdetails/del_itemimage";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#pictures_slade").empty().html(response.data.content);
                    init_itemdetails_edit();
                } else {
                    show_error(response);
                }
            },'json');

        }
    });
    $(".pictures").each(function(){
        var img = $(this).find('div.pic').prop('id');
        var item = $(this).find('div.pic').data('idx');
        var uploader = new qq.FileUploader({
            element: document.getElementById(img),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session_id', value: $("#session_id").val()});
                    params.push({name: 'entity', value: 'item_images'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'src'});
                    params.push({name: 'idx', value: item});
                    var url="/itemdetails/change_parameter";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#pictures_slade").empty().html(response.data.content);
                            init_itemdetails_edit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    });
    $(".itempriceval").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'entity', value: 'item_prices'});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#profitdataview").empty().html(response.data.profitdat);
                var rowdat;
                for (var key in response.data.research) {
                    rowdat=response.data.research[key];
                    var cellid = rowdat.id;
                    var price=rowdat.price
                    var priceclass=rowdat.priceclass;
                    $("table.researchprices td#priceval"+cellid).empty().html(price).removeClass('empty_price').removeClass('white').removeClass('blue').removeClass('orange').removeClass('red').addClass(priceclass);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Simular
    $(".simularselect").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'entity', value: $(this).data('entity')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Item Options
    $(".itemdetaildatainput").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'entity', value: $(this).data('entity')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fld', value: $(this).data('fldname')});
        params.push({name: 'idx', value: $(this).data('fldid')});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_specialcheck_manage() {
    $("select.specialcheckout_selecttype").change(function(){
        var params=new Array();
        var newval = $(this).val();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#specialsession").val()});
        var url="/itemdetails/change_specialcheck_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval==1) {
                    $("div.specialcheckout_options").fadeIn(200);
                } else {
                    $("div.specialcheckout_options").fadeOut(200);
                }
                init_specialcheck_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.specialcheckout_checkbox").unbind('change').change(function(){
        var params=new Array();
        var newval = 0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: newval});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#specialsession").val()});
        var url="/itemdetails/change_specialcheck_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_specialcheck_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.specialsetupinpt").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#specialsession").val()});
        var url="/itemdetails/change_specialcheck_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_specialcheck_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".specpriceinput").unbind('change').change(function(){
        var params=new Array();
        var valueidx=$(this).data('idx');
        params.push({name: 'entity', value: 'prices'});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: $(this).data('idx')});
        params.push({name: 'session_id', value: $("#specialsession").val()});
        var url="/itemdetails/change_specialcheck_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                // Update profit
                // Update amount
                $("div.specialcheckoutprice_profitperc[data-idx='"+valueidx+"']").removeClass('white').removeClass('green').removeClass('orange').removeClass('red').removeClass('maroon').removeClass('black').empty().html(response.data.profit_percent).addClass(response.data.profit_class);
                $("div.specialcheckoutprice_profit[data-idx='"+valueidx+"']").empty().html(response.data.profit);
                $("div.specialcheckoutprice_amount[data-idx='"+valueidx+"']").empty().html(response.data.amount);
                init_specialcheck_manage();

            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.savespecialcheckout").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'specsession_id', value: $("#specialsession").val()});
        var url="/itemdetails/save_specialcheckout";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModal").modal('hide');
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_bottomtext_manage() {
    $(".itembottomtxt_save").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: 'bottom_text'});
        params.push({name: 'newval', value: $("#itmbottomedt").val()});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModal").modal('hide');
            } else {
                show_error(response);
            }
        },'json');
    })
}

function shipping_manage() {
    $("div.saveshipping").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'item_weigth', value: $("#item_weigth").val()});
        params.push({name: 'cartoon_qty', value: $("#cartoon_qty").val()});
        params.push({name: 'cartoon_width', value: $("#cartoon_width").val()});
        params.push({name: 'cartoon_heigh', value: $("#cartoon_heigh").val()});
        params.push({name: 'cartoon_depth', value: $("#cartoon_depth").val()});
        params.push({name: 'charge_pereach', value: $("#charge_pereach").val()});
        params.push({name: 'charge_perorder', value: $("#charge_perorder").val()});
        params.name({name: 'boxqty', value: $("#boxqty").val()});
        var url="/itemdetails/save_shipping";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModal").modal('hide');
            } else {
                show_error(response);
            }
        },'json');
    });
}

function commonterms_manage() {
    $("input.inputcommondata").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'commonsession', value: $("#commonsession").val()});
        params.push({name: 'idx', value: $(this).data('idx')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/itemdetails/change_commonterm";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".savecommonterms").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'commonsession', value: $("#commonsession").val()});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/save_commonterms";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModal").modal('hide');
            } else {
                show_error(response);
            }
        },'json');
    });
}

function itemlocation_manage() {
    $("input.imprintlocationedit").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'imprsession', value: $("#imprsession").val()});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/itemdetails/change_imprintlocation";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".savelocationload").show();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".delimprintview").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'imprsession', value: $("#imprsession").val()});
        params.push({name: 'fld', value: 'item_inprint_view'});
        params.push({name: 'newval', value: ''});
        var url="/itemdetails/change_imprintlocation";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".savelocationload").show();
                $("#imprintlocationviewarea").empty().html(response.data.content);
                $(".savelocationload").show();
                itemlocation_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    if ($("#newimprintlocationview").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('newimprintlocationview'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'imprsession', value: $("#imprsession").val()});
                    params.push({name: 'fld', value: 'item_inprint_view'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/itemdetails/change_imprintlocation";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#imprintlocationviewarea").empty().html(response.data.content);
                            $(".savelocationload").show();
                            itemlocation_manage();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".savelocationload").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'imprsession', value: $("#imprsession").val()});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/save_imprintlocation";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModal").modal('hide');
                $("#imprintlocdata").empty().html(response.data.content);
                init_itemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    })


}