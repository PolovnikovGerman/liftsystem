function init_vendorpage() {
    var docwidth = parseInt($(document).width());
    if (docwidth > 992) {
        $("#devicetype").val('desktop');
    }
    initVendorPagination();
    $(".addnewvendor").unbind('click').click(function(){
        edit_vendor(-1);
    });
    init_vendor_search();
}

function init_vendor_search() {
    $("#filterdata").unbind('change').change(function () {
        search_vendors('mobile');
    });
    $("#filtertype").unbind('change').change(function () {
        search_vendors('mobile');
    });
    $(".datasearchbtn").unbind('click').click(function () {
        search_vendors('mobile');
    });
    $(".datacleanbtn").unbind('click').click(function () {
        $("#vedorsearch").val('');
        search_vendors('mobile');
    });
    $("#vedorsearch").keypress(function(event){
        if (event.which == 13) {
            search_vendors('mobile');
        }
    });
    $("#filterdata_desktop").unbind('change').change(function () {
        search_vendors('desktop');
    });
    $("#filtertype_desktop").unbind('change').change(function () {
        search_vendors('desktop');
    });
    $(".datasearchbtn_desktop").unbind('click').click(function () {
        search_vendors('desktop');
    });
    $(".datacleanbtn_desktop").unbind('click').click(function () {
        $("#vedorsearch_desktop").val('');
        search_vendors('desktop');
    });
    $("#vedorsearch_desktop").keypress(function(event){
        if (event.which == 13) {
            search_vendors('desktop');
        }
    });
    $(".vendordataview .datatitle").find(".sortable").unbind('click').click(function () {
        var fld=$(this).data('sortcell');
        sort_vendorlist(fld);
    });
}

function search_vendors(searchtype) {
    var params = prepare_list_filter(searchtype);
    var url='/vendors/vendor_search';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#totalvend").val(response.data.totals);
            $(".totaldata").empty().html(response.data.total_txt);
            $("#curpagevend").val(0);
            initVendorPagination(searchtype);
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
    if ($("#devicetype").val()=='desktop') {
        pageVendorCallbackDesktop(pageindex);
    } else {
        pageVendorCallbackMobile(pageindex);
    }
}

function initVendorPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalvend').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpagevend").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#vendorPagination").empty();
        pageVendorCallbackMobile(0);
    } else {
        var curpage = $("#curpageart").val();
        // Create content inside pagination element
        $("#vendorPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageVendorCallbackMobile,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function initVendorPaginationMobile() {
    // count entries inside the hidden content
    var num_entries = $('#totalvend').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpagevend").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#vendorPagination").empty();
        pageVendorCallbackMobile(0);
    } else {
        var curpage = $("#curpageart").val();
        // Create content inside pagination element
        $("#vendorPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageVendorCallbackMobile,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function initVendorPaginationDesktop() {
    // count entries inside the hidden content
    var num_entries = $('#totalvend').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpagevend").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#vendorPagination_desktop").empty();
        pageVendorCallbackDesktop(0);
    } else {
        var curpage = $("#curpageart").val();
        // Create content inside pagination element
        $("#vendorPagination_desktop").mypagination(num_entries, {
            current_page: curpage,
            callback: pageVendorCallbackDesktop,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageVendorCallbackMobile(page_index) {
    // var perpage = itemsperpage;
    var params = prepare_list_filter('mobile');
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagevend").val()});
    params.push({name: 'order_by', value: $("#orderbyvend").val()});
    params.push({name: 'direction', value: $("#directionvend").val()});
    params.push({name: 'maxval', value: $('#totalvend').val()});
    $("#loader").show();
    $.post('/vendors/vendordata', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div#vendorinfo").empty().html(response.data.content);
            $("#vendorinfo").find("div.dataarea").scrollpanel({
                'prefix' : 'sp-'
            });
            init_vendor_content();
            leftmenu_alignment();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function pageVendorCallbackDesktop(page_index) {
    // var perpage = itemsperpage;
    var params = prepare_list_filter('desktop');
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagevend").val()});
    params.push({name: 'order_by', value: $("#orderbyvend").val()});
    params.push({name: 'direction', value: $("#directionvend").val()});
    params.push({name: 'maxval', value: $('#totalvend').val()});
    $("#loader").show();
    $.post('/vendors/vendordata', params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div#vendorinfo").empty().html(response.data.content);
            init_vendor_content();
            leftmenu_alignment();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function prepare_list_filter(searchtype) {
    var params = new Array();
    if (searchtype=='mobile') {
        params.push({name: 'vendor_status', value: $("#filterdata").val()});
        params.push({name: 'search', value: $("#vedorsearch").val()});
        params.push({name: 'vtype', value: $("#filtertype").val()});
    } else {
        params.push({name: 'vendor_status', value: $("#filterdata_desktop").val()});
        params.push({name: 'search', value: $("#vedorsearch_desktop").val()});
        params.push({name: 'vtype', value: $("#filtertype_desktop").val()});
    }
    return params;
}

function init_vendor_content() {
    $(".vendordataview").find('div.datarow').hover(
        function () {
            $(this).addClass('activerow');
        },
        function () {
            $(this).removeClass('activerow');
        }
    );
    $(".vendordataview").find("div.datarow").unbind('click').click(function(){
        var vendor = $(this).data('vendor');
        edit_vendor(vendor);
    });
    $(".vendorwebsiteshow").unbind('click').click(function () {
        var url = $(this).data('weburl');
        // window.open('http:://www.alpi.net','_blank');
        // window.open(url,'_blank');
        // window.open(url);
        openInNewTab("//"+url);
    })
}

function openInNewTab(href) {
    Object.assign(document.createElement('a'), {
        target: '_blank',
        href: href,
    }).click();
}

function edit_vendor(vendor_id) {
    var url="/vendors/vendor_edit";
    $.post(url,{'vendor_id':vendor_id},function(response){
        if (response.errors=='') {
            $("#vendorDetailsModal").find('div.modal-header').removeClass('editmode');
            /* $("#mobileheadercontent").empty().html(response.data.mobheader); */
            $("#vendorDetailsModalLabel").empty().html(response.data.header);
            $("#vendorDetailsModal").find('div.modal-body').empty().html(response.data.content);
            // $("#vendorDetailsModal").find('div.modal-dialog').css('max-width','1333px');
            $("#vendorDetailsModal").modal({keyboard: false, show: true});
            if (parseInt(response.data.editmode)==0) {
                $("#vendorDetailsModal").find('div.modal-header').addClass(response.data.status);
                init_vendordetails_view();
            } else {
                $("#vendorDetailsModal").find('div.modal-header').addClass('editmode');
                init_vendordetails_edit(); 
            }
        } else {
            show_error(response);
        }
    },'json');
}

function init_vendordetails_view() {
    $(".mob-vendoractivatetbtn").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'vendor_id', value: $("#vendorid").val()});
        params.push({name: 'editmode', value: 1});
        var url="/vendors/vendor_edit";
        $.post(url,params,function(response) {
            if (response.errors=='') {
                $("#vendorDetailsModalLabel").empty().html(response.data.header);
                $("#mobileheadercontent").empty().html(response.data.mobheader);
                $("#vendorDetailsModal").find('div.modal-body').empty().html(response.data.content);
                $("#vendorDetailsModal").find('div.modal-header').addClass('editmode');
                init_vendordetails_edit();
                initAddressAutocomplete();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".vendoractivatetbtn").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'vendor_id', value: $("#vendorid").val()});
        params.push({name: 'editmode', value: 1});
        var url="/vendors/vendor_edit";
        $.post(url,params,function(response) {
            if (response.errors=='') {
                $("#vendorDetailsModalLabel").empty().html(response.data.header);
                $("#vendorDetailsModal").find('div.modal-body').empty().html(response.data.content);
                $("#vendorDetailsModal").find('div.modal-header').addClass('editmode');
                init_vendordetails_edit();
                initAddressAutocomplete();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".vendorweburl").unbind('click').click(function () {
        var url = $(this).data('weburl');
        openInNewTab("//"+url);
    })
    $(".pricedoc_icon").unbind('click').click(function () {
        var docurl = $(this).data('file');
        var docsrc = $(this).data('source');
        openai(docurl, docsrc);
    });
    $(".historicpricedoc_icon").unbind('click').click(function () {
        var docurl = $(this).data('file');
        var docsrc = $(this).data('source');
        openai(docurl, docsrc);
    });
    $(".pricedocs_view").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'full'});
        var url = '/vendors/show_pricelist_history';
        $.post(url, params, function (reponse) {
            if (reponse.errors=='') {
                $(".vendordetails-section.customserviceview").hide();
                $(".vendordetails-section.documentsview").hide();
                $(".docspricelistsarea").empty().html(reponse.data.content);
                init_vendordetails_view();
            } else {
                show_error(reponse);
            }
        },'json');
    });
    $(".hidepricelists").unbind('click').click(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'short'});
        var url = '/vendors/show_pricelist_history';
        $.post(url, params, function (reponse) {
            if (reponse.errors=='') {
                $(".docspricelistsarea").empty().html(reponse.data.content);
                $(".vendordetails-section.customserviceview").show();
                $(".vendordetails-section.documentsview").show();
                init_vendordetails_view();
            } else {
                show_error(reponse);
            }
        },'json');
    });
    $(".documentlist").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'full'});
        var url = '/vendors/show_otherdocs_history';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendordetails-section.pricesview").hide();
                $(".vendordetails-section.customserviceview").hide();
                $(".vendordocument_value").empty().html(response.data.content);
                init_vendordetails_view();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".hidedocumentslists").unbind('click').click(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'short'});
        var url = '/vendors/show_otherdocs_history';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendordetails-section.pricesview").show();
                $(".vendordetails-section.customserviceview").show();
                $(".vendordocument_value").empty().html(response.data.content);
                init_vendordetails_view();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".checkoutlockdata").unbind('click').click(function () {
        $(".checkoutlockdata").hide();
        $(".checkoutunlockdata").show();
    })
    $(".checkoutunlockdata").unbind('click').click(function () {
        $(".checkoutunlockdata").hide();
        $(".checkoutlockdata").show();
    });
}

