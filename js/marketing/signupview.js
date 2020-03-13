function init_signup(){
    initSignupPagination();
    $("input#beginsignup").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("input#endsignup").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("input.signupdate").unbind('change').change(function () {
        apply_signup_filter();
    })

    $("#exportgignup").unbind().click(function () {
        export_signup();
    });
    // Change Brand
    $("#signupemailbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#signupemailbrand").val(brand);
        $("#signupemailbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#signupemailbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#signupemailbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        $("#cursign").val(0);
        apply_signup_filter();
    });
}

function init_signup_management() {
    var heighv=0;
    var widthv=0;
    heighv=$("#editmail_form").css('height');
    heighv=heighv.replace('px','');
    heighv=$.browser.msie?heighv+"px":heighv;
    widthv=$("#editmail_form").css('width');
    widthv=widthv.replace('px','');
    widthv=$.browser.msie?widthv+"px":widthv;
    // $("#editmail_form:ui-dialog").dialog("destroy");
}
/* Init pagination */
function initSignupPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalsign').val();
    var perpage = $("#perpagesign").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("#signupPagination").empty();
        $("input#cursign").val(0);
        pageSignupCallback(0);
    } else {
        var curpage = $("#cursign").val();
        // Create content inside pagination element
        $("#signupPagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageSignupCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 7,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageSignupCallback(page_index) {
    var params = new Array();
    params.push({name: 'startdate', value: $("#beginsignup").val()});
    params.push({name: 'enddate', value: $("#endsignup").val()});
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagesign").val()});
    params.push({name: 'order_by', value: $("#ordersign").val()});
    params.push({name: 'direction', value: $("#direcsign").val()});
    params.push({name: 'maxval', value: $('#totalsign').val()});
    params.push({name: 'type', value: 'Signups'});
    params.push({name: 'brand', value: $("#signupemailbrand").val()});
    var url = "/marketing/signupsdat";
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $('#tabinfo_left').empty().append(response.data.content_left);
            $('#tabinfo_right').empty().append(response.data.content_right);
            $("#cursign").val(page_index);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

// function change_type() {
//     var newtype=$("#email_types").val();
//     $.post('/emailsview/calcmessages', {'status':newtype,'email_type':'Signups'}, function(data){
//         $('#totalrec').val(data.numrecs);
//         $("#curpage").val(0);
//         initPagination();
//     }, 'json');
// }

function apply_signup_filter() {
    var params=new Array();
    params.push({name:'startdate', value: $("#beginsignup").val()});
    params.push({name:'enddate', value: $("#endsignup").val()});
    params.push({name: 'brand', value: $("#signupemailbrand").val()});
    var url='/marketing/count_signup';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $('#totalsign').val(response.data.totals);
            initSignupPagination();
        } else {
            show_error(response);
        }
    },'json')
}

function export_signup() {
    var params = new Array();
    params.push({name: 'startdate', value: $("#beginsignup").val()});
    params.push({name: 'enddate', value: $("#endsignup").val()});
    params.push({name: 'type', value: 'Signups'});
    params.push({name: 'brand', value: $("#signupemailbrand").val()});
    var url = "/marketing/export_signups";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            window.open(response.data.url);
        } else {
            show_error(response);
        }
    },'json');

}