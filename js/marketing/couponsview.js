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
    $(".addnewcoupon").unbind('click').click(function(){
        edit_coupon(0);
    })
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
            init_coupon_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_coupon_content() {
    $("div.coupon_edit").unbind('click').click(function(){
        var coupon = $(this).data('coupon');
        edit_coupon(coupon);
    });
    $("div.activedoc").unbind('click').click(function(){
        var coupon = $(this).data('coupon');
        if (confirm('Change coupon status?')==true) {
            var params = new Array();
            params.push({name: 'coupon_id', value: coupon});
            var url = '/marketing/coupon_activate';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("div.activedoc[data-coupon='"+coupon+"']").empty().html(response.data.content);
                    init_coupon_content();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $("div.coupon_delete").unbind('click').click(function () {
        var coupon = $(this).data('coupon');
        if (confirm('Delete coupon?')==true) {
            var params = new Array();
            params.push({name: 'coupon_id', value: coupon});
            var url = '/marketing/coupon_delete';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $('#totalcoupon').val(response.data.total);
                    initCouponsPagination();
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
}

function edit_coupon(coupon) {
    var params = new Array();
    params.push({name: 'coupon_id', value: coupon});
    var url = '/marketing/coupon_details';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.label);
            $("#pageModal").find('div.modal-dialog').css('width','533px');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            $('#coupon_ispublic').bootstrapToggle({
                'size' : 'mini',
                'style' : 'ios'
            });
            if (response.data.percent_lock=='1') {
                $("#coupon_discount_perc").prop('disabled', true);
            }
            if (response.data.money_lock=='1') {
                $("#coupon_discount_sum").prop('disabled', true);
            }
            init_coupondetails_edit();
        } else {
            show_error(response);
        }

    },'json');
}

function init_coupondetails_edit() {
    $("#coupon_discount_perc").unbind('change').change(function(){
        var newval = parseFloat($(this).val());
        if (isNaN(newval)==true) {
            $("#coupon_discount_perc").val('');
            $("#coupon_discount_sum").prop('disabled', false);
        } else {
            $("#coupon_discount_sum").prop('disabled', true);
        }
    });
    $("#coupon_discount_sum").unbind('change').change(function(){
        var newval = parseFloat($(this).val());
        if (isNaN(newval)==true) {
            $("#coupon_discount_sum").val('');
            $("#coupon_discount_perc").prop('disabled', false);
        } else {
            $("#coupon_discount_perc").prop('disabled', true);
        }
    });
    $("#coupondetailsave").unbind('click').click(function () {
        var active = 0;
        if ($("#coupon_ispublic").prop('checked')==true) {
            active = 1;
        }
        var params = new Array();
        params.push({name: 'coupon_id', value: $("#coupon_id").val()});
        params.push({name: 'coupon_ispublic', value: active});
        params.push({name: 'brand', value: $("#coupon_brand").val()});
        params.push({name: 'coupon_discount_perc', value: $("#coupon_discount_perc").val()});
        params.push({name: 'coupon_discount_sum', value: $("#coupon_discount_sum").val()});
        params.push({name: 'coupon_minlimit', value: $("#coupon_minlimit").val()});
        params.push({name: 'coupon_maxlimit', value: $("#coupon_maxlimit").val()});
        params.push({name: 'coupon_code1', value: $("#coupon_code1").val()});
        params.push({name: 'coupon_code2', value: $("#coupon_code2").val()});
        params.push({name: 'coupon_description', value: $("#coupon_description").val()});
        var url = '/marketing/coupon_details_save';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $('#totalcoupon').val(response.data.total);
                $("#pageModal").modal('hide');
                initCouponsPagination();
            } else {
                show_error(response);
            }
        },'json')
    });
}