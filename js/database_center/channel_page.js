$(document).ready(function(){
    var act = '';
    if ($(".headmenuitem.active").length==1) {
        act = $(".headmenuitem.active").data('lnk');
    } else {
        act = $(".headmenuitem").first().data('lnk');
        $(".headmenuitem").first().addClass('active');
    }
    show_dbcenter_channelcontent(act);
    init_dbcenter_channelmenu();
})

function show_dbcenter_channelcontent(act) {
    if (act=='sbitems') {
        $("#sbitemsview").show();
        init_itemslist_view('SB');
    }
}

function init_dbcenter_channelmenu() {
    $(".headmenuitem").unbind('click').click(function () {
        $(".headmenuitem").removeClass('active');
        var act = $(this).data('lnk');
        $(this).addClass('active');
        $(".dbcontentarea").hide();
        show_dbcenter_channelcontent(act);
    });
    $(".returndbcenter").unbind('click').click(function () {
        window.location.href = '/databasecenter';
    })
}