function init_manageproj_content() {
    var SimplOrder = new SimpleBar(document.getElementById('orders-table'), { autoHide: false });
    var SimpleLeads = new SimpleBar(document.getElementById('leads-table'), { autoHide: false });
    var SimpleContacts = new SimpleBar(document.getElementById('contacts-table'), { autoHide: false });
    var SimpleAddres = new SimpleBar(document.getElementById('address-table'), { autoHide: false });
    // Hide all
    $(".custom-info-hide").unbind('click').click(function(){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide==1) {
            $(".custom-info-hide").empty().html('Hide <span class="ci-hideicon"><i class="fa fa-chevron-up" aria-hidden="true"></i></span>');
            $(".custom-info-hide").removeClass('hidetitle');
            // Contacts List
            $(".custinfo-titlebox[data-section='emailcontacts']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='emailcontacts']").removeClass('hidetitle');
            $("#customercontactslist").show();
            // Add'l Info
            $(".custinfo-titlebox[data-section='customeradditinfo']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customeradditinfo']").removeClass('hidetitle');
            $("#customeradditinfo").show();
            // Credits
            $(".custinfo-titlebox[data-section='customercreditlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customercreditlist']").removeClass('hidetitle');
            $("#customercreditlist").show();
            // Orders
            $(".custinfo-titlebox[data-section='customerorderslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerorderslist']").removeClass('hidetitle');
            $("#customerorderslist").show();
            // Leads
            $(".custinfo-titlebox[data-section='customerleadlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerleadlist']").removeClass('hidetitle');
            $("#customerleadlist").show();
            // Pay Methods
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").removeClass('hidetitle');
            $("#customerpaymethodslist").show();
            // Ship Accounts
            $(".custinfo-titlebox[data-section='customershipacclist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customershipacclist']").removeClass('hidetitle');
            $("#customershipacclist").show();
            // Address
            $(".custinfo-titlebox[data-section='customeraddresslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customeraddresslist']").removeClass('hidetitle');
            $("#customeraddresslist").show();
        } else {
            $(".custom-info-hide").empty().html('Show <span class="ci-hideicon"><i class="fa fa-chevron-down" aria-hidden="true"></i></span>');
            $(".custom-info-hide").addClass('hidetitle');
            // Contacts List
            $(".custinfo-titlebox[data-section='emailcontacts']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>')
            $(".custinfo-titlebox[data-section='emailcontacts']").addClass('hidetitle');
            $("#customercontactslist").hide();
            // Add'l Info
            $(".custinfo-titlebox[data-section='customeradditinfo']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>')
            $(".custinfo-titlebox[data-section='customeradditinfo']").addClass('hidetitle');
            $("#customeradditinfo").hide();
            // Credits
            $(".custinfo-titlebox[data-section='customercreditlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customercreditlist']").removeClass('hidetitle');
            $("#customercreditlist").hide();
            // Orders
            $(".custinfo-titlebox[data-section='customerorderslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerorderslist']").addClass('hidetitle');
            $("#customerorderslist").hide();
            // Leads
            $(".custinfo-titlebox[data-section='customerleadlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerleadlist']").addClass('hidetitle');
            $("#customerleadlist").hide();
            // Pay Methods
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").addClass('hidetitle');
            $("#customerpaymethodslist").hide();
            // Ship Accounts
            $(".custinfo-titlebox[data-section='customershipacclist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customershipacclist']").addClass('hidetitle');
            $("#customershipacclist").hide();
            // Address
            $(".custinfo-titlebox[data-section='customeraddresslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customeraddresslist']").addClass('hidetitle');
            $("#customeraddresslist").hide();
        }
    });
    // Hide / Show Emails List
    $(".custinfo-titlebox[data-section='emailcontacts']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='emailcontacts']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='emailcontacts']").removeClass('hidetitle');
            $("#customercontactslist").show();
        } else {
            $(".custinfo-titlebox[data-section='emailcontacts']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>')
            $(".custinfo-titlebox[data-section='emailcontacts']").addClass('hidetitle');
            $("#customercontactslist").hide();
        }
    });
    // Hide / Show Add'l Info
    $(".custinfo-titlebox[data-section='customeradditinfo']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customeradditinfo']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>')
            $(".custinfo-titlebox[data-section='customeradditinfo']").removeClass('hidetitle');
            $("#customeradditinfo").show();
        } else {
            $(".custinfo-titlebox[data-section='customeradditinfo']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>')
            $(".custinfo-titlebox[data-section='customeradditinfo']").addClass('hidetitle');
            $("#customeradditinfo").hide();
        }
    });
    // Hide Show Credits
    $(".custinfo-titlebox[data-section='customercreditlist']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customercreditlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customercreditlist']").removeClass('hidetitle');
            $("#customercreditlist").show();
        } else {
            $(".custinfo-titlebox[data-section='customercreditlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customercreditlist']").removeClass('hidetitle');
            $("#customercreditlist").hide();
        }
    })
    // Hide Show Orders
    $(".custinfo-titlebox[data-section='customerorderslist']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customerorderslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerorderslist']").removeClass('hidetitle');
            $("#customerorderslist").show();
        } else {
            $(".custinfo-titlebox[data-section='customerorderslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerorderslist']").addClass('hidetitle');
            $("#customerorderslist").hide();
        }
    });
    // Hide Show Leads
    $(".custinfo-titlebox[data-section='customerleadlist']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customerleadlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerleadlist']").removeClass('hidetitle');
            $("#customerleadlist").show();
        } else {
            $(".custinfo-titlebox[data-section='customerleadlist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerleadlist']").addClass('hidetitle');
            $("#customerleadlist").hide();
        }
    });
    // Hide Show Pay Methods
    $(".custinfo-titlebox[data-section='customerpaymethodslist']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").removeClass('hidetitle');
            $("#customerpaymethodslist").show();
        } else {
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customerpaymethodslist']").addClass('hidetitle');
            $("#customerpaymethodslist").hide();
        }
    });
    // Hide Show Ship Accounts
    $(".custinfo-titlebox[data-section='customershipacclist']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customershipacclist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customershipacclist']").removeClass('hidetitle');
            $("#customershipacclist").show();
        } else {
            $(".custinfo-titlebox[data-section='customershipacclist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customershipacclist']").addClass('hidetitle');
            $("#customershipacclist").hide();
        }
    });
    // Address
    $(".custinfo-titlebox[data-section='customeraddresslist']").unbind('click').click(function (){
        var blockhide = 0;
        if ($(this).hasClass('hidetitle')) {
            blockhide = 1;
        }
        if (blockhide == 1) {
            $(".custinfo-titlebox[data-section='customeraddresslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customeraddresslist']").removeClass('hidetitle');
            $("#customeraddresslist").show();
        } else {
            $(".custinfo-titlebox[data-section='customeraddresslist']").find('span.titlebox-arrow').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
            $(".custinfo-titlebox[data-section='customeraddresslist']").addClass('hidetitle');
            $("#customeraddresslist").hide();
        }
    });
}

function init_leadsorders_content() {
    var SimpleLeads = new SimpleBar(document.getElementById('olpleadstabl-body'), { autoHide: false });
    var SimplOrder = new SimpleBar(document.getElementById('olporderstabl-body'), { autoHide: false });
}

function init_leadquote_content() {
    var SimpleLeadquote = new SimpleBar(document.getElementById('leadquotetable'), { autoHide: false });
    var SimpleProofreq = new SimpleBar(document.getElementById('list-proofreqbox'), { autoHide: false });
}