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

}