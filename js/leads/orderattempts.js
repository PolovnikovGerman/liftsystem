function init_attempts() {
    init_attemptdata();
    $("a#attemptreportcall").click(function(){
        attempt_report();
    });
    // Change Brand
    $("#attemptsviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#attemptsviewbrand").val(brand);
        $("#attemptsviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#attemptsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#attemptsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_attemptdata();
    });
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
                $("#pageModal").find('div.modal-dialog').css('width','1267px');
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("div.artsubmitlog").qtip({
                    content: {
                        text: function(event, api) {
                            $.ajax({
                                url: api.elements.target.data('content') // Use href attribute as URL
                            }).then(function(content) {
                                // Set the tooltip content upon successful retrieval
                                api.set('content.text', content);
                            }, function(xhr, status, error) {
                                // Upon failure... set the tooltip content to error
                                api.set('content.text', status + ': ' + error);
                            });
                            return 'Loading...'; // Set some initial text
                        }
                    },
                    position: {
                        my: 'center right',  // Position my top left...
                        at: 'center left', // at the bottom right of...
                    },
                    show: {
                        event: 'click',
                    },
                    hide: {
                        event: 'click'
                    },
                    style: {
                        classes: 'artsubmitlog_tooltip'
                    }
                });
                // Show full data of truncated values
                $("div.truncateoverflowtext").qtip({
                    content: {
                        attr: 'data-content'
                    },
                    position: {
                        my: 'bottom right',
                        at: 'top left',
                    },
                    style: 'artsubmitlogdata_tooltip'
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