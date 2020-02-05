function init_signup(){
    initSignupPagination();
    $('.overflowtext').textOverflow();
    $("input#beginsignup").datepicker({
        format: 'MM/DD/YYYY',
        changeMonth: true,
        changeYear: true
    });
    $("input#endsignup").datepicker({
        format: 'MM/DD/YYYY',
        changeMonth: true,
        changeYear: true
    });
    $("input.datepicker").unbind('change').change(function () {
        apply_signup_filter();
    })
    $("a.button").button();
    $("a#exportgignup").unbind().click(function () {
        export_signup();
    })
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
    $("#editmail_form:ui-dialog").dialog("destroy");
}
/* Init pagination */
function initSignupPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalsign').val();
    var perpage = $("#perpagesign").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#signups div#Pagination").empty();
        $("input#cursign").val(0);
        pageSignupCallback(0);
    } else {
        var curpage = $("#cursign").val();
        // Create content inside pagination element
        $("div#signups div#Pagination").pagination(num_entries, {
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

function pageSignupCallback(page_index){
    var params = new Array();
    params.push({name: 'startdate', value: $("#beginsignup").val()});
    params.push({name: 'enddate', value: $("#endsignup").val()});
    params.push({name: 'offset', value: page_index});
    params.push({name: 'limit', value: $("#perpagesign").val()});
    params.push({name: 'order_by', value: $("#ordersign").val()});
    params.push({name: 'direction', value: $("#direcsign").val()});
    params.push({name: 'maxval', value: $('#totalsign').val()});
    params.push({name: 'type', value: 'Signups'});

    $.post('/emailsview/signupsdat',params,
        function(response){
            if (response.errors=='') {
                $('#tabinfo_left').empty().append(response.data.content_left);
                $('#tabinfo_right').empty().append(response.data.content_right);
                $("#curpage").val(page_index);
                var heigh=$("#tabinfo_left").css('height');
                heigh=heigh.replace('px','');
                /* $(".last_column_left").css('width','237'); */
                if (heigh<529) {
                    $(".last_column_left").css('width','237');
                }
            } else {
                show_error(response);
            }
        },'json');
    return false;
}
/*
function change_mailstatus(obj) {
    var objid=obj.id.substr(3);

    if (confirm('Are you sure ?')) {
        var url="/emailsview/updatestatus";
        var rep = 'SGN';
        $.post(url,{'mail_id':objid,'mail_rep':rep},function(data){
            if (data.error=='') {
                $("#newsignup").empty().html(data.newsign);
                if ($("#email_types").val()=='1') {
                    $('#totalrec').val(data.newsign);
                }
                initPagination();
            } else {
                alert(data.error);
            }
        },'json');
        return true;
    } else {
        return false;
    }
}
*/

function change_type() {
    var newtype=$("#email_types").val();
    $.post('/emailsview/calcmessages', {'status':newtype,'email_type':'Signups'}, function(data){
        $('#totalrec').val(data.numrecs);
        $("#curpage").val(0);
        initPagination();
    }, 'json');
}

function apply_signup_filter() {
    var params=new Array();
    params.push({name:'startdate', value: $("#beginsignup").val()});
    params.push({name:'enddate', value: $("#endsignup").val()});
    var url='/emailsview/count_signup';
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
    var url = "/emailsview/export_signups";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            window.open(response.data.url);
        } else {
            show_error(response);
        }
    },'json');

}