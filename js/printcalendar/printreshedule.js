var orderid = '';

function init_reschedule_management() {
    // $(".btnreschedular-btn")
    $(".btn-reschedular-open").unbind('click').click(function (){
        close_reschedule();
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
                    if ($("#reschdltabl-body").length > 0) {
                        new SimpleBar(document.getElementById('reschdltabl-body'), { autoHide: false });
                    }
                    if ($("#reschditms-body").length > 0) {
                        new SimpleBar(document.getElementById('reschditms-body'), { autoHide: false });
                    }
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
    var newdate = '';
    var incomeblock = '';
    if (ev.target.id.substring(0,9)=='printday_') {
        newdate = ev.target.id.replace('printday_','');
        incomeblock = 'right';
    } else {
        newdate = ev.target.id.replace('printdate_','')
        incomeblock = 'left';
    }
    var moveorder = '';
    var outcomeblock = '';
    if (orderid.substring(0,10)=='shedulord_') {
        outcomeblock = 'right';
        moveorder = orderid.replace('shedulord_','');
    } else {
        outcomeblock = 'left';
        moveorder = orderid.replace('printord_','');
    }
    // Send changes to Scheduler
    var params = new Array();
    params.push({name: 'print_date', value: newdate});
    params.push({name: 'order_id', value: moveorder});
    params.push({name: 'incomeblock', value: incomeblock});
    params.push({name: 'outcomeblock', value: outcomeblock});
    var url = '/printcalendar/ordernewdate';
    $.post(url, params, function (response){
         if (response.errors=='') {
             if (incomeblock==outcomeblock) {
                 $("div[data-printdata='"+newdate+"']").append(document.getElementById(data))
             } else {
                 if (incomeblock=='right') {
                     if (parseInt(response.data.late)==1) {
                         $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.income);
                     } else {
                         $(".dayschedulearea[data-printdata='"+response.data.incomedate+"']").empty().html(response.data.income);
                     }
                     $("#printshortunassignarea").empty().html(response.data.unassign);
                     $("#printshortassignarea").empty().html(response.data.assign);
                     $(".pscalendar-daybox[data-printdate='"+response.data.outdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.orders);
                     $(".pscalendar-daybox[data-printdate='"+response.data.outdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.prints);
                 } else {
                     if ($("#printshortunassignarea").length==0) {
                         $(".regular-section").html(response.data.todaytemplate);
                     }
                     $("#printshortunassignarea").empty().html(response.data.income);
                     if (parseInt(response.data.late)==1) {
                         $(".dayschedulearea[data-printdata='lateorders']").empty().html(response.data.outcome);
                     } else {
                         $(".dayschedulearea[data-printdata='"+response.data.outdate+"']").empty().html(response.data.outcome);
                     }
                     // Update Calendar
                     $(".pscalendar-daybox[data-printdate='"+newdate+"']").find('div.dayboxorders-numbers').empty().html(response.data.orders);
                     $(".pscalendar-daybox[data-printdate='"+newdate+"']").find('div.dayboxprints-numbers').empty().html(response.data.prints);
                     orderid='';
                 }
             }
         } else {
             // Show error
         }
    },'json');
}