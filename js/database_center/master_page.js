$(document).ready(function(){
    var act = '';
    if ($(".maincontentmenu_item.active").length==1) {
        act = $(".maincontentmenu_item.active").data('link');
    } else {
        act = $(".maincontentmenu_item").first().data('link');
        $(".maincontentmenu_item").first().addClass('active');
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
        $("#inventoryview").show();
        init_master_inventory();
    } else if (act=='mastersettings') {
        $("#settingsview").show();
        init_master_settings();
    }
}

function init_dbcenter_mastermenu() {
    $(".maincontentmenu_item").unbind('click').click(function () {
        $(".maincontentmenu_item").removeClass('active');
        var act = $(this).data('link');
        $(this).addClass('active');
        $(".dbcontentarea").hide();
        show_dbcenter_mastercontent(act);
    });
    $(".dbcenter-main-button").unbind('click').click(function () {
        var url = '/databasecenter?start='+$(this).data('link');
        window.location.href=url;
    })
}

function init_master_settings() {
    var act = '';
    if ($(".mastersettingsmenu_item.active").length==1) {
        act = $(".mastersettingsmenu_item.active").data('itemlnk');
    } else {
        act = $(".mastersettingsmenu_item").first().data('itemlnk');
        $(".mastersettingsmenu_item").first().addClass('active');
    }
    show_dbcenter_mastresettings(act);
    init_dbcenter_mastersetingsmenu();
}

function show_dbcenter_mastresettings(act) {
    if (act=='mastercalendars') {
        $("#calendarsview").show();
        init_calendars_page()
    } else if (act=='mastercountries') {
        $("#countriesview").show();
        init_countries();
    }
}

function init_dbcenter_mastersetingsmenu() {
    $(".mastersettingsmenu_item").unbind('click').click(function () {
        $(".mastersettingsmenu_item").removeClass('active');
        var act = $(this).data('itemlnk');
        $(this).addClass('active');
        $(".settingcontentarea").hide();
        show_dbcenter_mastresettings(act);
    });
}