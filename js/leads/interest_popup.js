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
    });
    // Unlink Relation
    $(".unlinkassignlead").unbind('click').click(function(){
        var title = $(this).data('title');
        var lead = $(this).data('lead');
        var type = 'customform';
        revertassignlead(title, lead, type);
    });
    // Change Shape Type
    $("#shape_type").unbind('change').change(function (){
        var params=new Array();
        params.push({name: 'custom_quote_id', value: $(this).data('form')});
        params.push({name: 'fld', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url = '/leads/customform_update';
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("button.closemodal").unbind('click').click(function (){
        $("#InterestModal").modal('hide');
        initCustomFormPagination();
    });
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
    $(".ip-btncreatelead").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var brand = $("#interestsb_brand").val();
            var params = new Array();
            params.push({name: 'type', value: 'Quote'});
            params.push({name: 'mail_id', value: question});
            params.push({name: 'leademail_id', value: $("#leademail_id").val()});
            params.push({name: 'brand', value: brand});
            var url="/leads/create_leadmessage";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#InterestModal").modal('hide');
                    $(".newwebquestioninfo").empty().html(response.data.totalnew);
                    show_new_lead(response.data.leadid,'question', brand);
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    });
    // Unlink Relation
    $(".unlinkassignlead").unbind('click').click(function(){
        var title = $(this).data('title');
        var lead = $(this).data('lead');
        var type = 'question';
        revertassignlead(title, lead, type);
    })
}

function showquotedetails(quote) {
    var url="/leads/quote_details";
    $.post(url,{'quote_id':quote},function(response){
        if (response.errors=='') {
            $("#InterestModal").find('h5.modal-title').empty().html('View Online Quote');
            $("#InterestModal").find('div.modal-body').empty().html(response.data.content);
            $("#InterestModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#InterestModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("select#lead_id").select2({
                dropdownParent: $('#InterestModal'),
                matcher: matchStart
            });
            $("#loader").hide();
            init_webquotes_modal(quote);
        } else {
            show_error(response);
        }
    },'json');
}

function init_webquotes_modal(quote) {
    $(".intpopupfooter-check").unbind('click').click(function(){
        var checkval = $("#webquotes_leadcheck").val();
        if (parseInt(checkval)==0) {
            $("#webquotes_leadcheck").val(1);
            $(".intpopupfooter-check").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
            // $(".interest_lead_assign").addClass('active');
            $("select#lead_id").prop('disabled', false);
            $(".interest_lead_assign").addClass('active');
            $(".ip-btncreatelead").removeClass('active');
            $("select#lead_id").focus();
        } else {
            $("#webquotes_leadcheck").val(0);
            $(".intpopupfooter-check").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
            // $(".customform_leadcheck_label").removeClass('active');
            $("select#lead_id").prop('disabled', true);
            $("select#lead_id").val('');
            $(".interest_lead_assign").removeClass('active');
            $(".ip-btncreatelead").addClass('active');
        }
    });
    // Quote link
    $(".btn-filepdf").unbind('click').click(function (){
        var winname='showquotadoc';
        var url=$(this).data('link');
        var params = "left=200,top=200,width=820,height=480, menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes"
        window.open(url, winname, params);
    });
    // Assign
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
                params.push({name: 'mail_id', value: quote});
                params.push({name: 'brand', value: $("#interestsb_brand").val()});
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $("#InterestModal").modal('hide');
                        $(".newwebquotesinfo").empty().html(response.data.totalnew);
                        initQuotesPagination();
                        init_webquotes_interest();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            }
        }
    });
    $(".ip-btncreatelead").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var brand = $("#interestsb_brand").val();
            var params = new Array();
            params.push({name: 'type', value: 'Quote'});
            params.push({name: 'mail_id', value: quote});
            params.push({name: 'leademail_id', value: $("#leademail_id").val()});
            params.push({name: 'brand', value: brand});
            var url="/leads/create_leadmessage";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#InterestModal").modal('hide');
                    $(".newwebquotesinfo").empty().html(response.data.totalnew);
                    show_new_lead(response.data.leadid,'quote', brand);
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    });
    // Unlink Relation
    $(".unlinkassignlead").unbind('click').click(function(){
        var title = $(this).data('title');
        var lead = $(this).data('lead');
        var type = 'quote';
        revertassignlead(title, lead, type);
    })
}

function revertassignlead(title, lead, type) {
    if (confirm('Revert assign to lead '+title+'?')==true) {
        var params = new Array();
        params.push({name: 'leadmail', value: lead});
        params.push({name: 'entity', value: type});
        var url = '/leads/revertassignlead';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#InterestModal").find('div.modal-footer').empty().html(response.data.content);
                $("select#lead_id").select2({
                    dropdownParent: $('#InterestModal'),
                    matcher: matchStart
                });
                if (type=='customform') {
                    init_customform_modal(response.data.entityid);
                    initCustomFormPagination();
                } else if (type=='quote') {
                    init_webquotes_modal(response.data.entityid);
                    initQuotesPagination();
                } else if (type=='question') {
                    init_webquestion_modal(response.data.entityid);
                    initQuestionPagination();
                }
            } else {
                show_error(response);
            }
        },'json');
    }
}