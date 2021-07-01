$(document).ready(function(){
    init_dbcenter_main();
});

function init_dbcenter_main() {
    $(".dbcenter_master_item").unbind('click').click(function () {
        var start = $(this).data('lnk');
        var url = '/databasecenter/masteritems';
        if (start) {
            url=url + '/?start='+start;
        }
        window.location.href = url;
    })
}