var sliderwidth=183;

function init_master_inventory() {
    init_master_inventoryhead();
    init_master_inventorydata();
    // init_master_containers();
    // init_master_express();
    // init_master_inventorycontent();
    init_inventcontainer_move();
}

function init_master_inventoryhead() {
    var params = new Array();
    params.push({name: 'inventory_type', value: $("#active_invtype").val()});
    params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
    params.push({name: 'showmax', value: $("#invshowmax").val()});
    var url="/masterinventory/get_inventory_head";
    $("#loader").show();
    $.post(url, params, function (response){
        if (response.errors=='') {
            $(".mastinvent_container_contentarea").empty().html(response.data.onboat_content);
            $(".containertotals").empty().html(response.data.onboat_totals);
            if (parseInt(response.data.container_leftview)==1) {
                $(".mastinvent_container_slideleft").addClass('active').empty().html('<img src="/img/masterinvent/container_active_left.png"/>');
            }
            $(".mastinvent_footlink_container").empty().html(response.data.onboat_links);
            $(".mastinvent_express_contentarea").empty().html(response.data.express_content);
            if (parseInt(response.data.express_leftview)==1) {
                $(".mastinvent_express_slideleft").addClass('active').empty().html('<img src="/img/masterinvent/container_active_left.png"/>');
            }
            $(".expresstotals").empty().html(response.data.express_totals);
            $(".mastinvent_footlink_express").empty().html(response.data.express_links);
            $("#masterinventpercent").empty().html(response.data.masterinventpercent);
            $("#masterinventorymaximum").empty().html(response.data.masterinventorymaximum);
            $("#masterinventinstock").empty().html(response.data.masterinventinstock);
            $("#masterinventreserv").empty().html(response.data.masterinventreserv);
            $("#masterinventavailab").empty().html(response.data.masterinventavailab);
            $("#maximuminvent").empty().html(response.data.maxsum);
            init_master_inventorycontent();
        } else {
            show_error(response);
        }
    },'json');
}
function init_master_inventorydata() {
    var params = new Array();
    params.push({name: 'inventory_type', value: $("#active_invtype").val()});
    params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
    params.push({name: 'showmax', value: $("#invshowmax").val()});
    params.push({name: 'sortby', value: $(".inventcolorsort").val()});
    var url="/masterinventory/get_inventory_list";
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#loader").hide();
            $("#masterinventtablebody").empty().html(response.data.bodylist);
            $(".masterinventtablebody").find('div.mastinvent_body_left').html(response.data.left_content);
            $(".masterinventtablebody").find('div.mastinvent_body_express').html(response.data.express_content);
            $(".masterinventtablebody").find('div.mastinvent_body_container').html(response.data.container_content);
            $(".masterinventtablebody").find('div.mastinvent_body_right').html(response.data.right_content);
            // $(".inventtotalinstock").empty().html(response.data.instock);
            // $(".inventtotalavailable").empty().html(response.data.available);
            // $(".inventtotalmaximum").empty().html(response.data.maximum);
            jQuery.balloon.init();
            init_master_inventorytabledat();
            leftmenu_alignment();
        } else {
            $("#loader").hide();
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
        init_master_inventoryhead();
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
        init_master_inventoryhead();
        init_master_inventorydata();
    });
    $(".inventtotalmaxshow").unbind('click').click(function () {
        var curmax = $("#invshowmax").val();
        if (curmax==0) {
            $("#invshowmax").val(1);
            $(".inventtotalmaxshow").empty().html('Hide Max');
            $(".mastinvent_header_left").addClass('showmax');
            $(".mastinvent_header_container").addClass('showmax');
            $(".mastinvent_header_right").find(".masterinvemptyspace").hide();
            $(".mastinvent_header_right").addClass('showmax');
            // Totals
            $(".containertotals").addClass('showmax');
            $(".masterinventtablebody").find("div.mastinvent_body_left").addClass('showmax');
            $(".masterinventtablebody").find("div.mastinvent_body_container").addClass('showmax');
            $(".masterinventtablebody").find("div.mastinvent_body_right").addClass('showmax');
            $(".masterinventtotals").find("div.masterinventorymaximum").show();
            $(".masterinventtablehead").find("div.masterinventorymaximum").show();
            $(".inventorydatarow").find("div.masterinventmaximum").show();
            $(".mastinvent_footlink_left").addClass('showmax');
            $(".mastinvent_footlink_container").addClass('showmax');
        } else {
            $("#invshowmax").val(0);
            $(".inventtotalmaxshow").empty().html('Show Max');
            $(".mastinvent_header_left").removeClass('showmax');
            $(".mastinvent_header_container").removeClass('showmax');
            $(".mastinvent_header_right").find(".masterinvemptyspace").show();
            $(".mastinvent_header_right").removeClass('showmax');
            // Totals
            $(".containertotals").removeClass('showmax');
            $(".masterinventtablebody").find("div.mastinvent_body_left").removeClass('showmax');
            $(".masterinventtablebody").find("div.mastinvent_body_container").removeClass('showmax');
            $(".masterinventtablebody").find("div.mastinvent_body_right").removeClass('showmax');
            $(".masterinventtotals").find("div.masterinventorymaximum").hide();
            $(".masterinventtablehead").find("div.masterinventorymaximum").hide();
            $(".inventorydatarow").find("div.masterinventmaximum").hide();
            $(".mastinvent_footlink_left").removeClass('showmax');
            $(".mastinvent_footlink_container").removeClass('showmax');
        }
    })
    $(".addnewmasterinvent").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'item', value: 0});
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
    $(".onboatmanage").find('i').unbind('click').click(function (){
        // Edit container
        var container = $(this).parent('div.onboatmanage').data('container');
        var onboattype = $(this).parent('div.onboatmanage').data('onboattype');
        var url="/masterinventory/changecontainer";
        var params = new Array();
        params.push({name: 'container', value: container});
        params.push({name: 'onboat_type', value: onboattype});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
        params.push({name: 'sortby', value: $(".inventcolorsort").val()});
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Lock add / edit elements
                $(".onboatmanage").find('i').unbind('click');
                $(".mastinvent_container_manage").find('span').unbind('click');
                $(".waitarrive").unbind('click');
                // Change view of container
                if (onboattype=='C') {
                    $("div.mastinvent_body_container").find("div.onboacontainerdata[data-container='"+container+"']").addClass('editdata');
                    $("div.mastinvent_body_container").find("div.onboacontainerarea[data-container='"+container+"']").addClass('editdata');
                    $("div.mastinvent_body_container").find('div.onboatdataareas').addClass('editdata');
                    $("div.mastinvent_body_container").find("div.onboacontainerdata[data-container='"+container+"']").empty().html(response.data.content);
                    var cwidth = parseInt($("div.mastinvent_body_container").find('div.after_head').css('width'))+55;
                    $("div.mastinvent_body_container").find('div.after_head').css('width',cwidth);
                    // Edit
                    $(".mastinvent_container_contentarea").find("div.waitarrive[data-container='"+container+"']").empty().html(response.data.managecontent);
                    $(".mastinvent_container_contentarea").find("input.boatcontainerdate[data-container='"+container+"']").val(response.data.boatdate).datepicker({
                        // format : 'mm/dd/yy',
                        autoclose : true,
                        todayHighlight: true,
                        zIndexOffset: 100,
                    }).on("change", function() {
                        update_onboat_date($(this).val());
                    });
                    $(".mastinvent_container_contentarea").find("input.boatcontainerfreight[data-container='"+container+"']").val(response.data.freight_price).prop('readonly', false).prop('title','');
                } else {
                    $("div.mastinvent_body_express").find("div.onboacontainerdata[data-container='"+container+"']").addClass('editdata');
                    $("div.mastinvent_body_express").find('div.onboatdataareas').addClass('editdata');
                    $("div.mastinvent_body_express").find("div.onboacontainerdata[data-container='"+container+"']").empty().html(response.data.content);
                    $("div.mastinvent_body_express").find("div.onboacontainerarea[data-container='"+container+"']").addClass('editdata');
                    var cwidth = parseInt($("div.mastinvent_body_express").find('div.after_head').css('width'))+55;
                    $("div.mastinvent_body_express").find('div.after_head').css('width',cwidth);
                    // Edit
                    $(".mastinvent_express_contentarea").find("div.waitarrive[data-container='"+container+"']").empty().html(response.data.managecontent);
                    $(".mastinvent_express_contentarea").find("input.boatcontainerdate[data-container='"+container+"']").val(response.data.boatdate).datepicker({
                        format : 'mm/dd/yy',
                        autoclose : true,
                        todayHighlight: true,
                        zIndexOffset: 100,
                    }).on("change", function() {
                        update_onboat_date($(this).val());
                    });
                    $(".mastinvent_express_contentarea").find("input.boatcontainerfreight[data-container='"+container+"']").val(response.data.freight_price).prop('readonly', false).prop('title','');
                }
                init_edit_inventcontainer(container,onboattype);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".mastinvent_container_manage").find('span').unbind('click').click(function (){
        // Add container
        var container = 0;
        var url="/masterinventory/changecontainer";
        var params = new Array();
        params.push({name: 'container', value: container});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
        params.push({name: 'onboat_type', value: 'C'})
        params.push({name: 'sortby', value: $(".inventcolorsort").val()});
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Lock add / edit elements
                $(".onboatmanage").find('i').unbind('click');
                $(".mastinvent_container_manage").find('span').unbind('click');
                $(".mastinvent_express_manage").find('span').unbind('click');
                $(".waitarrive").unbind('click');
                // Change view of container
                $(".mastinvent_body_container").find("div.onboacontainerdata[data-container='"+container+"']").addClass('editdata');
                $(".mastinvent_body_container").find('div.onboatdataareas').addClass('editdata');
                $(".mastinvent_body_container").find("div.onboacontainerarea[data-container='"+container+"']").addClass('editdata');
                $(".mastinvent_container_contentarea").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.containerhead);
                $(".containertotals").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.containertotal);
                var cwidth = parseInt($("div.mastinvent_body_container").find('div.after_head').css('width'))+55;
                $("div.mastinvent_body_container").find('div.after_head').css('width',cwidth);
                $(".onboatdataareas").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.content);
                // Edit
                $(".waitarrive[data-container='"+container+"']").empty().html(response.data.managecontent);
                $(".onboacontainer").find("input.boatcontainerdate[data-container='"+container+"']").datepicker({
                    // format : 'mm/dd/yy',
                    autoclose : true,
                    todayHighlight: true,
                    zIndexOffset: 100,
                }).on("change", function() {
                    update_onboat_date($(this).val());
                });
                $(".onboacontainer").find("input.boatcontainerfreight[data-container='"+container+"']").prop('readonly', false).prop('title','');
                init_edit_inventcontainer(container,'C');
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".mastinvent_express_manage").find('span').unbind('click').click(function (){
        // Add express
        var container = 0;
        var url="/masterinventory/changecontainer";
        var params = new Array();
        params.push({name: 'container', value: container});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
        params.push({name: 'sortby', value: $(".inventcolorsort").val()});
        params.push({name: 'onboat_type', value: 'E'})
        $.post(url, params, function (response){
            if (response.errors=='') {
                // Lock add / edit elements
                $(".onboatmanage").find('i').unbind('click');
                $(".mastinvent_container_manage").find('span').unbind('click');
                $(".mastinvent_express_manage").find('span').unbind('click');
                $(".waitarrive").unbind('click');
                // Change view of container
                $(".mastinvent_body_express").find("div.onboacontainerdata[data-container='"+container+"']").addClass('editdata');
                $(".mastinvent_body_express").find('div.onboatdataareas').addClass('editdata');
                $(".mastinvent_express_contentarea").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.containerhead);
                $(".expresstotals").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.containertotal);
                $(".mastinvent_body_express").find("div.onboatdataareas").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.content);
                // Edit
                $(".mastinvent_express_contentarea").find("div.waitarrive[data-container='"+container+"']").empty().html(response.data.managecontent);
                $(".mastinvent_express_contentarea").find("input.boatcontainerdate[data-container='"+container+"']").datepicker({
                    format : 'mm/dd/yy',
                    autoclose : true,
                    todayHighlight: true,
                }).on("change", function() {
                    update_onboat_date($(this).val());
                });
                $(".mastinvent_express_contentarea").find("input.boatcontainerfreight[data-container='"+container+"']").prop('readonly', false).prop('title','');
                init_edit_inventcontainer(container,'E');
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".waitarrive").unbind('click').click(function (){
        var conteinernum = $(this).data('container');
        var onboattype = $(this).data('onboattype');
        var msg = 'Are You Sure You Want to Mark This As Arrived?'
        if (confirm(msg)==true) {
            var params = new Array();
            params.push({name: 'onboat_container', value: conteinernum});
            params.push({name: 'onboat_type', value: onboattype});
            params.push({name: 'inventory_type', value: $("#active_invtype").val()});
            params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
            var url = '/masterinventory/container_arrive';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors==''){
                    $(".mastinvent_container_contentarea").empty().html(response.data.onboat_header);
                    $("#loader").hide();
                    init_master_inventoryhead();
                    init_master_inventorydata();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    // Eddit add cost
    $(".inventadd").unbind('change').change(function(){
        var params = new Array();
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'addcost', value: $(this).val()});
        var url = '/masterinventory/changeaddcost';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".addlabeltotal").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Change Sort
    $("select.inventcolorsort").unbind('change').change(function(){
        init_master_inventorydata();
    });
}

function init_edit_inventcontainer(container, onboat_type) {
    // Edit onstock
    $(".onroutestockinpt").focus(function (){
        var color = $(this).data('color');
        $(".mastinvent_body_left").find("div.itemcolor[data-invcolor='"+color+"']").addClass('currenteditrow');
    });
    $(".onroutestockinpt").blur(function (){
        var color = $(this).data('color');
        $(".mastinvent_body_left").find("div.itemcolor[data-invcolor='"+color+"']").removeClass('currenteditrow');
    });
    $(".onroutepriceinpt").focus(function (){
        var color = $(this).data('color');
        $(".mastinvent_body_left").find("div.itemcolor[data-invcolor='"+color+"']").addClass('currenteditrow');
    });
    $(".onroutepriceinpt").blur(function (){
        var color = $(this).data('color');
        $(".mastinvent_body_left").find("div.itemcolor[data-invcolor='"+color+"']").removeClass('currenteditrow');
    });

    $(".onroutestockinpt").unbind('change').change(function (){
        var item = $(this).data('item');
        var color = $(this).data('color');
        var params = new Array();
        params.push({name: 'session', value: $("#container_session").val()});
        params.push({name: 'entity', value: 'qty'});
        params.push({name: 'color', value: $(this).data('color')});
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/masterinventory/changecontainer_param';
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (onboat_type=='C') {
                    $(".mastinvent_body_container").find("div.conteinerqty[data-itemtotal='"+item+"']").empty().html(response.data.itemtval);
                    $(".containertotals").find(".containertotal[data-container='"+response.data.container+"']").empty().html(response.data.total);
                } else {
                    $(".mastinvent_body_express").find("div.conteinerqty[data-itemtotal='"+item+"']").empty().html(response.data.itemtval);
                    $(".expresstotals").find("div.containertotal[data-container='"+response.data.container+"']").empty().html(response.data.total);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".onroutepriceinpt").unbind('change').change(function (){
        var item = $(this).data('item');
        var color = $(this).data('color');
        var params = new Array();
        params.push({name: 'session', value: $("#container_session").val()});
        params.push({name: 'entity', value: 'vendor_price'});
        params.push({name: 'color', value: $(this).data('color')});
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url='/masterinventory/changecontainer_param';
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // $("input.boatcontainerdate").unbind('change').change(function(){
    //     var params = new Array();
    //     params.push({name: 'session', value: $("#container_session").val()});
    //     params.push({name: 'entity', value: 'onboat_date'});
    //     params.push({name: 'newval', value: $(this).val()});
    //     var url='/masterinventory/changecontainer_header';
    //     $.post(url, params, function (response){
    //         if (response.errors=='') {
    //         } else {
    //             show_error(response);
    //         }
    //     },'json');
    // });
    $("input.boatcontainerfreight").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'session', value: $("#container_session").val()});
        params.push({name: 'entity', value: 'freight_price'});
        params.push({name: 'newval', value: $(this).val()});
        var url='/masterinventory/changecontainer_header';
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Click on cancel button
    $(".cancelboatcontainer").unbind('click').click(function (){
        var url='/masterinventory/containerchange_cancel';
        var params = new Array();
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
        params.push({name: 'onboat_type', value: onboat_type});
        params.push({name: 'sortby', value: $(".inventcolorsort").val()});
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#loader").hide();
                if (onboat_type=='C') {
                    $(".mastinvent_container_contentarea").empty().html(response.data.onboat_header);
                    $(".mastinvent_body_container").empty().html(response.data.onboat_content);
                    $(".mastinvent_footlink_container").empty().html(response.data.onboat_links);
                } else {
                    // $(".mastinvent_body_express").css('width','270px');
                    $(".mastinvent_express_contentarea").empty().html(response.data.onboat_header);
                    $(".mastinvent_body_express").empty().html(response.data.onboat_content);
                    $(".mastinvent_footlink_express").empty().html(response.data.onboat_links);
                }
                init_master_inventorycontent();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    // Click save button
    $(".saveboatcontainer").unbind('click').click(function (){
        var url='/masterinventory/containerchange_save';
        var params = new Array();
        params.push({name: 'session', value: $("#container_session").val()});
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
        params.push({name: 'sortby', value: $(".inventcolorsort").val()});
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#loader").hide();
                if (onboat_type=='C') {
                    $(".mastinvent_container_contentarea").empty().html(response.data.onboat_header);
                    $(".mastinvent_body_container").empty().html(response.data.onboat_content);
                    $(".mastinvent_footlink_container").empty().html(response.data.onboat_links);
                } else {
                    // $(".mastinvent_body_express").css('width','270px');
                    $(".mastinvent_express_contentarea").empty().html(response.data.onboat_header);
                    $(".mastinvent_body_express").empty().html(response.data.onboat_content);
                    $(".mastinvent_footlink_express").empty().html(response.data.onboat_links);
                }
                init_master_inventorycontent();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
}

function update_onboat_date(dateval) {
        var params = new Array();
        params.push({name: 'session', value: $("#container_session").val()});
        params.push({name: 'entity', value: 'onboat_date'});
        params.push({name: 'newval', value: dateval});
        var url='/masterinventory/changecontainer_header';
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
}
function init_inventcontainer_move() {
    $(".mastinvent_container_slideleft").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var offset = 60;
            container_slider_move(offset);
        }
    });
    $(".mastinvent_container_slideright").unbind('click').click(function (){
        if ($(this).hasClass('active')) {
            var offset = -60;
            container_slider_move(offset);
        }
    });
}

function container_slider_move(offset) {
    var margin=parseInt($(".mastinvent_container_contentarea").find("div.after_head").css('margin-left'));
    var slwidth=parseInt($(".mastinvent_container_contentarea").find("div.after_head").css('width'));
    var newmargin=(margin+offset);
    if (newmargin>=0) {
        newmargin=0;
        $(".mastinvent_container_slideleft").removeClass('active');
        $(".mastinvent_container_slideleft").empty().html('<img src="/img/masterinvent/container_nonactive_left.png"/>');
    } else {
        $(".mastinvent_container_slideleft").addClass('active');
        $(".mastinvent_container_slideleft").empty().html('<img src="/img/masterinvent/container_active_left.png"/>');
    }

    if ((slwidth+newmargin)>sliderwidth) {
        $(".mastinvent_container_slideright").addClass('active');
        $(".mastinvent_container_slideright").empty().html('<img src="/img/masterinvent/container_active_right.png"/>');
    } else {
        $(".mastinvent_container_slideright").removeClass('active');
        $(".mastinvent_container_slideright").empty().html('<img src="/img/masterinvent/container_nonactive_right.png"/>');
    }
    $("div.after_head").animate({marginLeft:newmargin+'px'},'slow',function(){
    });
    $("div.linksconteiners").animate({marginLeft:newmargin+'px'},'slow',function(){
    });

    // $("div.after_head_boat").animate({marginLeft:newmargin+'px'},'slow');
    // if ((slwidth+newmargin)>=slshow) {
    init_inventcontainer_move();
}

function init_master_inventorytabledat() {
    // $(".inventorydatarow.itemcolor").hover(
    //     function () {
    //         $(this).addClass('activeinvent');
    //     },
    //     function () {
    //         $(this).removeClass('activeinvent');
    //     }
    // );
    $(".inventorydatarow.itemcolor").find("div.masterinventhistory").unbind('click').click(function () {
        var item=$(this).data('item');
        var params = new Array();
        params.push({name: 'itemcolor', value: item});
        var url='/masterinventory/get_color_inventory';
        $.post(url,params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventPriceLabel").empty().html(response.data.wintitle);
                $("#modalEditInventPrice").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventPrice").modal({keyboard: false, show: true});
                // $('body').addClass('modal-open');
                init_itemcolor_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".inventorydatarow.itemcolor").find("div.masterinventhistorystock").unbind('click').click(function () {
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
                // $('body').addClass('modal-open');
                init_colorhistory_popup();
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
    });
    $(".masterinventnumber.colordata").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'item', value: 0});
        params.push({name: 'color', value: $(this).data('color')});
        params.push({name: 'editmode', value: 0});
        var url='/masterinventory/get_inventory_color';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventColorLabel").empty().html(response.data.wintitle);
                $("#modalEditInventColor").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventColor").find('div.modal-footer').empty().html(response.data.winfooter);
                $("#modalEditInventColor").modal({keyboard: false, show: true});
                // $('body').addClass('modal-open');
                init_mastercolor_popup();
            } else {
                show_error(response);
            }
        },'json')
    });
    $(".addmasterinventory").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'item', value: $(this).data('item')});
        params.push({name: 'color', value: 0});
        params.push({name: 'editmode', value: 1});
        var url='/masterinventory/get_inventory_color';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventColorLabel").empty().html(response.data.wintitle);
                $("#modalEditInventColor").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventColor").find('div.modal-footer').empty().html(response.data.winfooter);
                $("#modalEditInventColor").modal({keyboard: false, show: true});
                // $('body').addClass('modal-open');
                init_mastercolor_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".masterinventexport").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'inventory_type', value: $("#active_invtype").val()});
        params.push({name: 'inventory_label', value: $(".mastinvent_left_section.active").data('invlabel')});
        params.push({name: 'sortby', value: $(".inventcolorsort").val()});
        var url = '/masterinventory/export_inventory';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                window.open(response.data.url);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".linksconteiners").find('div.download_container').unbind('click').click(function(){
        var container = $(this).parent('.onboacontainer').data('container');
        var onboattype = $(this).parent('.onboacontainer').data('onboattype');
        var params=new Array();
        params.push({name: 'onboat_container', value: container});
        params.push({name: 'onboat_type', value: onboattype});
        var url="/masterinventory/inventory_boat_download";
        $.post(url, params, function(response){
            if (response.errors=='') {
                var link = response.data.url;
                window.open(link, 'win', 'width=500,height=500,toolbar=0');
            }
        },'json');
    });
}

