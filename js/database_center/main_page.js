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
    });
    $(".dbcenter_channel_item").unbind('click').click(function() {
        var start = $(this).data('start');
        var brand = $(this).data('brand');
        var url = '/databasecenter/channelitems';
        var firstparam = 1;
        if (brand) {
            url=url + '/?brand='+brand;
            firstparam = 0;
        }
        if (start) {
            if (firstparam==1) {
                url=url + '/?start='+start;
            } else {
                url=url + '&start='+start;
            }

        }
        window.location.href = url;

    });
}
