function init_vendorpage() {
    initVendorPagination();
    $(".newvendor").unbind('click').click(function(){
        add_vendor();
    });
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
    var params = new Array();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagevend").val()});
    params.push({name: 'order_by', value: $("#orderbyvend").val()});
    params.push({name: 'direction', value: $("#directionvend").val()});
    params.push({name: 'maxval', value: $('#totalvend').val()});
    $("#loader").css('display','block');
    $.post('/database/vendordata', params, function(response){
        $("#loader").css('display','none');
        if (response.errors=='') {
            $("div#vendorinfo").empty().html(response.data.content);
            init_vendor_content();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}

function init_vendor_content() {
    $("input.vendpayincl").unbind('change').change(function(){
        var vendid=$(this).data('vendorid');
        var incl=0;
        if ($(this).prop('checked')==true) {
            incl=1;
        }
        var url="/database/vendor_includereport";
        $.post(url, {'vendor_id':vendid, 'payinclude':incl}, function(response){
            if (response.errors=='') {

            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".deletevend").unbind('click').click(function(){
        var vendor = $(this).data('vendor');
        del_vendor(vendor);
    });
    $(".editvendor").unbind('click').click(function(){
        var vendor = $(this).data('vendor');
        edit_vendor(vendor);
    });
}



function add_vendor() {
    var vendor_id=-1;
    var url="/database/vendor_edit";
    $.post(url,{'vendor_id':vendor_id},function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','625px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* Init save button */
            $("#savevendor").click(function(){
                save_vendor();
            });
        } else {
            show_error(response);
        }
    },'json');
}

function edit_vendor(vendor_id) {
    var url="/database/vendor_edit";
    $.post(url,{'vendor_id':vendor_id},function(response){
        if (response.errors=='') {
            $("#pageModal").find('div.modal-dialog').css('width','625px');
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* Init save button */
            $("#savevendor").click(function(){
                save_vendor();
            });
        } else {
            show_error(response);
        }
    },'json');
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