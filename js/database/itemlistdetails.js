function init_itemlist_details_view() {
    image_slider_init();
    $(".implintdatavalue.vectorfile").unbind('click').click(function () {
        var imgurl = $(this).data('link');
        openai(imgurl, 'Vector Image');
    });

    $(".itemlistactivatetbtn").unbind('click').click(function(){
        var params = prepare_edit();
        params.push({name: 'editmode', value: 1});
        var url="/database/itemlistdetails";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModalLabel").empty().html(response.data.header);
                $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                // First init
                image_slider_init();
                $(".displayprice").css('cursor','pointer');
                $(".template-checkbox").css('cursor','pointer');
                $(".implintdatavalue.sellopt").css('cursor','pointer');
                init_vectorfile_upload();
                init_itemlist_details_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".inprintdataview").unbind('click').click(function () {
        var imgsrc = $(this).data('viewurl');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
}

function image_slider_init() {
    $("#sliderlist").easySlider({
        nextText : '',
        prevText : '',
        vertical : false
    });
}

function init_vectorfile_upload() {
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background-image: none; width: 90px;">click to open</div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('newvecorfile'),
        action: '/utils/save_vectorfile',
        uploadButtonText: '',
        multiple: false,
        debug: false,
        template: upload_templ,
        allowedExtensions: ['ai', 'AI'],
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                $("li.qq-upload-success").hide();
                var params=prepare_edit();
                params.push({name: 'entity', value: 'item'});
                params.push({name: 'fld', value: 'item_vector_img'});
                params.push({name: 'newval', value: responseJSON.filename});
                var url = '/dbitemdetails/change_parameter';
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                    } else {
                        show_error(response);
                    }
                },'json');
            }
        }
    });
}

