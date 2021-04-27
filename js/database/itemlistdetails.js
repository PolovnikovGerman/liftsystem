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
    })

}

function prepare_edit() {
    var params = new Array();
    params.push({name: 'session_id', value: $("#dbdetailsession").val()});
    params.push({name: 'item_id', value: $("#dbdetailid").val()});
    params.push({name: 'brand', value: $("#dbdetailbrand").val()});
    return params;
}
