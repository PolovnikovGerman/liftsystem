function init_searchtime_content() {
    show_thisweek();
    $("#week").unbind('click').click(function(){
        show_thisweek();
    });
    $("#month").unbind('click').click(function(){
        show_thismonth();
    })
    $("#custom").unbind('click').click(function(){
        custom_range();
    })
    $("#showcustomrange").unbind('click').click(function(){
        show_custom();
    })
    // calendar_init();
    $("#d_bgn").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    $("#d_end").datepicker({
        autoclose: true,
        todayHighlight: true
    });
}


function show_custom() {
    var d_bgn = $("#d_bgn").val();
    var d_end = $("#d_end").val();
    if (d_end == '' && d_bgn == '') {
        alert('Please enter custom date range');
    } else {
        var params = new Array();
        params.push({name: 'period', value: 'custom'});
        params.push({name: 'd_bgn', value: d_bgn});
        params.push({name: 'd_end', value: d_end});
        params.push({name: 'brand', value: $("#searchtimebrand").val()});
        var url='/marketing/searchtimedata';
        $("#loader").show();
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#timesearchresultcontent").empty().html(response.data.content);
                // var heigh = $("#tabcontent").css('height');
                // heigh = heigh.replace('px', '');
                // heigh = parseInt(heigh);
                // if (heigh < 550) {
                //     $("#tabcontent").css('overflow-y', 'hidden');
                //     $(".table-dat-row").css('width', '992');
                //     $(".last_column").css('width', '135');
                // } else {
                //     $(".table-dat-row").css('width', '970');
                //     $(".last_column").css('width', '112');
                //     $("#tabcontent").css('overflow-y', 'auto');
                // }
                $("#loader").hide();
            } else {
                show_error(response);
            }
        }, 'json');

    }

}


function show_thismonth() {
    $("#datarangeview").css('visibility','hidden');
    var url='/marketing/searchtimedata';
    var params = new Array();
    params.push({name: 'period', value: 'month'});
    params.push({name: 'brand', value: $("#searchtimebrand").val()});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#timesearchresultcontent").empty().html(response.data.content);
            // var heigh=$("#tabcontent").css('height');
            // heigh=heigh.replace('px','');
            // heigh=parseInt(heigh);
            // if (heigh<550) {
            //     $("#tabcontent").css('overflow','');
            //     $(".table-dat-row").css('width','992');
            //     $(".last_column").css('width','135');
            // } else {
            //     $("#tabcontent").css('overflow-y','auto');
            // }
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }

    }, 'json');
}

