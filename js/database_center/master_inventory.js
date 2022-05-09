function init_master_inventory() {
    init_master_inventorydata();
    init_master_inventorycontent();
}

function init_master_inventorydata() {
    var params = new Array();
    params.push({name: 'inventory_type', value: $("#active_invtype").val()});
    params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
    params.push({name: 'showmax', value: $("#invshowmax").val()});
    var url="/masterinventory/get_inventory_list";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".masterinventtablebody").empty().html(response.data.content);
            $(".inventtotalinstock").empty().html(response.data.instock);
            $(".inventtotalavailable").empty().html(response.data.available);
            $(".inventtotalmaximum").empty().html(response.data.maximum);
            init_master_inventorytabledat();
        } else {
            show_error(response)
        }
    },'json')
}

function init_master_inventorycontent() {
    $(".mastinvent_left_section ").unbind('click').click(function () {
        var invent_type = $(this).data('invrtype');
        var invlabel = $(this).data('invlabel');
        $("#active_invtype").val(invent_type);
        $(".mastinvent_left_section").removeClass('active');
        $(".mastinvent_left_section").find('div.mastinvent_left_sectiondata').removeClass('active');
        $(".mastinvent_left_section[data-invrtype='"+invent_type+"']").addClass('active');
        $(".mastinvent_left_section[data-invrtype='"+invent_type+"']").find('div.mastinvent_left_sectiondata').addClass('active');
        $(".masterinventexport").empty().html('<i class="fa fa-share-square-o" aria-hidden="true"></i> Export '+invlabel+' Inventory');
        init_master_inventorydata();
    });
    $(".mastinvent_left_section").hover(
        function() {
            $( this ).find('div.mastinvent_left_sectiondata').addClass('rawsbtype');
        }, function() {
            $( this ).find('div.mastinvent_left_sectiondata').removeClass('rawsbtype');
        }
    );
    $(".inventfilterselect").unbind('change').change(function () {
        init_master_inventorydata();
    });
    $(".inventtotalmaxshow").unbind('click').click(function () {
        var curmax = $("#invshowmax").val();
        if (curmax==0) {
            $("#invshowmax").val(1);
            $(".inventtotalmaxshow").empty().html('Hide Max');
            $(".inventtotalmaximum").show();
            $(".masterinventtablehead").find("div.masterinventorymaximum").show();
            $(".masterinventtablehead").find("div.masterinventonorder").hide();
            $(".masterinventtablehead").find("div.masterinventonorder").hide();
            $(".masterinventtablehead").find("div.masterinventonmax").show();
            // Change body
            $(".inventorydatarow").find("div.masterinventpercent").css('border-right','none');
            $(".inventorydatarow").find('div.masterinventmaximum').show();
            $(".inventorydatarow").find('div.masterinventonorder').hide();
            $(".inventorydatarow").find('div.masterinventonmax').show();
        } else {
            $("#invshowmax").val(0);
            $(".inventtotalmaxshow").empty().html('Show Max');
            $(".inventtotalmaximum").hide();
            $(".masterinventtablehead").find("div.masterinventorymaximum").hide();
            $(".masterinventtablehead").find("div.masterinventonorder").show();
            $(".masterinventtablehead").find("div.masterinventonmax").hide();
            // Change body
            $(".inventorydatarow").find("div.masterinventpercent").css('border-right','1px solid #000000');
            $(".inventorydatarow").find('div.masterinventmaximum').hide();
            $(".inventorydatarow").find('div.masterinventonorder').show();
            $(".inventorydatarow").find('div.masterinventonmax').hide();
        }

    })
}