function init_itemcolor_popup() {
    $("#modalEditInventPrice").find('button.close').unbind('click').click(function () {
        if (parseInt($("#invenorymanualpriceadd").val())===0) {
            $("#modalEditInventPrice").modal('hide');
        } else {
            var color = $("span.incomelistadd").data('item');
            var params = new Array();
            params.push({name: 'itemcolor', value: color});
            params.push({name: 'inventory_type', value: $("#active_invtype").val()})
            var url='/masterinventory/update_color_inventory';
            $.post(url,params, function (response) {
                if (response.errors=='') {
                    // Update Color
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventpercent').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.color_stockclass);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventpercent').empty().html(response.data.color_percent);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventinstock').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.color_stockclass);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventinstock').empty().html(response.data.color_instock);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventreserv').empty().html(response.data.color_reserved);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventavailab').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.color_stockclass);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventavailab').empty().html(response.data.color_available);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.dictprice').empty().html(response.data.color_price);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.avgprice').empty().html(response.data.color_avgprice);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventtotalval').empty().html(response.data.color_total);
                    // Update Item
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventpercent').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.item_stockclass);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventpercent').empty().html(response.data.item_percent);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventinstock').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.item_stockclass);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventinstock').empty().html(response.data.item_instock);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventreserv').empty().html(response.data.item_reserved);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventavailab').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.item_stockclass);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventavailab').empty().html(response.data.item_available);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.dictprice').empty().html(response.data.item_price);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.avgprice').empty().html(response.data.item_avgprice);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventtotalval').empty().html(response.data.item_total);
                    // Update Total
                    $("#masterinventpercent").empty().html(response.data.masterinventpercent);
                    $("#masterinventorymaximum").empty().html(response.data.masterinventorymaximum);
                    $("#masterinventinstock").empty().html(response.data.masterinventinstock);
                    $("#masterinventreserv").empty().html(response.data.masterinventreserv);
                    $("#masterinventavailab").empty().html(response.data.masterinventavailab);
                    $("#modalEditInventPrice").modal('hide');
                } else {
                    show_error(response);
                }
            },'json');
        }
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
                // $('body').addClass('modal-open');
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
    });
    // Add manual income
    $("span.incomelistadd").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'itemcolor', value: $(this).data('item')});
        var url = '/masterinventory/add_color_income';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".inventoryprice_table_body").prepend(response.data.content);
                init_manualincome_manage();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_manualincome_manage() {
    $("span.incomelistadd").unbind('click');
    $("span.cancelsaveinventincome").unbind('click').click(function () {
        $(".inventoryprice_table_row.manualaddrecord").remove();
        init_itemcolor_popup();
    });
    $(".inventoryincomedateinpt").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("span.saveinventincome").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'itemcolor', value: $("#colorinventory").val()});
        params.push({name: 'income_date', value: $("input.inventoryincomedateinpt").val()});
        // params.push({name: 'income_recnum', value: $("input.inventincomerecnum").val()});
        params.push({name: 'income_desript', value: $("input.inventincomedescripinpt").val()});
        params.push({name: 'income_price', value: $("input.inventincomepriceinpt").val()});
        params.push({name: 'income_qty', value: $("input.inventincomeqtyinpt").val()});
        var url = '/masterinventory/save_color_income';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventPrice").find('div.modal-body').empty().html(response.data.content);
                $("#invenorymanualpriceadd").val(1);
                init_itemcolor_popup();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_colorhistory_popup() {
    $("#modalEditInventHistory").find('button.close').unbind('click').click(function () {
        if (parseInt($("#invenorynewhistoryadd").val())===0) {
            $("#modalEditInventHistory").modal('hide');
        } else {
            var color = $("span.outcomelistadd").data('item');
            var params = new Array();
            params.push({name: 'itemcolor', value: color});
            params.push({name: 'inventory_type', value: $("#active_invtype").val()})
            var url='/masterinventory/update_color_inventory';
            $.post(url,params, function (response) {
                if (response.errors=='') {
                    // Update Color
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventpercent').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.color_stockclass);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventpercent').empty().html(response.data.color_percent);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventinstock').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.color_stockclass);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventinstock').empty().html(response.data.color_instock);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventreserv').empty().html(response.data.color_reserved);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventavailab').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.color_stockclass);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventavailab').empty().html(response.data.color_available);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.dictprice').empty().html(response.data.color_price);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.avgprice').empty().html(response.data.color_avgprice);
                    $(".inventorydatarow[data-invcolor='"+color+"']").find('div.masterinventtotalval').empty().html(response.data.color_total);
                    // Update Item
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventpercent').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.item_stockclass);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventpercent').empty().html(response.data.item_percent);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventinstock').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.item_stockclass);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventinstock').empty().html(response.data.item_instock);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventreserv').empty().html(response.data.item_reserved);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventavailab').removeClass('severevalstock').removeClass('lowinstock').addClass(response.data.item_stockclass);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventavailab').empty().html(response.data.item_available);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.dictprice').empty().html(response.data.item_price);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.avgprice').empty().html(response.data.item_avgprice);
                    $(".inventorydatarow[data-item='"+response.data.item+"']").find('div.masterinventtotalval').empty().html(response.data.item_total);
                    // Update Total
                    $("#masterinventpercent").empty().html(response.data.masterinventpercent);
                    $("#masterinventorymaximum").empty().html(response.data.masterinventorymaximum);
                    $("#masterinventinstock").empty().html(response.data.masterinventinstock);
                    $("#masterinventreserv").empty().html(response.data.masterinventreserv);
                    $("#masterinventavailab").empty().html(response.data.masterinventavailab);
                    $("#modalEditInventHistory").modal('hide');
                } else {
                    show_error(response);
                }
            },'json');
        }
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
    });
    $("span.outcomelistadd").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'itemcolor', value: $(this).data('item')});
        var url = '/masterinventory/add_color_outcome';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".inventoryhistory_table_body").prepend(response.data.content);
                init_manualoutcome_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".instock_recnum[data-rectype='order']").unbind('click').click(function () {
        var order = $(this).data('order');
        inventory_order_edit(order);
    })
}

