function init_master_inventory() {
    init_master_inventorydata();
}

function init_master_inventorydata() {
    var params = new Array();
    params.push({name: 'inventory_type', value: $("#active_invtype")});
    var url="/masterinventory/get_inventory_list";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".masterinventtablebody").empty().html(response.data.content);
        } else {
            show_error(response)
        }
    },'json')
}