function show_thisweek() {
    // Hide Custom range
    $("#datarangeview").css('visibility','hidden');
    var url='/marketing/searchtimedata';
    var params = new Array();
    params.push({name: 'period', value: 'week'});
    params.push({name: 'brand', value: $("#searchtimebrand").val()});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#timesearchresultcontent").empty().html(response.data.content);
            // var heigh=$("#tabcontent").css('height');
            // heigh=heigh.replace('px','');
            // heigh=parseInt(heigh);
            // if (heigh<550) {
            //     $("#tabcontent").css('overflow','');
            //     $(".table-dat-row").css('width','992');
            //     $(".last_column").css('width','135');
            // } else {
            //     $("#tabcontent").css('overflow-y','auto');
            // }
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

// function show_alltime() {
//     $("#f_btn1").attr("disabled",'disabled');
//     $("#f_btn2").attr('disabled','disabled');
//     $("#d_bgn").val('');
//     $("#d_end").val('');
//     var url='/searchresults/timedata';
//     $.post(url, {'period':'alltime'}, function(data){
//         $("#tabcontent").empty().html(data.content);
//         var heigh=$("#tabcontent").css('height');
//         heigh=heigh.replace('px','');
//         heigh=parseInt(heigh);
//         if (heigh<550) {
//             $("#tabcontent").css('overflow','');
//             $(".table-dat-row").css('width','992');
//             $(".last_column").css('width','135');
//         } else {
//             $("#tabcontent").css('overflow-y','auto');
//         }
//     }, 'json');
// }

function close_details() {
    $("#report_dialog").fadeOut(500);
    $("#tooltip").remove();
    $('#mask').hide();
}


function custom_range() {
    $("#datarangeview").css('visibility','visible');
}
/*
function showgraph(obj) {
    var weekday=obj.id.substr(3);
    var title='Number of Searches on ';

    switch (weekday) {
        case '0':
            title=title+'Sundays';
            break;
        case '1':
            title=title+'Mondays';
            break;
        case '2':
            title=title+'Tuesdays';
            break;
        case '3':
            title=title+'Wednesdays';
            break;
        case '4':
            title=title+'Thursdays';
            break;
        case '5':
            title=title+'Fridays';
            break;
        case '6':
            title=title+'Saturdays';
            break;
    }

    var period='alltimes';
    var d_bgn='';
    var d_end='';
    if ($("#week").prop('checked')) {
        period='week';
    } else if($("#month").prop('checked')) {
        period='month';
    } else if($("#custom").prop('checked')) {
        period='custom';
        d_bgn=$("#d_bgn").val();
        d_end=$("#d_end").val();
        if (d_bgn!='') {
            d_bgn=d_bgn.substr(6)+'-'+d_bgn.substr(0,2)+'-'+d_bgn.substr(3,2);
        }
        if (d_end!='') {
            d_end=d_end.substr(6)+'-'+d_end.substr(0,2)+'-'+d_end.substr(3,2);
        }
    }
    // Call POST - to get a data about search results
    var url = '/searchresults/graphtime';
    $.post(url, {'period':period,'d_bgn':d_bgn,'d_end':d_end,'weekday':weekday}, function(data){
        $(".schedule").empty();
        $(".name-schedule").html(title);
        var d1=[];
        var d2=[];
        var obj=data.repdatp;
        var objv=data.reppos;
        for(i = 0; i < obj.length; ++i)
        {
            d1.push([obj[i],objv[i]]);
        }

        objv=data.repneg;
        for(i = 0; i < obj.length; ++i)
        {
            d2.push([obj[i],objv[i]]);
        }

        $.plot($(".schedule"), [
            { label : 'Results', data: d1, color: "#000000"},
            { label : 'No Results', data: d2, color: "#610089"}
        ], {
            series: {
                lines: { show: true }
            },
            xaxis: {
                mode: "time", timeformat: "%b %y",ticks:12,  show:true, label:'Search Date'
            },
            yaxis: {
                position:"left",
                show:true,
                labelWidth:25
            },
            grid: {
                backgroundColor: { colors: ["#fff", "#eee"] },
                hoverable: true
            },
            legend: {
                show : false
            }
        });


        var previousPoint = null;

        $(".schedule").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(0));
            $("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(0);
                    // Call POST for detail
                    $.post('/searchresults/graphdetails',{'date': x},function(response){
                        showTooltip(item.pageX, item.pageY,response.content);
                    },'json');

                }
            } else {
                $("#tooltip").remove();
            }

        });

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#FFF',
                'z-index' : '99999'
            }).appendTo("body").fadeIn(200);
        }
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        //Set height and width to mask to fill up the whole screen
        $('#mask').css({'width':maskWidth,'height':maskHeight});

        //transition effect
        $('#mask').fadeIn(1000);
        $('#mask').fadeTo("slow",0.8);
        //Get the window height and width
        var winH = $(".container").height();
        var winW = $(".container").width();
        var top=winH/2-$("#report_dialog").height()/2;
        var left=winW/2-$("#report_dialog").width()/2

        //Set the popup window to center
        $("#report_dialog").css('top',top);
        $("#report_dialog").css('left',left);
        //transition effect
        $("#report_dialog").fadeIn(2000);
    }, 'json');
}
*/
