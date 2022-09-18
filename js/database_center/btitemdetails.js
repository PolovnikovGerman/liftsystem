function init_btitemdetails_view(item) {
    $(".itemimagepreview").unbind('click').click(function () {
        // Show popup with images and colors
    });
    $(".itemvendorfilebtn.vectorfile").unbind('click').click(function(){
        var url = $(this).data('file');
        window.openai(url, 'AI Template');
    });
    $(".printlocexample").unbind('click').click(function () {
        var url = $(this).data('link');
        window.open(url, 'Print Location','left=120,top=120,width=600,height=600');
    });
    $(".edit_itemdetails").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'item_id', value: item});
        params.push({name: 'brand', value: 'BT'});
        params.push({name: 'editmode', value: 1});
        var url = '/dbitems/itemlistdetails';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModalLabel").empty().html(response.data.header);
                $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                // init_relievitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    })
}