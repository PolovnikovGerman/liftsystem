$(document).ready(function(){
    var act = '';
    if ($(".headmenuitem.active").length==1) {
        act = $(".headmenuitem.active").data('lnk');
    } else {
        act = $(".headmenuitem").first().data('lnk');
        $(".headmenuitem").first().addClass('active');
    }
    show_dbcenter_mastercontent(act);
    init_dbcenter_mastermenu();
})

function show_dbcenter_mastercontent(act) {

    if (act=='mastercustomer') {

    } else if (act=='mastervendors') {
        $("#vendorsview").show();
        init_vendorpage();
    } else if (act=='masterinventory') {

    } else if (act=='mastersettings') {

    }
}

function init_dbcenter_mastermenu() {
    $(".headmenuitem").unbind('click').click(function () {
        $(".headmenuitem").removeClass('active');
        var act = $(this).data('lnk');
        $(".dbcontentarea").hide();
        show_dbcenter_mastercontent(act);
    });
}