function inventory_order_edit(order) {
    var callpage = 'masterinvent';
    var brand = $("input#ordersviewbrand").val();
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#modalEditInventHistory").modal('hide');
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            // $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({keyboard: false, show: true})
            if (parseInt(order)==0) {
                init_onlineleadorder_edit();
                init_rushpast();
                if (parseInt($("#ordermapuse").val())==1) {
                    // Init simple Shipping address
                    initShipOrderAutocomplete();
                    if ($("#billorder_line1").length > 0) {
                        initBillOrderAutocomplete();
                    }
                }
            } else {
                if (parseInt(response.data.cancelorder)===1) {
                    $("#artModal").find('div.modal-header').addClass('cancelorder');
                } else {
                    $("#artModal").find('div.modal-header').removeClass('cancelorder');
                }
                navigation_init();
            }
            // $('body').addClass('modal-open');
        } else {
            show_error(response);
        }
    },'json');
}

function init_manualoutcome_manage() {
    $("span.outcomelistadd").unbind('click');
    $("span.cancelinventoutcome").unbind('click').click(function () {
        $(".manualoutcomerow").remove();
        init_colorhistory_popup();
    });
    $(".inventoryoutcomedateinpt").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("span.saveinventoutcome").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'itemcolor', value: $("#colorinventory").val()});
        params.push({name: 'outcome_date', value: $("input.inventoryoutcomedateinpt").val()});
        params.push({name: 'outcome_recnum', value: $("input.inventoutcomerecnum").val()});
        params.push({name: 'outcome_descript', value: $("input.inventoutcomedescripinpt").val()});
        params.push({name: 'outcome_qty', value: $("input.inventoutcomeqtyinpt").val()});
        var url = '/masterinventory/save_color_outcome';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventHistory").find('div.modal-body').empty().html(response.data.content);
                $("#invenorynewhistoryadd").val(1);
                init_colorhistory_popup();
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
            action: '/utils/save_itemprooftemplate',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            allowedExtensions: ['ai','AI','pdf','PDF'],
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
                    params.push({name: 'doc_source', value: responseJSON.source});
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
            action: '/utils/save_itemprooftemplate',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            allowedExtensions: ['ai','AI','pdf','PDF'],
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
                    params.push({name: 'doc_source', value: responseJSON.source});
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
            action: '/utils/save_itemplatetemplate',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            allowedExtensions: ['ai','AI'],
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
                    params.push({name: 'doc_source', value: responseJSON.source});
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
            action: '/utils/save_itemplatetemplate',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            allowedExtensions: ['ai','AI'],
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
                    params.push({name: 'doc_source', value: responseJSON.source});
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
            action: '/utils/save_itemboxtemplate',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg','JPG','jpeg','JPEG','png','PNG','pdf','PDF'],
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
                    params.push({name: 'doc_source', value: responseJSON.source});
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
            action: '/utils/save_itemboxtemplate',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            allowedExtensions: ['jpg','JPG','jpeg','JPEG','png','PNG','pdf','PDF'],
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
                    params.push({name: 'doc_source', value: responseJSON.source});
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