function init_vendordetails_edit() {
    $(".vendorsaveactionbtn").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        var url = '/vendors/vendordata_save';
        $(".vendorvaluearea").find('fieldset').removeClass('error');
        $(".vendorvaluearea").find('.vendormandatoryfld').hide();
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#vendorDetailsModal").modal('hide');
                search_vendors();
            } else {
                show_errorfld(response);
            }
        },'json');
    });
    $(".mob-vendorsaveactionbtn").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        var url = '/vendors/vendordata_save';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#vendorDetailsModal").modal('hide');
                search_vendors();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendorchangemode").unbind('click').click(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: 'vendor_status'});
        var url='/vendors/update_vendor_param';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendorchangemode").empty().html(response.data.status_label);
            } else {
                show_error(response);
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
    $("input.vendordetailsphone").focusin(function() {
        var phone = $(this).val();
        var edtphone = phone.replaceAll('-','');
        $( this ).val(edtphone);
    });
    $("input.vendordetailsphone").focusout(function () {
        var params = prepare_vendor_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_phone';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("input[data-item='"+fldname+"']").val(response.data.newval);
            } else {
                show_error(response);
            }
        },'json');
    })
    $("input.vendordetailsphone").unbind('change').change(function () {
        var params = prepare_vendor_edit();
        var fldname = $(this).data('item');
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/vendors/update_vendor_phone';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("input[data-item='"+fldname+"']").val(response.data.newval);
            } else {
                show_error(response);
            }
        },'json');
    })
    $("input.vendoraddress").unbind('change').change(function () {
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
    })
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
                $(".vedorpaymentmethod[data-item='"+item+"']").removeClass('checked').addClass(response.data.class);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Radio
    $(".vendorparam_icon").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'entity', value: 'vendor'});
        params.push({name: 'fld', value: $(this).data('item')});
        var url='/vendors/update_vendor_radio';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vedorpaymentterm[data-item='payment_prepay']").removeClass('checked').addClass(response.data.prepay_class);
                $(".vendorparam_icon[data-item='payment_prepay']").empty().html(response.data.prepay_content);
                $(".vedorpaymentterm[data-item='payment_terms']").removeClass('checked').addClass(response.data.term_class);
                $(".vendorparam_icon[data-item='payment_terms']").empty().html(response.data.term_content);
            } else {
                show_error(response);
            }
        },'json');
    })
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
    $(".addnewpricedoc").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'doctype', value: 'pricelist'});
        var url='/vendors/vendordoc_upload_prepare';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Please upload a new pricing doc');
                $("#editModal").find('.modal-dialog').css('width', '300px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal({show: true, keyboard: false });
                init_venorprice_upload();
                $('#editModal').on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".pricedocs_view").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'full'});
        var url = '/vendors/show_pricelist_history';
        $.post(url, params, function (reponse) {
            if (reponse.errors=='') {
                $(".vendordetails-section.customserviceview").hide();
                $(".vendordetails-section.documentsview").hide();
                $(".docspricelistsarea").empty().html(reponse.data.content);
                init_vendordetails_edit();
            } else {
                show_error(reponse);
            }
        },'json');
    });
    $(".hidepricelists").unbind('click').click(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'short'});
        var url = '/vendors/show_pricelist_history';
        $.post(url, params, function (reponse) {
            if (reponse.errors=='') {
                $(".docspricelistsarea").empty().html(reponse.data.content);
                $(".vendordetails-section.customserviceview").show();
                $(".vendordetails-section.documentsview").show();
                init_vendordetails_edit();
            } else {
                show_error(reponse);
            }
        },'json');
    });
    $(".documentlist").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'full'});
        params.push({name: 'editmode', value: 1});
        var url = '/vendors/show_otherdocs_history';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendordetails-section.pricesview").hide();
                $(".vendordetails-section.customserviceview").hide();
                $(".vendordocument_value").empty().html(response.data.content);
                init_vendordetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".addnewotherdorcs").unbind('click').click(function () {
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'full'});
        params.push({name: 'editmode', value: 1});
        var url = '/vendors/show_otherdocs_history';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendordetails-section.pricesview").hide();
                $(".vendordetails-section.customserviceview").hide();
                $(".vendordocument_value").empty().html(response.data.content);
                init_vendordetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".addotherdoc").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'doctype', value: 'otherdoc'});
        var url='/vendors/vendordoc_upload_prepare';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#editModalLabel").empty().html('Please upload a non-pricing doc');
                $("#editModal").find('.modal-dialog').css('width', '300px');
                $("#editModal").find('div.modal-body').empty().html(response.data.content);
                $("#editModal").modal({show: true, keyboard: false });
                init_venorotherdoc_upload()
            } else {
                show_error(response);
            }
        },'json');
    })
    $(".hidedocumentslists").unbind('click').click(function(){
        var params = prepare_vendor_edit();
        params.push({name: 'view', value: 'short'});
        var url = '/vendors/show_otherdocs_history';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".vendordetails-section.pricesview").show();
                $(".vendordetails-section.customserviceview").show();
                $(".vendordocument_value").empty().html(response.data.content);
                init_vendordetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".otherdocdel_icon").unbind('click').click(function () {
        if (confirm('Delete Non-pricing Doument?')) {
            var url='/vendors/vendor_doc_manage';
            var params=prepare_vendor_edit();
            params.push({name: 'idx', value: $(this).data('doc')});
            params.push({name: 'manage', value: 'del'});
            params.push({name: 'doc_type', value: 'OTHERS'});
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#editModal").modal('hide');
                    $(".vendordocument_value").empty().html(response.data.content);
                    init_vendordetails_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $(".checkoutlockdata").unbind('click').click(function () {
        $(".checkoutlockdata").hide();
        $(".checkoutunlockdata").show();
    })
}

function init_venorprice_upload() {
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background: none;"><img src="/img/vendors/browse_new_doc.png" alt="Add Proof"/></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('pricelistuploadbtn'),
        action: '/utils/vendorcenterattach',
        uploadButtonText: '',
        multiple: false,
        debug: false,
        template: upload_templ,
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                $(".qq-upload-list").hide();
                var url='/vendors/vendor_doc_manage';
                var params=prepare_vendor_edit();
                params.push({name: 'doc_url', value: responseJSON.filename});
                params.push({name: 'doc_name', value: responseJSON.source});
                params.push({name: 'doc_type', value: 'PRICELIST'});
                params.push({name: 'doc_year', value: $(".pricelistyearselect").val()});
                params.push({name: 'manage', value: 'add'});
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $("#editModal").modal('hide');
                        $(".docspricelistsarea").empty().html(response.data.content);
                        $(".vendordetails-section.customserviceview").show();
                        $(".vendordetails-section.documentsview").show();
                        init_vendordetails_edit();
                    } else {
                        show_error(response);
                    }
                },'json');
            } else {
                alert(responseJSON.error);
                $("div#loader").hide();
                $("div.qq-upload-button").css('visibility','visible');
            }
        }
    });

}

