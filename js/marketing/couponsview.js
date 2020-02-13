$(document).ready(function(){
    init_couponedit();
});
function addcoupon() {
    var coupon_id=$("#coupon_id").val();
    var chkv=$('#pub').prop('checked');
    if (chkv==true) {
        var pub=1;
    } else {
        pub=0;
    }
    var discount_perc=$("#discount_perc").val();
    var discount_sum=$("#discount_sum").val();
    var minlimit=$("#minlimit").val();
    var maxlimit=$("#maxlimit").val();
    var description=$("#description").val();
    var code1=$("#code1").val();
    var code2=$("#code2").val();
    var url='/coupons/updcoupon';
    $.post(url, {'coupon_id':coupon_id,'pub':pub,'discount_perc':discount_perc,'discount_sum':discount_sum,'minlimit':minlimit,'maxlimit':maxlimit,
        'description':description,'code1':code1,'code2':code2}, function(data){
        if (data.error!='') {
            alert(data.error);
        } else {
            $("#tabinfo").empty().html(data.content);
            init_couponedit();
            $("#coupon_id").val(0);
            $('#pub').removeAttr('checked');
            $("#discount_perc").val('');
            $("#discount_sum").val('');
            $("#minlimit").val('');
            $("#maxlimit").val('');
            $("#description").val('');
            $("#code1").val('');
            $("#code2").val('');
        }
    }, 'json');
}

function editcoupon(obj) {
    var objid=obj.id.substr(3);
    /* Get data about coupon */
    var url='/coupons/coupon_details';
    $.post(url, {'coupon_id':objid}, function(data){
        if (data.error=='') {
            $("#coupon_id").val(data.coupon_id);
            if (data.coupon_ispublic==1) {
                $('#pub').attr('checked','checked');
            } else {
                $('#pub').removeAttr('checked');
            }
            $("#discount_perc").val(data.coupon_discount_perc);
            $("#discount_sum").val(data.coupon_discount_sum);
            $("#minlimit").val(data.coupon_minlimit);
            $("#maxlimit").val(data.coupon_maxlimit);
            $("#description").val(data.coupon_description);
            $("#code1").val(data.coupon_code.substr(0,3));
            $("#code2").val(data.coupon_code.substr(4,3));
        } else {
            alert(data.error);
        }
    }, 'json');
}

function delcoupon(obj) {
    var objid=obj.id.substr(3);
    if (confirm('Are you sure?')) {
        var url='/coupons/del_coupon';
        $.post(url, {'coupon_id':objid}, function(data){
            if (data.error=='') {
                $("#tabinfo").empty().html(data.content);
                // $(".overflowtext").textOverflow();
                $("#coupon_id").val(0);
                $('#pub').removeAttr('checked');
                $("#discount_perc").val('');
                $("#discount_sum").val('');
                $("#minlimit").val('');
                $("#maxlimit").val('');
                $("#description").val('');
                $("#code1").val('');
                $("#code2").val('');
            } else {
                alert(data.error);
            }
        }, 'json')
        return true;
    } else {
        return false;
    }
}

function init_couponedit() {
    // $(".overflowtext").textOverflow();
    $("input.couponactivchk").unbind('change').change(function(){
        var activ=0;
        if ($(this).prop('checked')==false) {
            activ=1;
        }
        var data=new Array();
        data.push({name:'coupon_id', value: $(this).data('couponid')});
        data.push({name:'old_active', value: activ});
        var url="/coupons/coupon_activate";
        $.post(url, data, function(response){

        },'json');
    });
}