function init_mastercolor_popup() {
    $(".edititembutton").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'item', value: 0});
        params.push({name: 'color', value: $(this).data('color')});
        params.push({name: 'editmode', value: 1});
        var url='/masterinventory/get_inventory_color';
        $.post(url,params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventColorLabel").empty().html(response.data.wintitle);
                $("#modalEditInventColor").find('div.modal-body').empty().html(response.data.winbody);
                $("#modalEditInventColor").find('div.modal-footer').empty().html(response.data.winfooter);
                $("#modalEditInventColor").modal({keyboard: false, show: true});
                init_mastercolor_popup();
            } else {
                show_error(response);
            }
        },'json');
    });

    $(".invcolor").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#invsessioin").val()});
        params.push({name: 'fld', value: $(this).data('item')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/masterinventory/inventory_color_change';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".colorvendornameinpt").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#invsessioin").val()});
        params.push({name: 'vendlist', value: $(this).data('list')});
        params.push({name: 'fld', value: 'vendor_id'});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/masterinventory/inventory_colorvendor_change';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".colorvendorpriceinpt").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#invsessioin").val()});
        params.push({name: 'vendlist', value: $(this).data('list')});
        params.push({name: 'fld', value: 'price'});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/masterinventory/inventory_colorvendor_change';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".statusradiobtn").unbind('click').click(function () {
        var newstatus = $(this).data('status');
        var params = new Array();
        params.push({name: 'session', value: $("#invsessioin").val()});
        params.push({name: 'fld', value: 'color_status'});
        params.push({name: 'newval', value: newstatus});
        var url = '/masterinventory/inventory_color_change';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".statusradiobtn[data-status='1']").empty().html(response.data.activebnt);
                $(".statusradiobtn[data-status='0']").empty().html(response.data.inactivebnt);
            } else {
                show_error(response);
            }
        },'json');

    });

    $(".closeitemdata").unbind('click').click(function () {
        $("#modalEditInventColor").modal('hide');
    });

    $(".saveitemdata").unbind('click').click(function () {
        // Collect data for save
        var params = new Array();
        params.push({name: 'session', value: $("#invsessioin").val()});
        var url = "/masterinventory/mastercolor_save";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#modalEditInventColor").modal('hide');
                init_master_inventorydata();
            } else {
                show_error(response);
            }
        },'json');
    });
    init_uploadfiles_mastercolor();
}

function init_uploadfiles_mastercolor() {
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button replacebutton">[Replace]</div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';
    if ($("#pic-uploader").length > 0) {
        var uploadproof = new qq.FileUploader({
            element: document.getElementById('pic-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: 'Upload',
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    var url='/masterinventory/mastercolor_image_change';
                    var params=new Array()
                    params.push({name: 'session', value: $("#invsessioin").val()});
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_src', value: responseJSON.source});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".colorimagedata").empty().html(response.data.content);
                            init_uploadfiles_mastercolor();
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

    if ($("#picnew-uploader").length > 0) {
        var uploadnewproof = new qq.FileUploader({
            element: document.getElementById('picnew-uploader'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            template: upload_templ,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $(".qq-upload-list").hide();
                    var url='/masterinventory/mastercolor_image_change';
                    var params=new Array()
                    params.push({name: 'session', value: $("#invsessioin").val()});
                    params.push({name: 'doc_url', value: responseJSON.filename});
                    params.push({name: 'doc_src', value: responseJSON.source});
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".colorimagedata").empty().html(response.data.content);
                            init_uploadfiles_mastercolor();
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