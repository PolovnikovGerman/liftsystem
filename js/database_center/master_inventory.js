function init_master_inventory() {
    init_master_inventorydata();
    init_master_inventorycontent();
}

function init_master_inventorydata() {
    var params = new Array();
    params.push({name: 'inventory_type', value: $("#active_invtype").val()});
    params.push({name: 'inventory_filter', value: $(".inventfilterselect").val()});
    var url="/masterinventory/get_inventory_list";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".masterinventtablebody").empty().html(response.data.content);
            $(".inventtotalinstock").empty().html(response.data.instock);
            $(".inventtotalavailable").empty().html(response.data.available);
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
    })
}