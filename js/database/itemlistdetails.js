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
}

function prepare_edit() {
    var params = new Array();
    params.push({name: 'session_id', value: $("#dbdetailsession").val()});
    params.push({name: 'item_id', value: $("#dbdetailid").val()});
    params.push({name: 'brand', value: $("#dbdetailbrand").val()});
    return params;
}
