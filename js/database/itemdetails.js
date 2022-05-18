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
                $("#itemdetailsview").hide();
                $("#legacyview").show();
                init_legacy_page('itemcategoryview');
            } else {
                var start = $(".summenu_item:first").data('link');
                $("#itemdetailsview").hide();
                $("#legacyview").show();
                init_legacy_page(start);
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
            $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
            $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
            $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
            $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
            // $(".dbcontentarea").hide();
            $("#itemdetailsview").find('div.right_maincontent').empty().html(response.data.content);
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
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
            $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
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
                if (parseInt(response.data.profitview)==1) {
                    $("#profitdataview").empty().html(response.data.profitdat);
                }
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
    /* Autocomplete for Vendor Data */
    $("#vendor_name").autocompleter({
        source:'/itemdetails/search_vendor',
        minLength: 2,
        combine: function(params) {
            return {
                q: params.query
            };
        },
        callback: function(value, index, object) {
            if (object.id) {
                $("#vendor_item_vendor").val(object.id);
            }
        }
    });

    $("#vendor_name").blur(function(){
        var vendor_name=$("#vendor_name").val();
        params = new Array();
        params.push({name: 'vendor_name', value :vendor_name});
        params.push({name: 'session_id', value: $("#session_id").val()});
        $.post('/itemdetails/vendor_check',params,function(response){
            if (response.errors=='') {
                $("#vendor_item_vendor").val(response.data.vendor_id);
                if (parseInt(response.data.showvendor)==1) {
                    $("#vendor_name").val(response.data.vendor_name);
                    $("#vendor_item_number").val(response.data.vendor_item_number);
                    $("#vendor_item_number").attr('readonly',false);
                    $("input.vendorinputvalues[data-fld='vendor_item_zipcode']").val(response.data.vendor_item_zipcode);
                    $("textarea.vendorinputvalues[data-fld='vendor_item_notes']").val(response.data.vendor_item_notes);
                    $(".vendorprices").empty().html(response.data.vendorprices);
                }
            } else {
                show_error(response);
            }
        },'json');

        // $.post('/itemdetails/chk_vendor',{'name':vendor_name},function(data){
        //     $("#vendor_item_vendor").val(data.vendor_id);
        // },'json');
    });

    $("#vendor_item_number").autocompleter({
        source: '/itemdetails/search_vendor_item',
        minLength: 3,
        combine: function(params) {
            var vendor_id = $('#vendor_item_vendor').val();
            return {
                q: params.query,
                vendor_id: vendor_id
            };
        },
        callback: function(value, index, object) {
            if (object.id) {
                $("#vendor_item_id").val(object.id);
            }
        }
    });

    $("#vendor_item_number").blur(function(){
        // Check Item Number
        var vendor_it_number=$("#vendor_item_number").val();
        var params = new Array();
        params.push({name: 'number', value :vendor_it_number});
        params.push({name: 'session_id', value: $("#session_id").val()});
        $.post('/itemdetails/vendoritem_check',params,function(response){
            if (response.errors=='') {
                $("#vendor_item_vendor").val(response.data.vendor_item_vendor);
                $("#vendor_name").val(response.data.vendor_name);
                $("#vendor_item_number").val(response.data.vendor_item_number);
                $("#vendor_item_number").attr('readonly',false);
                $("input.vendorinputvalues[data-fld='vendor_item_zipcode']").val(response.data.vendor_item_zipcode);
                $("textarea.vendorinputvalues[data-fld='vendor_item_notes']").val(response.data.vendor_item_notes);
                $(".vendorprices").empty().html(response.data.vendorprices);
                if (parseInt(response.data.profitview)==1) {
                    $("#profitdataview").empty().html(response.data.profitdat);
                }

                // $("#vendor_item_name").val(response.data.vendor_item_name);
                // $("#vendor_item_name").attr('readonly',false)
                // $("#vendor_item_cost").val(response.data.vendor_item_cost);
                // $("#vendor_item_cost").attr('readonly',false);
                // $("#vendor_item_exprint").val(response.data.vendor_item_exprint);
                // $("#vendor_item_exprint").attr('readonly',false);
                // $("#vendor_item_setup").val(response.data.vendor_item_setup);
                // $("#vendor_item_setup").attr('readonly',false);
                // $("#vendor_item_notes").val(response.data.vendor_item_notes);
                // $("#vendor_item_notes").attr('readonly',false);

                // $("#vendor_name").val(response.data.vendor_name);
                // if (response.data.vendor_item_vendor=='') {
                // New Vendor - open for edit
                // $("#vendor_name").attr('readonly',false);
                // }
            } else {
                show_error(response);
            }
        },'json');
    });

    $(".vendorinputvalues").unbind('change').change(function() {
        var params = new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        params.push({name: 'entity', value: $(this).data('entity')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = "/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors == '') {
                if (parseInt(response.data.profitview)==1) {
                    $("#profitdataview").empty().html(response.data.profitdat);
                }
            } else {
                show_error(response);
            }
        }, 'json');
    });
    init_outstock_content();
}

function init_outstock_content() {
    $("#outstock").unbind('change').change(function () {
        var newval = 0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params=new Array();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: 'outstock'});
        params.push({name: 'newval', value: newval});
        params.push({name: 'idx', value: 0});
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval==0) {
                    $("div.itemoutstock_link").hide();
                } else {
                    $("div.itemoutstock_link").show();
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.itemoutstock_link").unbind('click').click(function(){
        var params = new Array();
        params.push({name: 'session_id', value: $("#session_id").val()});
        var url="/itemdetails/itemoutstock";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Out of Stock Banner and Link');
                $("#editModal").find('.modal-dialog').css('width','571px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_outstockbanner_upload();
                $("div.savebannercontent").find('img').unbind('click').click(function () {
                    save_outsockdetails();
                })
            } else {
                show_error(response);
            }
        }, 'json');
    });
}

function init_outstockbanner_upload() {
    var temp= '<div class="qq-uploader">' +
        '<div class="custom_upload"><span></span></div>' +
        '</div>';
    var uploader = new qq.FileUploader({
        element: document.getElementById('uploadbannersrc'),
        action: '/utils/save_itemimg',
        /* template: temp,            */
        uploadButtonText: '',
        multiple: false,
        debug: false,
        allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
        onComplete: function(id, fileName, responseJSON){
            $("li.qq-upload-success").hide();
            // $('.qq-uploader').fadeOut('100', function(){
            // $('div#deleteviewbuttons').fadeIn('200');
            // });
            $('input#newbannersrc').val(responseJSON.filename);
            $("div.viewpreloadbanner").empty().html("<img src='"+responseJSON.filename+"' />");
            $("div.saveimgupload").show();
        }
    });
}

function save_outsockdetails() {
    var params=new Array();
    params.push({name: 'outstock_banner', value: $("#newbannersrc").val()});
    params.push({name: 'outstock_link', value: $("#outstocklnk").val()});
    params.push({name: 'session_id', value: $("#session_id").val()});
    var url="/itemdetails/itemoutstock_save";
    $.post(url, params, function (resposnse) {
        if (resposnse.errors=='') {
            $("#editModal").modal('hide');
        } else {
            show_error(resposnse);
        }
    },'json');
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
        params.push({name: 'boxqty', value: $("#boxqty").val()});
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