function init_master_inventorytabledat() {
    $(".inventorydatarow.itemcolor").hover(
        function () {
            $(this).addClass('activeinvent');
        },
        function () {
            $(this).removeClass('activeinvent');
        }
    );
    $(".inventorydatarow.itemcolor").find("div.masterinventavgprice").unbind('click').click(function () {
        var item=$(this).data('item');
        var params = new Array();
        params.push({name: 'itemcolor', value: item});
        var url='/masterinventory/get_color_inventory';
        $.post(url,params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventPriceLabel").empty().html(response.data.wintitle);
                $("#modalEditInventPrice").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventPrice").modal({keyboard: false, show: true});
                init_itemcolor_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".masterinventseq.itemedit").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'editmode', value: 0});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        var url='/masterinventory/get_item_inventory';
        $.post(url,params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventItemLabel").empty().html(response.data.wintitle);
                $("#modalEditInventItem").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventItem").find('div.modal-footer').empty().html(response.data.winfooter);
                $("#modalEditInventItem").modal({keyboard: false, show: true});
                init_masteritem_popup();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_itemcolor_popup() {
    $("#modalEditInventPrice").find('button.close').unbind('click').click(function () {
        init_master_inventorydata();
    });
    $(".priceheadhistorylnk").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'itemcolor', value: $(this).data('item')});
        var url='/masterinventory/get_color_history';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                // Close Color data
                $("#modalEditInventPrice").modal('hide');
                // Show History window
                $("#modalEditInventHistoryLabel").empty().html(response.data.wintitle);
                $("#modalEditInventHistory").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventHistory").modal({keyboard: false, show: true});
                init_colorhistory_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".viewbalancesused").unbind('click').click(function () {
        var showused=0;
        if ($("#priceslistshowused").val()==0) {
            showused=1;
        }
        $("#priceslistshowused").val(showused);
        var params = new Array();
        params.push({name: 'itemcolor', value: $(this).data('item')});
        params.push({name: 'showused', value: showused});
        var url = '/masterinventory/get_color_showused';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (showused==1) {
                    $(".inventoryprice_table_body").addClass('showused').empty().html(response.data.content);
                    $(".viewbalancesused").empty().html('- hide balances used');
                } else {
                    $(".inventoryprice_table_body").removeClass('showused').empty().html(response.data.content);
                    $(".viewbalancesused").empty().html('+ view balances used');
                }
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_colorhistory_popup() {
    $("#modalEditInventHistory").find('button.close').unbind('click').click(function () {
        init_master_inventorydata();
    });
    $(".inventoryhistory_view_prices").unbind('click').click(function () {
        var item=$(this).data('item');
        var params = new Array();
        params.push({name: 'itemcolor', value: item});
        var url='/masterinventory/get_color_inventory';
        $.post(url,params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventHistory").modal('hide');
                $("#modalEditInventPriceLabel").empty().html(response.data.wintitle);
                $("#modalEditInventPrice").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventPrice").modal({keyboard: false, show: true});
                init_itemcolor_popup();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_masteritem_popup() {
    $(".itemteplatevalue").find('img').unbind('click').click(function () {
        var url = $(this).data('src');
        window.open(url, 'ItemTemplate', "width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
    });
    $(".edititembutton").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'editmode', value: 1});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        var url='/masterinventory/get_item_inventory';
        $.post(url,params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventItemLabel").empty().html(response.data.wintitle);
                $("#modalEditInventItem").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventItem").find('div.modal-footer').empty().html(response.data.winfooter);
                $("#modalEditInventItem").modal({keyboard: false, show: true});
                init_masteritem_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    init_uploadfiles_masteritem();
    $(".closeitemdata").unbind('click').click(function () {
        $("#modalEditInventItem").modal('hide');
    });
    $(".saveitemdata").unbind('click').click(function () {
        // Collect data for save
        var params = new Array();
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'item_name', value: $(".itemdataname").val()});
        params.push({name: 'item_unit', value: $(".itemdataunit").val()});
        params.push({name: 'proofflag', value: $("#prooftemplflag").val()});
        params.push({name: 'proofsrc', value: $("#prooftemplatesrc").val()});
        params.push({name: 'proofname', value: $("#prooftemplatename").val()});
        params.push({name: 'plateflag', value: $("#platetemplflag").val()});
        params.push({name: 'platesrc', value: $("#platetemplatesrc").val()});
        params.push({name: 'platename', value: $("#platetemplatename").val()});
        params.push({name: 'boxflag', value: $("#boxtemplflag").val()});
        params.push({name: 'boxsrc', value: $("#boxtemplatesrc").val()});
        params.push({name: 'boxname', value: $("#boxtemplatename").val()});
        var url = "/masterinventory/masteritem_save";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventItem").modal('hide');
                init_master_inventorydata();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_uploadfiles_masteritem() {
    $(".itemteplatevalue").find('img').unbind('click').click(function () {
        var url = $(this).data('src');
        window.open(url, 'ItemTemplate', "width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
    });
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button replacebutton">[Replace]</div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';
    if ($("#proof-uploader").length > 0) {
        var uploadproof = new qq.FileUploader({
            element: document.getElementById('proof-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            // template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    $("#prooftemplflag").val(1);
                    $("#prooftemplatesrc").val(responseJSON.filename);
                    $("#prooftemplatename").val(responseJSON.source);
                    var url='/masterinventory/masteritem_newdoc';
                    var params=new Array()
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_type', value: 'proof'});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#prooftemplatearea").empty().html(response.data.content);
                            init_uploadfiles_masteritem();
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

    if ($("#proofnew-uploader").length > 0) {
        var uploadnewproof = new qq.FileUploader({
            element: document.getElementById('proofnew-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    $("#prooftemplflag").val(1);
                    $("#prooftemplatesrc").val(responseJSON.filename);
                    $("#prooftemplatename").val(responseJSON.source);
                    var url='/masterinventory/masteritem_newdoc';
                    var params=new Array()
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_type', value: 'proof'});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#prooftemplatearea").empty().html(response.data.content);
                            init_uploadfiles_masteritem();
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
    if ($("#plate-uploader").length > 0) {
        var uploadplate = new qq.FileUploader({
            element: document.getElementById('plate-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            // template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    $("#platetemplflag").val(1);
                    $("#platetemplatesrc").val(responseJSON.filename);
                    $("#platetemplatename").val(responseJSON.source);
                    var url='/masterinventory/masteritem_newdoc';
                    var params=new Array()
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_type', value: 'plate'});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#platetemplatearea").empty().html(response.data.content);
                            init_uploadfiles_masteritem();
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
    if ($("#platenew-uploader").length > 0) {
        var uploadnewplate = new qq.FileUploader({
            element: document.getElementById('platenew-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    $("#platetemplflag").val(1);
                    $("#platetemplatesrc").val(responseJSON.filename);
                    $("#platetemplatename").val(responseJSON.source);
                    var url='/masterinventory/masteritem_newdoc';
                    var params=new Array()
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_type', value: 'plate'});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#platetemplatearea").empty().html(response.data.content);
                            init_uploadfiles_masteritem();
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
    if ($("#box-uploader").length > 0 ) {
        var uploadbox = new qq.FileUploader({
            element: document.getElementById('box-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            // template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    $("#boxtemplflag").val(1);
                    $("#boxtemplatesrc").val(responseJSON.filename);
                    $("#boxtemplatename").val(responseJSON.source);
                    var url='/masterinventory/masteritem_newdoc';
                    var params=new Array()
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_type', value: 'box'});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#boxtemplatearea").empty().html(response.data.content);
                            init_uploadfiles_masteritem();
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
    if ($("#boxnew-uploader").length > 0) {
        var uploadnewbox = new qq.FileUploader({
            element: document.getElementById('boxnew-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    $("#boxtemplflag").val(1);
                    $("#boxtemplatesrc").val(responseJSON.filename);
                    $("#boxtemplatename").val(responseJSON.source);
                    var url='/masterinventory/masteritem_newdoc';
                    var params=new Array()
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_type', value: 'box'});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#boxtemplatearea").empty().html(response.data.content);
                            init_uploadfiles_masteritem();
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
}