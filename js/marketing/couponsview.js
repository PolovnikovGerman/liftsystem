function init_coupon_view() {
    // Change Brand
    $("#couponmanagebrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#signupemailbrand").val(brand);
        $("#couponmanagebrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#couponmanagebrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#couponmanagebrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        $("#curcoupon").val(0);
        initCouponsPagination();
    });
    initCouponsPagination();
}

function initCouponsPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalcoupon').val();
    var perpage = $("#perpagecoupon").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#couponPaginator").empty();
        $("input#curcoupon").val(0);
        pageCouponsCallback(0);
    } else {
        var curpage = $("#cursign").val();
        // Create content inside pagination element
        $("#couponPaginator").mypagination(num_entries, {
            current_page: curpage,
            callback: pageCouponsCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageCouponsCallback(page_index) {
    var params = new Array();
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagecoupon").val()});
    params.push({name: 'order_by', value: $("#ordercoupon").val()});
    params.push({name: 'direction', value: $("#directcoupon").val()});
    params.push({name: 'brand', value: $("#signupemailbrand").val()});
    var url = "/marketing/couponsdat";
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".coupondata_content").empty().html(response.data.content);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
    
}