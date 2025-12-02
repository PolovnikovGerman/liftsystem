function init_doubleorders() {
    $("#dualordspecialtags").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordspecialtags").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordspecialtags").removeClass("hidetitle");
            $("#dualordspecialtags").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".infodetelies").removeClass("hideinfo");
        } else {
            $("#dualordspecialtags").addClass("hidetitle");
            $("#dualordspecialtags").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".infodetelies").addClass("hideinfo");
        }
    });
    $("#dualordcredits").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordcredits").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordcredits").removeClass("hidetitle");
            $("#dualordcredits").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordcredits").addClass("hidetitle");
            $("#dualordcredits").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
    $("#dualordpaymethods").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordpaymethods").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordpaymethods").removeClass("hidetitle");
            $("#dualordpaymethods").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordpaymethods").addClass("hidetitle");
            $("#dualordpaymethods").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
    $("#dualordshipaccounts").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordshipaccounts").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordshipaccounts").removeClass("hidetitle");
            $("#dualordshipaccounts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordshipaccounts").addClass("hidetitle");
            $("#dualordshipaccounts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
    $("#dualordcontacts").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordcontacts").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordcontacts").removeClass("hidetitle");
            $("#dualordcontacts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordcontacts").addClass("hidetitle");
            $("#dualordcontacts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
    $("#dualordorderslist").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordorderslist").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordorderslist").removeClass("hidetitle");
            $("#dualordorderslist").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordorderslist").addClass("hidetitle");
            $("#dualordorderslist").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
    $("#dualordquotes").find('span.titlebox-arrow').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordquotes").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $("#dualordquotes").removeClass("hidetitle");
            $("#dualordquotes").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
        } else {
            $("#dualordquotes").addClass("hidetitle");
            $("#dualordquotes").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
        }
    });
    $("#dualordcustomlabel").find('div.custom-info-hide').unbind('click').click(function () {
        var blockhide = 0;
        if ($("#dualordcustomlabel").hasClass("hidetitle")) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".tagslist-title").show();
            $(".custinfo-tags").show();
            $(".custinfo-member").show();
            $("#dualordcustomlabel").find('div.custom-info-hide').empty().html('Hide <span class="ci-hideicon"><i class="fa fa-chevron-up" aria-hidden="true"></i></span>');
            $("#dualordcustomlabel").removeClass("hidetitle");
            // Other Blocks
            $("#dualordcredits").removeClass("hidetitle");
            $("#dualordcredits").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $("#dualordpaymethods").removeClass("hidetitle");
            $("#dualordpaymethods").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $("#dualordshipaccounts").removeClass("hidetitle");
            $("#dualordshipaccounts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $("#dualordcontacts").removeClass("hidetitle");
            $("#dualordcontacts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $("#dualordorderslist").removeClass("hidetitle");
            $("#dualordorderslist").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $("#dualordquotes").removeClass("hidetitle");
            $("#dualordquotes").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $("#dualordspecialtags").removeClass("hidetitle");
            $("#dualordspecialtags").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".infodetelies").removeClass("hideinfo");
        } else {
            $(".tagslist-title").hide();
            $(".custinfo-tags").hide();
            $(".custinfo-member").hide();
            $("#dualordcustomlabel").find('div.custom-info-hide').empty().html('Show <span class="ci-hideicon"><i class="fa fa-chevron-down" aria-hidden="true"></i></span>');
            $("#dualordcustomlabel").addClass("hidetitle");
            // Other Blocks
            $("#dualordcredits").addClass("hidetitle");
            $("#dualordcredits").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $("#dualordpaymethods").addClass("hidetitle");
            $("#dualordpaymethods").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $("#dualordshipaccounts").addClass("hidetitle");
            $("#dualordshipaccounts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $("#dualordcontacts").addClass("hidetitle");
            $("#dualordcontacts").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $("#dualordorderslist").addClass("hidetitle");
            $("#dualordorderslist").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $("#dualordquotes").addClass("hidetitle");
            $("#dualordquotes").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $("#dualordspecialtags").addClass("hidetitle");
            $("#dualordspecialtags").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".infodetelies").addClass("hideinfo");
        }
        init_doubleorders();
    });
}