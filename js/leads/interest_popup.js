// Popup Customforms
function showcustomformdetails(formid) {
    var url="/leads/customformdetail";
    $("#loader").show();
    $.post(url, {'form_id': formid}, function(response){
        if (response.errors=='') {
            $("#InterestModal").find('h5.modal-title').empty().html('Custom Stress Ball Form Request');
            $("#InterestModal").find('div.modal-body').empty().html(response.data.content);
            $("#InterestModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#InterestModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("select#lead_id").select2({
                dropdownParent: $('#InterestModal'),
                matcher: matchStart
            });
            $("#loader").hide();
            init_customform_modal(formid);
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}
// SB Custom form content
function init_customform_modal(formid) {
    $(".name-file").unbind('click').click(function(){
        var url = $(this).data('imgsrc');
        // Open new window
        window.open(url, 'customformwin', 'width=600, height=800,toolbar=1')
    });
    // Prepare assign
    $(".intpopupfooter-check").unbind('click').click(function(){
        var checkval = $("#customform_leadcheck").val();
        if (parseInt(checkval)==0) {
            $("#customform_leadcheck").val(1);
            $(".intpopupfooter-check").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
            // $(".interest_lead_assign").addClass('active');
            $("select#lead_id").prop('disabled', false);
            $(".interest_lead_assign").addClass('active');
            $(".ip-btncreatelead").removeClass('active');
            $("select#lead_id").focus();
        } else {
            $("#customform_leadcheck").val(0);
            $(".intpopupfooter-check").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
            // $(".customform_leadcheck_label").removeClass('active');
            $("select#lead_id").prop('disabled', true);
            $("select#lead_id").val('');
            $(".interest_lead_assign").removeClass('active');
            $(".ip-btncreatelead").addClass('active');
        }
    });
    // Assign
    $(".interest_lead_assign").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var newlead = $("select#lead_id").val();
            if (newlead=='') {
                alert('Choose Lead # before assign');
            } else {
                var url="/leads/savecustomformstatus";
                var params = new Array();
                params.push({name: 'customform', value: formid});
                params.push({name: 'lead_id', value: $("#lead_id").val()});
                params.push({name: 'leademail_id', value: $("#leademail_id").val()});
                params.push({name: 'brand', value: $("#interestsb_brand").val()});
                // var dat=$("form#msgstatus").serializeArray();
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $("#InterestModal").modal('hide');
                        $(".newcustomformsinfo").empty().html(response.data.totalnew);
                        initCustomFormPagination();
                        init_customform_interest();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            }
        }
    });
    // New Lead
    $(".ip-btncreatelead").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var brand = $("#interestsb_brand").val();
            var params = new Array();
            params.push({name: 'type', value: 'CustomQuote'});
            params.push({name: 'customquote', value: formid});
            params.push({name: 'brand', value: brand});
            var url="/leads/create_leadmessage";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#InterestModal").modal('hide');
                    $(".newcustomformsinfo").empty().html(response.data.totalnew);
                    show_new_lead(response.data.leadid,'customquote', brand);
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    })
}
// WEB Question content
function showquestdetails(question) {
    var url="/leads/question_detail";
    $.post(url, {'quest_id':question}, function(response){
        if (response.errors=='') {
            $("#InterestModal").find('h5.modal-title').empty().html('View Question');
            $("#InterestModal").find('div.modal-body').empty().html(response.data.content);
            $("#InterestModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#InterestModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("select#lead_id").select2({
                dropdownParent: $('#InterestModal'),
                matcher: matchStart
            });
            $("#loader").hide();
            init_webquestion_modal(question);
        } else {
            show_error(response)
        }
    }, 'json');
}

function init_webquestion_modal(question) {
    $(".intpopupfooter-check").unbind('click').click(function(){
        var checkval = $("#webquestions_leadcheck").val();
        if (parseInt(checkval)==0) {
            $("#webquestions_leadcheck").val(1);
            $(".intpopupfooter-check").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
            // $(".interest_lead_assign").addClass('active');
            $("select#lead_id").prop('disabled', false);
            $(".interest_lead_assign").addClass('active');
            $(".ip-btncreatelead").removeClass('active');
            $("select#lead_id").focus();
        } else {
            $("#webquestions_leadcheck").val(0);
            $(".intpopupfooter-check").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
            // $(".customform_leadcheck_label").removeClass('active');
            $("select#lead_id").prop('disabled', true);
            $("select#lead_id").val('');
            $(".interest_lead_assign").removeClass('active');
            $(".ip-btncreatelead").addClass('active');
        }
    });
    $(".interest_lead_assign").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var newlead = $("select#lead_id").val();
            if (newlead=='') {
                alert('Choose Lead # before assign');
            } else {
                var url="/leads/savequeststatus";
                var params = new Array();
                params.push({name: 'lead_id', value: $("#lead_id").val()});
                params.push({name: 'leademail_id', value: $("#leademail_id").val()});
                params.push({name: 'mail_id', value: question});
                params.push({name: 'brand', value: $("#interestsb_brand").val()});
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $("#InterestModal").modal('hide');
                        $(".newwebquestioninfo").empty().html(response.data.totalnew);
                        initQuestionPagination();
                        init_webquest_interest();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            }
        }
    });
}