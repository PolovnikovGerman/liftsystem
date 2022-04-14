function init_accounts_receivable() {
    init_accreceive_totals();
    init_accreceive_details();
    $(".accreceiv-period-select").unbind('change').change(function(){
        init_accreceive_totals();
        init_accreceive_details();
    });
    // Change Brand
    $("#accreceivebrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#accreceivebrand").val(brand);
        $("#accreceivebrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#accreceivebrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#accreceivebrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_accreceive_totals();
        init_accreceive_details();
    });
}

function init_accreceive_totals() {
    var params = new Array();
    params.push({name: 'brand', value: $("#accreceivebrand").val()});
    params.push({name: 'period', value: $(".accreceiv-period-select").val()});
    var url = '/accounting/accountreceiv_totals';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".accreceiv-content-left").find(".accreceiv-details-totals").empty().html(response.data.totalown);
            $(".accreceiv-content-center").find(".accreceiv-details-totals").empty().html(response.data.totalrefund);
            $(".accrecive-totalsarea").empty().html(response.data.totals);
            $(".accreceiv-content-right").empty().html(response.data.totals);
        } else {
            show_error(response);
        }
    },'json');
}

function init_accreceive_details() {
    var params = new Array();
    var maxwidth = parseInt(window.innerWidth);
    params.push({name: 'brand', value: $("#accreceivebrand").val()});
    params.push({name: 'period', value: $(".accreceiv-period-select").val()});
    params.push({name: 'ownsort', value: $("#accreciveownsort").val()});
    params.push({name: 'owndirec', value: $("#accreciveowndir").val()});
    params.push({name: 'refundsort', value: $("#accreceiverefundsort").val()});
    params.push({name: 'refunddirec', value: $("#accreceiverefunddir").val()});
    params.push({name: 'maxwidth', value: maxwidth});
    var url = '/accounting/accountreceiv_details';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".accreceiv-content-left").find("div.accreceiv-details").empty().html(response.data.owndetails);
            $(".accreceiv-content-center").find("div.accreceiv-details").empty().html(response.data.refunddetails);
            init_accreceive_content();
        } else {
            show_error(response);
        }
    },'json');
}

function init_accreceive_content() {
    $(".accreceiv-owndetails-bodyrow").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        }
    );
    $(".accreceiv-refunddetails-bodyrow").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        }
    );

    $(".accreceiv-owndetails-bodyorder").unbind('click').click(function () {
        var order = $(this).data('order');
        var callpage = 'accrecive';
        var brand = $("#accreceivebrand").val();
        var url="/leadorder/leadorder_change";
        var params = new Array();
        params.push({name: 'order', value: order});
        params.push({name: 'page', value: callpage});
        params.push({name: 'edit', value: 0});
        params.push({name: 'brand', value: brand});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artModalLabel").empty().html(response.data.header);
                $("#artModal").find('div.modal-body').empty().html(response.data.content);
                $("#artModal").find('div.modal-dialog').css('width','1004px');
                $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
                $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
                if (parseInt(order)==0) {
                    init_onlineleadorder_edit();
                } else {
                    navigation_init();
                }
            } else {
                show_error(response);
            }
        },'json');
    })

    $(".accreceiv-refunddetails-bodyorder").unbind('click').click(function () {
        var order = $(this).data('order');
        var callpage = 'accrecive';
        var brand = $("#accreceivebrand").val();
        var url="/leadorder/leadorder_change";
        var params = new Array();
        params.push({name: 'order', value: order});
        params.push({name: 'page', value: callpage});
        params.push({name: 'edit', value: 0});
        params.push({name: 'brand', value: brand});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artModalLabel").empty().html(response.data.header);
                $("#artModal").find('div.modal-body').empty().html(response.data.content);
                $("#artModal").find('div.modal-dialog').css('width','1004px');
                $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
                $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
                if (parseInt(order)==0) {
                    init_onlineleadorder_edit();
                } else {
                    navigation_init();
                }
            } else {
                show_error(response);
            }
        },'json');
    })
    // Sorting own
    $(".ownsort").unbind('click').click(function () {
        var newsort = $(this).data('sort');
        var oldsort = $("#accreciveownsort").val();
        var newdir = 'desc';
        if (newsort==oldsort) {
            var olddir = $("#accreciveowndir").val();
            if (olddir=='desc') {
                newdir='asc';
            }
        }
        $("#accreciveownsort").val(newsort);
        $("#accreciveowndir").val(newdir);
        init_accreceive_details();
    });
    // Sorting refund
    $(".refundsort").unbind('click').click(function () {
        var newsort = $(this).data('sort');
        var oldsort = $("#accreceiverefundsort").val();
        var newdir = 'desc';
        if (newsort==oldsort) {
            var olddir = $("#accreceiverefunddir").val();
            if (olddir=='desc') {
                newdir='asc';
            }
        }
        $("#accreceiverefundsort").val(newsort);
        $("#accreceiverefunddir").val(newdir);
        init_accreceive_details();
    })

}