function init_vendorpage() {
    initVendorPagination();
    $(".addnewvendor").live('click',function(){
        edit_vendor(-1);
    });
    init_vendor_search();
}

function init_vendor_search() {
    $("#filterdata").unbind('change').change(function () {
        search_vendors();
    });
    $(".datasearchbtn").unbind('click').click(function () {
        search_vendors();
    });
    $(".datacleanbtn").unbind('click').click(function () {
        $("#vedorsearch").val('');
        search_vendors();
    });
    $("#vedorsearch").keypress(function(event){
        if (event.which == 13) {
            search_vendors();
        }
    });
    $(".vendordataview .datatitle").find(".sortable").unbind('click').click(function () {
        var fld=$(this).data('sortcell');
        sort_vendorlist(fld);
    });
}

function search_vendors() {
    var params = prepare_list_filter();
    var url='/database/vendor_search';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#totalvend").val(response.data.totals);
            $(".totaldata").empty().html(response.data.total_txt);
            $("#curpagevend").val(0);
            initVendorPagination();
        } else {
            show_error(response);
        }
    },'json');
}

function sort_vendorlist(fld) {
    var cursort = $('#orderbyvend').val();
    var curdirec = $('#directionvend').val();
    if (cursort==fld) {
        // Change direction
        if (curdirec=='asc') {
            $(".vendordataview .datatitle").find("div.ascsort").remove();
            $(".vendordataview .datatitle").find('div[data-sortcell="'+fld+'"]').append('<div class="descsort">&nbsp;</div>');
            $('#directionvend').val('desc');
        } else {
            $(".vendordataview .datatitle").find("div.descsort").remove();
            $(".vendordataview .datatitle").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
            $('#directionvend').val('asc');
        }
    } else {
        $(".vendordataview .datatitle").find("div.ascsort").remove();
        $(".vendordataview .datatitle").find("div.descsort").remove();
        $(".vendordataview .datatitle").find("div[data-sortcell='"+cursort+"']").removeClass('active');
        $(".vendordataview .datatitle").find("div[data-sortcell='"+fld+"']").addClass('active');
        $(".vendordataview .datatitle").find('div[data-sortcell="'+fld+'"]').append('<div class="ascsort">&nbsp;</div>');
        $('#directionvend').val('asc');
        $('#orderbyvend').val(fld);
    }
    var pageindex = $('#curpagevend').val();
    pageVendorCallback(pageindex);

}

function initVendorPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalvend').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpagevend").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#vendorPagination").empty();
        pageVendorCallback(0);
    } else {
        var curpage = $("#curpageart").val();
        // Create content inside pagination element
        $("#vendorPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageVendorCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageVendorCallback(page_index) {
    // var perpage = itemsperpage;
    var params = prepare_list_filter();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagevend").val()});
    params.push({name: 'order_by', value: $("#orderbyvend").val()});
    params.push({name: 'direction', value: $("#directionvend").val()});
    params.push({name: 'maxval', value: $('#totalvend').val()});
    $("#loader").show();
    $.post('/database/vendordata', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div#vendorinfo").empty().html(response.data.content);
            init_vendor_content();
        } else {
            $("#loader").hide();
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}

function prepare_list_filter() {
    var params = new Array();
    params.push({name: 'vendor_status', value: $("#filterdata").val()});
    params.push({name: 'search', value: $("#vedorsearch").val()});
    return params;
}

function init_vendor_content() {
    // $("input.vendpayincl").unbind('change').change(function(){
    //     var vendid=$(this).data('vendorid');
    //     var incl=0;
    //     if ($(this).prop('checked')==true) {
    //         incl=1;
    //     }
    //     var url="/database/vendor_includereport";
    //     $.post(url, {'vendor_id':vendid, 'payinclude':incl}, function(response){
    //         if (response.errors=='') {
    //
    //         } else {
    //             show_error(response);
    //         }
    //     }, 'json');
    // });
    // $(".deletevend").unbind('click').click(function(){
    //     var vendor = $(this).data('vendor');
    //     del_vendor(vendor);
    // });
    $(".vendordataview").find("div.editdata").unbind('click').click(function(){
        var vendor = $(this).data('vendor');
        edit_vendor(vendor);
    });
}


function edit_vendor(vendor_id) {
    var url="/database/vendor_edit";
    $.post(url,{'vendor_id':vendor_id},function(response){
        if (response.errors=='') {            
            $("#vendorDetailsModalLabel").empty().html(response.data.header);
            $("#vendorDetailsModal").find('div.modal-body').empty().html(response.data.content);
            $("#vendorDetailsModal").find('div.modal-dialog').css('width','1333px');
            $("#vendorDetailsModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(response.data.editmode)==0) {
                init_vendordetails_view();
            } else {
                init_vendordetails_edit(); 
            }
        } else {
            show_error(response);
        }
    },'json');
}

function init_vendordetails_view() {
    $(".vendoractivatetbtn").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'vendor_id', value: $("#vendorid").val()});
        params.push({name: 'editmode', value: 1});
        var url="/database/vendor_edit";
        $.post(url,params,function(response) {
            if (response.errors=='') {
                $("#vendorDetailsModalLabel").empty().html(response.data.header);
                $("#vendorDetailsModal").find('div.modal-body').empty().html(response.data.content);
                init_vendordetails_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    })
}

function init_vendordetails_edit() {
    $(".vendorsaveactionbtn").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        var url = '/database/vendordata_save';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#vendorDetailsModal").modal('hide');
                search_vendors();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendorstatusbtn").unbind('click').click(function () {
        var newstatus = 1;
        if ($(this).hasClass('active')) {
            newstatus = 0;
        }
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: 'vendor_status'});
        params.push({name: 'newval', value: newstatus});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newstatus==1) {
                    $(".vendorstatusbtn").removeClass('inactive').addClass('active');
                    $(".vendorstatusbtn").empty().html('Active');
                } else {
                    $(".vendorstatusbtn").removeClass('active').addClass('inactive');
                    $(".vendorstatusbtn").empty().html('Inactive');
                }
            } else {
                show_error(response)
            }
        },'json');
    });
    $("input.vendordetailsinpt").unbind('change').change(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {

            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea.vendordetailsinpt").unbind('change').change(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {

            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.vendordetailsselect").unbind('change').change(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {

            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendorparamcheck").unbind('click').click(function(){
        var item=$(this).data('item');
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: item});
        var url='/vendors/update_vendor_check';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendorparamcheck[data-item='"+item+"']").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Contacts
    $(".vendorcontactinpt").unbind('change').change(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor_contacts'});
        params.push({name: 'fld', value: $(this).data('field')});
        params.push({name: 'idx', value: $(this).data('idx')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {

            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendorcontactcheck").unbind('click').click(function () {
        var field = $(this).data('field');
        var idx = $(this).data('idx');
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor_contacts'});
        params.push({name: 'fld', value: field});
        params.push({name: 'idx', value: idx});
        var url='/vendors/update_vendor_check';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendorcontactcheck[data-field='"+field+"'][data-idx='"+idx+"']").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add contacts
    $(".vendorcontactadd").unbind('click').click(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'manage', value: 'add'});
        var url = '/vendors/vendor_contact_manage';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#vendorcontacts").empty().html(response.data.content);
                init_vendordetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Remove contacts
    $(".removevendorcontact").unbind('click').click(function () {
        if (confirm('Remove contact ?')==true) {
            var params = prepare_vendor_edit();
            params.push({name: 'manage', value: 'del'});
            params.push({name: 'idx', value: $(this).data('idx')});
            var url = '/vendors/vendor_contact_manage';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#vendorcontacts").empty().html(response.data.content);
                    init_vendordetails_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Docs
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background-image: none; width: 90px; color: #ffffff;top: -25px;left: 36px;"><i class="fa fa-plus-circle" aria-hidden="true"></i><span>Add</span></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({

        element: document.getElementById('vendordocadd'),
        action: '/utils/vendorcenterattach',
        uploadButtonText: '',
        multiple: false,
        debug: false,
        template: upload_templ,
        allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG','pdf','PDF'],
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                $("li.qq-upload-success").hide();
                var params=prepare_vendor_edit();
                params.push({name: 'entity', value: 'vendor_docs'});
                params.push({name: 'newval', value: responseJSON.filename});
                params.push({name: 'srcname', value: responseJSON.source});
                params.push({name: 'manage', value: 'add'});
                var url="/vendors/vendor_doc_manage";
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $("#vendordocuments").empty().html(response.data.content);
                        init_vendordetails_edit();
                    } else {
                        show_error(response);
                    }
                },'json');
            }
        }
    });
    $("input.vendordocuminpt").unbind('change').change(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor_docs'});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'idx', value: $(this).data('idx')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {

            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendordocremove").unbind('click').click(function () {
        var params=prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor_docs'});
        params.push({name: 'manage', value: 'del'});
        params.push({name: 'idx', value: $(this).data('idx')});
        var url="/vendors/vendor_doc_manage";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#vendordocuments").empty().html(response.data.content);
                init_vendordetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    })

}

function prepare_vendor_edit() {
    var params = new Array();
    params.push({name: 'session', value: $("#session").val()});
    return params;
}

function save_vendor() {
    var dat=$("#vendordat").serializeArray();
    var url="/database/vendordata_save";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
            initVendorPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}


function del_vendor(vendor_id) {
    if (confirm("You realy want to delete vendor?")) {
        var url="/database/vendor_remove";
        $.post(url, {'vendor_id':vendor_id}, function(response){
            if (response.errors=='') {
                $("#totalvend").val(response.data.totals);
                initVendorPagination();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}