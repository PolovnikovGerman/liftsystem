function init_attempts() {
    init_attemptdata();
    $("a#attemptreportcall").click(function(){
        attempt_report();
    })
}

function init_attemptdata() {
    var url='/leads/attempts_data';
    var params = new Array();
    params.push({name: 'brand', value: $("input#attemptsviewbrand").val()})
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("#attemptsreportsinfo").empty().html(response.data.content);
            init_attempts_details()
        } else {
            $("#dbitemloader").hide();
            show_error(response);
        }
    }, 'json');
}

function init_attempts_details() {
    $("div.attempt_dayresult").click(function(){
        var params = new Array();
        params.push({name: 'day', value: $(this).data('attemptday')});
        params.push({name: 'brand', value: $("input#attemptsviewbrand").val()});
        var url="/leads/attempts_details";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#pageModalLabel").empty().html('Online Checkouts');
                $("#pageModal").find('div.modal-content').css('width','1290px');
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal('show');
                $("div.artsubmitlog").each(function(){
                    $("div#"+$(this).prop('id')).bt({
                        trigger: 'click',
                        ajaxCache: false,
                        width: '463px',
                        ajaxPath: ["$(this).attr('href')"]
                    });
                });
            } else {
                show_error(response);
            }
        }, 'json');
    });
}

function attempt_report() {
    var url="/orders/attempt_repparams";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            show_popup('attempt_repparams');
            $("div#pop_content").empty().html(response.data.content);
            $("div#pop_content input.attparams").datepicker();
            $("div#pop_content .attempt_start").click(function(){
                attempt_report_build();
            })
        } else {
            show_error(response);
        }
    }, 'json')
}

function attempt_report_build() {
    var datbgn=$("div#pop_content input#attdatstart").val();
    var datend=$("div#pop_content input#attdatend").val();
    var url="/orders/runattemptreport";
    $("#dbitemloader").show();
    $.post(url, {'datbgn':datbgn, 'datend':datend}, function(response){
        if (response.errors=='') {
            $("#dbitemloader").hide();
            disablePopup();
            window.open(response.data.repfile, 'AttemptReport');
        } else {
            $("#dbitemloader").hide();
            show_error(response);
        }
    }, 'json');
}