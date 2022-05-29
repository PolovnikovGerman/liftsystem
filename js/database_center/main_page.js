$(document).ready(function(){
    // var url=$(".dbcenter-main-button").first().data('link');
    // activate_dbcenter(url);
    // $(".dbcenter-main-button").first().addClass('active');
    $(".dbcenter-main-button").unbind('click').click(function () {
        var url = $(this).data('link');
        $(".dbcenter-main-button").removeClass('active');
        $(this).addClass('active');
        activate_dbcenter(url);
    })
});

function activate_dbcenter(dburl) {
    var params=new Array();
    params.push({name: 'url', value: dburl});
    var url = '/databasecenter/show_channel';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".dbcenter_content").empty().html(response.data.content);
            activate_channel_content();
        } else {
            show_error(response);
        }
    },'json');
}

function activate_channel_content() {

}
/*
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
*/
