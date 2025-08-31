var orderid = '';

function init_reschedule_management() {
    $(".btnreschedular-btn").unbind('click').click(function (){
        var printdate = $("#calendarprintdate").val();
        $("#loader").show();
        $(".btn-reschedular-open").hide();
        $(".btn-reschedular").show();
        $(".pschedul-leftside").hide();
        $(".pschedul-rightside").hide();

        $(".maingreyblock.fullinfo").show();
        $(".history-section").show();
        init_printdate_details(printdate);
        $("#loader").hide();
    });
    $(".reschdl-tab").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
        } else {
            var sortfld = $(this).data('sortfld');
            $(".reschdl-tab").removeClass('active');
            $(".reschdl-tab[data-sortfld='"+sortfld+"']").addClass('active');
            var params = new Array();
            params.push({name: 'sortfld', value: sortfld});
            var url = '/printcalendar/reschedulechangeview';
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $(".reschedularbody").empty().html(response.data.calendarview);
                    init_reschedule_management();
                    $("#loader").hide();
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    })
}

function dragstartHandler(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    console.log('Star ID '+ev.target.id);
    orderid = ev.target.id;
}

function dragoverHandler(ev) {
    ev.preventDefault();
}

function dropHandler(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    // ev.target.appendChild(document.getElementById(data));
    // console.log('Add to Element '+ev.target.id+'!');
    var newdate = ev.target.id.replace('printday_','');
    // console.log('Target '+ev.target);
    // Send changes to Scheduler
    var params = new Array();
    params.push({name: 'print_date', value: newdate});
    params.push({name: 'order_id', value: orderid.replace('printord_','')});
    var url = '/printcalendar/ordernewdate';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("div[data-printdata='"+newdate+"']").append(document.getElementById(data))
            orderid='';
        }
    },'json');
}
