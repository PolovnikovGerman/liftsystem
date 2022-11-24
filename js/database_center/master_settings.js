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