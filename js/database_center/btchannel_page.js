$(document).ready(function(){
    var act = '';
    if ($(".maincontentmenu_item.active").length==1) {
        act = $(".maincontentmenu_item.active").data('link');
    } else {
        act = $(".maincontentmenu_item").first().data('link');
        $(".maincontentmenu_item").first().addClass('active');
    }
    show_dbcenter_channelcontent(act);
    init_dbcenter_channelmenu();
})

function show_dbcenter_channelcontent(act) {
    if (act=='btitems') {
        $("#btitemsview").show();
        init_btitemslist_view();
    } else if (act=='sbpages') {
        $("#sbpagesview").show();
        init_sitecontent('SB');
    } else if (act=='btpages') {
        $("#btpagesview").show();
        // init_sitecontent('BT');
    } else if (act=='btcustomers') {
        $("#customersview").show();
    } else if (act=='btsettings') {
        $("#settingsview").show();
    }
}

function init_dbcenter_channelmenu() {
    $(".maincontentmenu_item").unbind('click').click(function () {
        $(".maincontentmenu_item").removeClass('active');
        var act = $(this).data('link');
        $(this).addClass('active');
        $(".dbcontentarea").hide();
        show_dbcenter_channelcontent(act);
    });
    $(".dbcenter-main-button").unbind('click').click(function () {
        var url = '/databasecenter?start='+$(this).data('link');
        window.location.href=url;
    })
}