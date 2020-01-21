// View content
function init_itemdetails_view() {
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
         show_checkout_special();
    });
    // $("a.gallery").fancybox();
    // $(".tooltip").bt({
    //     fill: '#5353f0',
    //     positions: ['top','left'],
    //     cornerRadius: 10,
    //     width: 180,
    //     padding: 20,
    //     strokeWidth: 2,
    //     strokeStyle : '#FFFFFF',
    //     strokeHeight: 18,
    //     cssClass: 'right_tooltip',
    //     cssStyles: {color: '#FFFFF'}
    // });
    // $(".tooltip-descript").bt({
    //     fill: '#5353f0',
    //     positions: 'left',
    //     cornerRadius: 10,
    //     width: 180,
    //     padding: 20,
    //     strokeWidth: 2,
    //     strokeStyle : '#FFFFFF',
    //     strokeHeight: 18,
    //     cssClass: 'right_tooltip',
    //     cssStyles: {color: '#FFFFF'}
    // });
    // $(".viewvideo").click(function(){
    //     show_video();
    // })
    // $("div.activate_btn").click(function(){
    //     activate_edit();
    // });
    // $("div.shipcondinlink").click(function(){
    //     show_shipping();
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

function show_bottom_text((item) {
    
}