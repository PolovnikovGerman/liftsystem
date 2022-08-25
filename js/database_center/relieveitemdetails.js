function init_relievitemdetails_view(item) {
    $(".edit_itemdetails").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'item_id', value: item});
        params.push({name: 'brand', value: 'SR'});
        params.push({name: 'editmode', value: 1});
        var url = '/dbitems/relieve_item_edit';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModalLabel").empty().html(response.data.header);
                $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                init_relievitemdetails_edit(item);
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_relievitemdetails_edit(item) {
    $(".itemdetailsstatus").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'item_active'});
        params.push({name: 'newval', value: newval});
        var url='/dbitems/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(newval)==1) {
                    $(".itemdetailsstatus-value").empty().html('ACTIVE');
                } else {
                    $(".itemdetailsstatus-value").empty().html('INACTIVE');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemdetailstemplate").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'item_template'});
        params.push({name: 'newval', value: newval});
        var url='/dbitems/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $("select.itemdetailstemplate").addClass('missing_info');
                } else {
                    $("select.itemdetailstemplate").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemsubcategory").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'subcategory_id'});
        params.push({name: 'newval', value: newval});
        var url='/dbitems/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $("select.itemsubcategory").addClass('missing_info');
                } else {
                    $("select.itemsubcategory").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".tags-checkbox").unbind('click').click(function () {
        var fldname = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        var url='/dbitems/change_relive_checkbox';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".tags-checkbox[data-item='"+fldname+"']").empty().html('<i class="fa fa-check-square" aria-hidden="true"></i>');
                    $(".tags-checkbox-label[data-item='"+fldname+"']").addClass('active');
                } else {
                    $(".tags-checkbox[data-item='"+fldname+"']").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
                    $(".tags-checkbox-label[data-item='"+fldname+"']").removeClass('active');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.itemkeyinfoinput").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/dbitems/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (fldname=='bullet1' || fldname=='bullet2' || fldname=='bullet3' || fldname=='bullet4') {
                } else {
                    if (newval=='') {
                        $("input.itemkeyinfoinput[data-item='"+fldname+"']").addClass('missing_info');
                    } else {
                        $("input.itemkeyinfoinput[data-item='"+fldname+"']").removeClass('missing_info');
                    }
                }
            } else {
                show_error(response);
            }
        },'json');
    })
    $(".itemdescription").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'item_description1';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/dbitems/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $(".itemdescription").addClass('missing_info');
                } else {
                    $(".itemdescription").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
}