function init_venorotherdoc_upload() {
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background: none;"><img src="/img/vendors/browse_new_doc.png" alt="Add Proof"/></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('otherdocuploadbtn'),
        action: '/utils/vendorcenterattach',
        uploadButtonText: '',
        multiple: false,
        debug: false,
        template: upload_templ,
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                $(".qq-upload-list").hide();
                var url='/vendors/vendor_doc_manage';
                var params=prepare_vendor_edit();
                params.push({name: 'doc_url', value: responseJSON.filename});
                params.push({name: 'doc_name', value: responseJSON.source});
                params.push({name: 'doc_type', value: 'OTHERS'});
                params.push({name: 'doc_description', value: $("#newdocname").val()});
                params.push({name: 'manage', value: 'add'});
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $("#editModal").modal('hide');
                        $(".vendordocument_value").empty().html(response.data.content);
                        init_vendordetails_edit();
                    } else {
                        show_error(response);
                    }
                },'json');
            } else {
                alert(responseJSON.error);
                $("div#loader").hide();
                $("div.qq-upload-button").css('visibility','visible');
            }
        }
    });
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

function show_errorfld(response) {
    var fld = response.data.errorfld;
    for (i=0; i<fld.length;i++) {
        var fldname = fld[i];
        console.log('Fld '+fldname);
        $(".vendorvaluearea[data-item='"+fldname+"']").find('fieldset').addClass('error');
        $(".vendorvaluearea[data-item='"+fldname+"']").find('.vendormandatoryfld').show();
    }
}