function init_itemlist_details_edit() {
    // Save 
    $(".itemlistsaveactionbtn").unbind('click').click(function () {
        var brand = $("#dbdetailbrand").val();
        var params = prepare_edit();
        var url = '/dbitemdetails/save_itemdetails';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModal").modal('hide');
                initItemsListPagination(brand);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemstatusbtn").unbind('click').click(function () {
        var status = 1;
        if ($(this).hasClass('active')) {
            status = 0;
        }
        var params = prepare_edit();
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: 'item_active'});
        params.push({name: 'newval', value: status});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (parseInt(status)==0) {
                    $(".itemstatusbtn").removeClass('active').addClass('inactive').empty().html('Inactive');
                } else {
                    $(".itemstatusbtn").removeClass('inactive').addClass('active').empty().html('Active');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.itemlistdetailsinpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
                $("input.itemlistdetailsinpt[data-item='"+fldname+"']").val(response.data.oldvalue).focus();
            }
        },'json');
    });
    $("textarea.itemlistdetailsinpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
                $("textarea.itemlistdetailsinpt[data-item='"+fldname+"']").val(response.data.oldvalue).focus();
            }
        },'json');

    });
    $('.template-checkbox').unbind('click').click(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: 0});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".template-checkbox[data-item='"+fldname+"']").empty().html(response.data.newcheck);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.simulardataselect").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'similar'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: $(this).data('idx')})
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("#vendor_name").autocompleter({
        source:'/dbitemdetails/searchvendor',
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
        params = prepare_edit();
        params.push({name: 'vendor_name', value :vendor_name});
        $.post('/dbitemdetails/vendor_check',params,function(response){
            if (response.errors=='') {
                if (parseInt(response.data.showvendor)==1) {
                    $("#vendordataviewarea").empty().html(response.data.vendor_view);
                    $(".vendordatainpt[data-item='vendor_item_zipcode']").focus();
                    init_itemlist_details_edit();
                }
            } else {
                show_error(response);
            }
        },'json');
    });

    $("#vendor_item_number").autocompleter({
        source: '/dbitemdetails/search_vendor_item',
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

    $("#vendor_item_number").blur(function() {
        // Check Item Number
        var vendor_item_number = $("#vendor_item_number").val();
        params = prepare_edit();
        params.push({name: 'number', value: vendor_item_number});
        $.post('/dbitemdetails/vendoritem_check',params,function(response){
            if (response.errors=='') {
                $("#vendordataviewarea").empty().html(response.data.vendor_view);
                $(".vendordatainpt[data-item='vendor_item_name']").focus();
                init_itemlist_details_edit();
            } else {
                show_error(response);
            }
        },'json');
    });

    $(".vendordatainpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'vendor_item'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendorpriceinpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        if (fldname=='vendor_item_exprint' || fldname=='vendor_item_setup') {
            params.push({name: 'entity', value: 'vendor_specprice'});
        }  else {
            params.push({name: 'entity', value: 'vendor_price'});
        }
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = '/dbitemdetails/change_price';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#profitareaview").empty().html(response.data.profit_view);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".pricevalinpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        if (fldname=='item_sale_setup' || fldname=='item_sale_print') {
            params.push({name: 'entity', value: 'item_specprice'});
        } else {
            params.push({name: 'entity', value: 'item_price'});
        }
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = '/dbitemdetails/change_price';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#profitareaview").empty().html(response.data.profit_view);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".shipvalinpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'shipping'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".displayprice").unbind('click').click(function () {
        var params=prepare_edit();
        var curidx = $(this).data('idx');
        params.push({name: 'entity', value: 'priceshow'});
        params.push({name: 'fld', value: 'show_first'});
        params.push({name: 'newval', value: 1});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                // Remove old checked
                $(".displayprice").empty().html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
                $(".displayprice[data-idx='"+curidx+"']").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemcolorinpt").unbind('change').change(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'colors'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Print type
    $(".implintdatavalue.sellopt").unbind('click').click(function () {
        var params=prepare_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'item'});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: 1});
        var url = '/dbitemdetails/change_parameter';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".implintdatavalue.sellopt[data-item='"+fldname+"']").empty().html(response.data.newcheck);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Imprint
    $(".inprintdataview").unbind('click').click(function () {
        var imgsrc = $(this).data('viewurl');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".delimprint").unbind('click').click(function(){
        if (confirm('Do you want to remove Print Area?')==true) {
            var params=prepare_edit();
            params.push({name: 'idx', value: $(this).data('idx') });
            var url  = "/dbitemdetails/remove_inprint";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".imprintcontent").empty().html(response.data.content);
                    init_itemlist_details_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $(".newimprintloaction").unbind('click').click(function () {
        var params=prepare_edit();
        params.push({name: 'idx', value: 0});
        var url = '/dbitemdetails/inprint_prepare';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Edit Imprint Location');
                $("#editModal").find('.modal-dialog').css('width','493px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                // $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
                dbitemlocation_manage();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".inprintdatanameedit").unbind('click').click(function () {
        var params=prepare_edit();
        params.push({name: 'idx', value: $(this).data('idx')});
        var url = '/dbitemdetails/inprint_prepare';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Edit Imprint Location');
                $("#editModal").find('.modal-dialog').css('width','493px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                // $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
                dbitemlocation_manage();
            } else {
                show_error(response);
            }
        }, 'json');
    })
    if ($("#newadvimage").length > 0 ) {
        var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background-image: none; width: 90px;">empty</div>' +
            '<ul class="qq-upload-list"></ul>' +
            '<ul class="qq-upload-drop-area"></ul>'+
            '<div class="clear"></div></div>';
        var uploader = new qq.FileUploader({
            element: document.getElementById('newadvimage'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            /* template: upload_templ, */
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=prepare_edit();
                    params.push({name: 'fld', value: 'printlocat_example_img'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'entity', value: 'item'});
                    var url="/dbitemdetails/change_parameter";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".advinfoarea").empty().html(response.data.content);
                            init_itemlist_details_edit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".deladvimage").unbind('click').click(function () {
        var params=prepare_edit();
        params.push({name: 'fld', value: 'printlocat_example_img'});
        params.push({name: 'newval', value: ''});
        params.push({name: 'entity', value: 'item'});
        var url="/dbitemdetails/change_parameter";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".advinfoarea").empty().html(response.data.content);
                init_itemlist_details_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Pictures
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
                    var params=prepare_edit();
                    params.push({name: 'entity', value: 'item_images'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'src'});
                    params.push({name: 'idx', value: item});
                    var url="/dbitemdetails/change_pictures";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".pictureslderarea").empty().html(response.data.content);
                            image_slider_init();
                            init_itemlist_details_edit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    });
    $(".remove-slideimage").unbind('click').click(function () {
        if (confirm('Remove Image?')==true) {
            var params=prepare_edit();
            params.push({name: 'entity', value: 'item_images'});
            params.push({name: 'idx', value: $(this).data('idx')});
            var url="/dbitemdetails/delete_picture";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".pictureslderarea").empty().html(response.data.content);
                    image_slider_init();
                    init_itemlist_details_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Sort images
    $(".imagesortarea").unbind('click').click(function () {
        var params=prepare_edit();
            var url = "/dbitemdetails/sort_picture_prepare";
        $.post(url, params, function (response) {
            $("#editModalLabel").empty().html('Change Sequence');
            $("#editModal").find('.modal-dialog').css('width','493px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            // $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#editModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#imagesortcontent").sortable({
                draggable: '.itemimagesort',
                dataIdAttr: 'data-idx'
            });
            $(".savesort").unbind('click').click(function () {
                var params = prepare_edit();
                var name='';
                var numpp=1;
                var idx='';
                $("#imagesortcontent").find('div.itemimagesort').each(function(){
                    idx = $(this).data('idx');
                    name='sort_'+numpp;
                    params.push({name: name, value: idx});
                    numpp = numpp + 1;
                });
                var url = 'dbitemdetails/sort_picture_save';
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $(".pictureslderarea").empty().html(response.data.content);
                        image_slider_init();
                        init_itemlist_details_edit();
                        $("#editModal").modal('hide');
                    } else {
                        show_error(response);
                    }
                },'json');
            })
            // dbitemlocation_manage();
        },'json');
    });
}

function dbitemlocation_manage() {
    $("input.inprintlocationedit").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'imprsession', value: $("#imprsession").val()});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/dbitemdetails/change_imprintlocation";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".savelocationedit").show();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".delinprintview").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'imprsession', value: $("#imprsession").val()});
        params.push({name: 'fld', value: 'item_inprint_view'});
        params.push({name: 'newval', value: ''});
        var url="/dbitemdetails/change_imprintlocation";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".savelocationedit").show();
                $("#imprintlocationviewarea").empty().html(response.data.content);
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
                    var url="/dbitemdetails/change_imprintlocation";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#imprintlocationviewarea").empty().html(response.data.content);
                            $(".savelocationedit").show();
                            itemlocation_manage();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".savelocationedit").unbind('click').click(function () {
        var params=prepare_edit();
        params.push({name: 'imprsession', value: $("#imprsession").val()});
        var url="/dbitemdetails/save_imprintlocation";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModal").modal('hide');
                $(".imprintcontent").empty().html(response.data.content);
                init_itemlist_details_edit();
            } else {
                show_error(response);
            }
        },'json');
    })
}


function prepare_edit() {
    var params = new Array();
    params.push({name: 'session_id', value: $("#dbdetailsession").val()});
    params.push({name: 'item_id', value: $("#dbdetailid").val()});
    params.push({name: 'brand', value: $("#dbdetailbrand").val()});
    return params;
}
