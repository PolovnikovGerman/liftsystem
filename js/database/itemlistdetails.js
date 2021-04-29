function init_itemlist_details_view() {
    $("#sliderlist").easySlider({
        nextText : '',
        prevText : '',
        vertical : false
    });
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
                $("#sliderlist").easySlider({
                    nextText : '',
                    prevText : '',
                    vertical : false
                });
                $(".displayprice").css('cursor','pointer');
                $(".template-checkbox").css('cursor','pointer');
                $(".implintdatavalue.sellopt").css('cursor','pointer');
                init_itemlist_details_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_itemlist_details_edit() {
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
    })
}

function prepare_edit() {
    var params = new Array();
    params.push({name: 'session_id', value: $("#dbdetailsession").val()});
    params.push({name: 'item_id', value: $("#dbdetailid").val()});
    params.push({name: 'brand', value: $("#dbdetailbrand").val()});
    return params;
}
