function init_ordersreports() {
    init_reportdata();
    // Change Brand
    $("#checkoutreporttopmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#checkoutreportbrand").val(brand);
        $("#checkoutreporttopmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#checkoutreporttopmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#checkoutreporttopmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_checkoutreport();
    });

    $(".blok-reports").unbind('click').click(function () {
        var charttype = $(this).data('charttype');
        build_chart(charttype);
    });
}

function init_reportdata() {
    var url='/analytics/checkout_reports_data';
    var params=new Array();
    params.push({name: 'brand', value: $("#checkoutreportbrand").val()});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#reportsinfo").empty().html(response.data.content);
            $("#loader").hide();
        } else {
            show_error(response);
        }
    }, 'json');
}

function build_chart(charttype) {
    var url="/analytics/chekout_report_chart";
    $("#loader").show();
    $.post(url, {'charttype': charttype}, function (response) {
        if (response.errors=='') {
            $("#loader").hide();
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','925px');
            $("#pageModal").modal('show');
            var datarows = response.data.chartdata;
            var data = google.visualization.arrayToDataTable(datarows);

            var options = {
                title: '',
                curveType: 'none',
                legend: {position: 'right', textStyle: {color: 'black', fontSize: 11}},
                axes: {
                    y: {label: ''}
                },
                // chartArea: {
                //     top: 40,
                //     backgroundColor: {stroke: "#3eac48", strokeWidth: 2}
                // },
                width: 900,
                height: 500,
                series: {
                    0: { color: '#0000ff'}
                },
                lineWidth: 4
            };
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
            chart.draw(data, options);
        } else {
            show_error(response);
        }
    },'json');
}

function showdetails(type) {
    /* Get Email Details */
    var url='/ordersview/graphdata';
    $.post(url, {'type':type}, function(data){
        if (data.error!='') {
            alert(data.error);
        } else {
            /* Fill data into Dialog */
            $("#chart1").empty();
            if (type=='day') {
                $(".report_graph_title").html('Graph by Days');
            } else {
                $(".report_graph_title").html('Graph by Weeks');
            }

            var d= [];
            var obj=data.dat;
            var objv=data.val;
            for(i = 0; i < obj.length; ++i)
            {
                d.push([obj[i],objv[i]]);
            }


            var options = {
                xaxis: { mode: "time", timeformat: "%b %y",ticks:12, minTickSize: [1, "month"], show:true, label:'Order Date' },
                yaxis: { position:"left", show:true, labelWidth:25, label: 'Orders Totals' },
                selection: { mode: "x" },
                series: {
                    lines: { show: true, lineWidth: 1, fill: true, fillColor: "rgba(0, 0, 128, 0.8)" },
                    points: { show: true },
                    shadowSize: 0
                },
                grid: {
                    backgroundColor: "#FFFFFF",
                    show : true,
                    color:"#000000",
                    hoverable: true
                }
            };

            var plot = $.plot($("#chart1"), [d], options)

            var previousPoint = null;

            $("#chart1").bind("plothover", function (event, pos, item) {
                $("#x").text(pos.x.toFixed(0));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY,
                            "Orders totals  $" + y);
                    }
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
                    'background-color': '#f8da4e',
                    'z-index' : '99999'
                }).appendTo("body").fadeIn(200);
            }
            /* */
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
        }
    }, 'json')

}

function init_checkoutreport() {
    var params=new Array();
    params.push({name: 'brand', value: $("#checkoutreportbrand").val()});
    var url='/analytics/checkout_reports_totals';
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#checkout_footerarea").empty().html(response.data.content);
            init_reportdata();
        } else {
            show_error(response);
        }
    },'json');

}


// function close_details() {
//     $("#question_details_dialog").fadeOut(500);
//     $("#tooltip").remove();
//     $('#mask').hide();
// }
//
// function attempt_report() {
//     var url="/ordersview/attempts_report";
//     $.post(url,{},function(response){
//         if (response.filename!='') {
//             var winname="report";
//
//             var windowWidth = document.documentElement.clientWidth;
//             var windowHeight = document.documentElement.clientHeight;
//
//             var popupHeight = 572;
//             var popupWidth = 695;
//
//             var top = windowHeight/2-popupHeight/2;
//             var left= windowWidth/2-popupWidth/2;
//
//             window.open(response.filename,winname,'width='+popupWidth+',height='+popupHeight+',top='+top+', left='+left+',resizable=no,toolbar=no,menubar=no,status=no,fullscreen=no,directories=no');
//
//         }
//     },'json')
// }