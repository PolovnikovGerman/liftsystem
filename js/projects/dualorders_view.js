function init_doubleorders() {
    $("#dualordspecialtags").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordspecialtags").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordspecialtags").removeClass("hidetitle");
            $("#dualordspecialtags").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordspecialtags").addClass("hidetitle");
            $("#dualordspecialtags").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
}