// View content
function init_itemdetails_view() {
    var attribtemplate='<div class="popover blue_background"  role="tooltip"><div class="arrow"></div><div class="popover-content attrib_tooltip"></div></div>';
    $("#slider").easySlider({
        nextText : '',
        prevText : '',
        vertical : false
    });
    $(".closeitemdetails").click(function(){
        close_view();
    });
    $(".bottomtxtlnk").click(function(){
        var item=$(this).data('item');
        show_bottom_text(item)
    })
    $(".commontermslnk").click(function(){
        var item=$(this).data('item');
        show_common_terms(item);
    })
    $("div.checkoutspeciallnk").unbind('click').click(function(){
        var item=$(this).data('item');
        show_checkout_special(item);
    });
    $("div.location_upload").unbind('click').click(function () {
        var imgsrc = $(this).data('srclink');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    })
    $("div.pictures").find('.pic').unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".imprintpopover").popover({
        html: true,
        trigger: 'hover',
        placement: 'top'
    });
    $("td.competitorname").popover({
        html: true,
        trigger: 'hover',
        placement: 'right'
    });
    $(".tooltip-descript").popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        template: attribtemplate
    });
    $("div.shipcondinlink").click(function(){
        var item=$(this).data('item');
        show_shipping(item);
    });
    // $(".viewvideo").click(function(){
    //     show_video();
    // })
    // $("div.activate_btn").click(function(){
    //     activate_edit();
    // });
    // // Competitors title
    // $("td.competitorname").bt({
    //     fill: '#FFFFFF',
    //     positions: ['right'],
    //     cornerRadius: 10,
    //     width: 128,
    //     padding: 15,
    //     strokeWidth: 2,
    //     strokeStyle : '#000000',
    //     strokeHeight: 18,
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // });

}
// VIEW Functions
/* Close Preview */
function close_view() {
    var url='/database/restore_databaseview'
    $.post(url, {}, function (response) {
        if (response.errors=='') {
            var pagename = response.data.pagename;
            if (pagename=='categview') {
                init_page('itemcategoryview');
            }
        } else {
            show_errors(response);
        }
    },'json');
}

function show_bottom_text(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'param', value: 'bottom_text'});
    var url='/itemdetails/view_footer';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            // editModal
            $("#editModalLabel").empty().html('View Bottom Text');
            $("#editModal").find('.modal-dialog').css('width','492px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');
}

function show_common_terms(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    params.push({name: 'param', value: 'common_terms'});
    var url='/itemdetails/view_footer';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            // editModal
            $("#editModalLabel").empty().html('View Common Terms');
            $("#editModal").find('.modal-dialog').css('width','352px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');
}

function show_checkout_special(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    var url="/itemdetails/view_specialcheck";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#editModalLabel").empty().html('View Checkout Specials');
            $("#editModal").find('.modal-dialog').css('width','564px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {
            show_error(response);
        }
    },'json');

}

function show_shipping(item) {
    var params=new Array();
    params.push({name: 'item_id', value: item});
    var url='/itemdetails/view_shipping';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#editModalLabel").empty().html('View Shipping Details');
            $("#editModal").find('.modal-dialog').css('width','468px');
            $("#editModal").find('div.modal-body').empty().html(response.data.content);
            $("#editModal").modal('show');
        } else {

        }
    },'json');
}