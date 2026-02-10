function init_customforms() {
    initCustomFormPagination();
    $("select#customform_status").unbind('change').change(function(){
        search_customforms();
    })
    $("select#customformhideincl").unbind('change').change(function(){
        search_customforms();
    });
    /* Enter as start search */
    $("input#customformsearch").keypress(function(event){
        if (event.which == 13) {
            search_customforms();
        }
    });
    /* Search actions */
    $("a#clear_customform").unbind('click').click(function(){
        $("select#customform_status").val(1);
        $("input#customformsearch").val('');
        search_customforms();
    })
    $("a#find_customform").unbind('click').click(function(){
        search_customforms();
    });
    // Change Brand
    $("#customformviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#customformviewbrand").val(brand);
        $("#customformviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#customformviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#customformviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_customforms();
    });
    // Change Totals view
    $(".customform_total_switcher").unbind('click').click(function(){
        var newview = 'table';
        if ($("#customformviewtype").val()=='table') {
            newview = 'chart';
        }
        $("#customformviewtype").val(newview);
        if (newview=='table') {
            $("#customformtotal_chartview").hide();
            $("#customformtotal_tableview").show();
            $(".customform_total_switcher").empty().html('Chart');
        } else {
            $("#customformtotal_tableview").hide();
            $("#customformtotal_chartview").empty().show().html('<canvas id="myChart"></canvas>');
            $(".customform_total_switcher").empty().html('Table');
        }
        initCustomFormTotals(newview);
    });
    // Init totals
    initCustomFormTotals('table');
    $(".customform_monthtotal_switcher").unbind('click').click(function (){
        var newview = 'table';
        if ($("#customformmonthtype").val()=='table') {
            newview = 'chart';
        }
        $("#customformmonthtype").val(newview);
        if (newview=='table') {
            $("#customformmonthtotal_chartview").hide();
            $("#customformmonthtotal_tableview").show();
            $(".customform_monthtotal_switcher").empty().html('Chart');
        } else {
            $("#customformmonthtotal_tableview").hide();
            $("#customformmonthtotal_chartview").empty().show().html('<canvas id="myMonthChart"></canvas>');
            $(".customform_monthtotal_switcher").empty().html('Table');
        }
        initCustomFormMonthtotal(newview);
    });
    initCustomFormMonthtotal('table');
}

function search_customforms() {
    var params = getCustomformParams();
    var url = '/leads/customformsearch';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#totalcustomform").val(response.data.totals);
            $("#curpagecustomform").val(0);
            initCustomFormPagination();
        }
    },'json')
}

function initCustomFormPagination() {
    // count entries inside the hidden content
    var num_entries = parseInt($('#totalcustomform').val());
    var assign = parseInt($("#customform_status").val());
    var perpage = parseInt($("#perpagecustomform").val());
    if (assign==1) {
        perpage = num_entries+1;
    }
    if (num_entries < perpage) {
        $("div#customformpagination").empty();
        $("#curpagecustomform").val(0);
        pageCustomFormsCallback(0);
    } else {
        var curpage = $("#curpagecustomform").val();
        // Create content inside pagination element
        $("div#customformpagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageCustomFormsCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function getCustomformParams() {
    var search=$("input#customformsearch").val();
    var params = new Array();
    params.push({name:'search',value: search});
    params.push({name:'assign',value:$("#customform_status").val()});
    params.push({name:'brand',value:$("#customformviewbrand").val()});
    params.push({name:'hideincl',value:$("#customformhideincl").val()});
    return params;
}

function pageCustomFormsCallback(pageidx) {
    var assign = parseInt($("#customform_status").val());
    var perpage = parseInt($("#perpagecustomform").val());
    var maxval = parseInt($('#totalcustomform').val());
    if (assign==1) {
        perpage = maxval+1;
    }
    var params=getCustomformParams();
    params.push({name:'offset', value: pageidx});
    params.push({name:'limit', value:perpage});
    params.push({name:'maxval',value:maxval});
    params.push({name:'order_by',value:$("#sortcustomform").val()});
    params.push({name:'direction',value:$("#sortdircustomform").val()});
    var url='/leads/customformsdat';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#customform_tabledat").empty().html(response.data.content);

            $("#curpagecustomform").val(pageidx);
            /* change size */
            // if (parseInt(response.data.totals) > 21) {
            //     $(".customform_tabledat").scrollpanel({
            //         'prefix' : 'sp-'
            //     });
            // }
            // jQuery.balloon.init();
            init_customform_content();
            leftmenu_alignment();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
    return false;
}

function init_customform_content() {
    $("div.content-row").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        }
    );
    $("div.showformdetails").click(function(){
        var formid = $(this).parent('div.content-row').data('form');
        showcustomformdetails(formid);
    });
    $("div.websys").find('i').unbind('click').click(function () {
        var action = 1;
        if ($(this).parent('div.websys').hasClass('active')==true) {
            action = 0;
        }
        var formid = $(this).parent('div.websys').data('form');
        var params = new Array();
        params.push({name: 'activity', value: action});
        params.push({name: 'form_id', value: formid});
        var url = '/leads/customformdmanage';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                search_customforms();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".assignform").unbind('click').click(function () {
        var formid = $(this).parent('div.content-row').data('form');
        assign_custom(formid);
    });
}

// function showcustomformdetails(formid) {
//     var url="/leads/customformdetail";
//     $("#loader").show();
//     $.post(url, {'form_id': formid}, function(response){
//         if (response.errors=='') {
//             $("#pageModalLabel").empty().html('View Lead from Custom Stress Ball Form');
//             $("#pageModal").find('div.modal-dialog').css('width','725px');
//             $("#pageModal").find('div.modal-body').empty().html(response.data.content);
//             $("#pageModal").find('div.modal-footer').empty().html(response.data.footer);
//             $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
//             $("select#lead_id").select2({
//                 dropdownParent: $('#pageModal'),
//                 matcher: matchStart
//             });
//             $("#loader").hide();
//             init_customform_modal(formid);
//         } else {
//             $("#loader").hide();
//             show_error(response);
//         }
//     }, 'json');
// }

// function init_customform_modal(formid) {
//     $(".name-file").unbind('click').click(function(){
//         var url = $(this).data('imgsrc');
//         // Open new window
//         window.open(url, 'customformwin', 'width=600, height=800,toolbar=1')
//     });
//     // Prepare assign
//     $(".customform_leadcheck").unbind('click').click(function(){
//         var checkval = $("#customform_leadcheck").val();
//         if (parseInt(checkval)==0) {
//             $("#customform_leadcheck").val(1);
//             $(".customform_leadcheck").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
//             $(".customform_leadcheck_label").addClass('active');
//             $("select#lead_id").prop('disabled', false);
//             $(".customform_lead_assign").addClass('active');
//             $(".sbcustomform_newlead").removeClass('active');
//             $("select#lead_id").focus();
//         } else {
//             $("#customform_leadcheck").val(0);
//             $(".customform_leadcheck").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
//             $(".customform_leadcheck_label").removeClass('active');
//             $("select#lead_id").prop('disabled', true);
//             $("select#lead_id").val('');
//             $(".customform_lead_assign").removeClass('active');
//             $(".sbcustomform_newlead").addClass('active');
//         }
//     });
//     // Assign
//     $(".customform_lead_assign").unbind('click').click(function(){
//         if ($(this).hasClass('active')) {
//             var newlead = $("select#lead_id").val();
//             if (newlead=='') {
//                 alert('Choose Lead # before assign');
//             } else {
//                 var url="/leads/savecustomformstatus";
//                 var params = new Array();
//                 params.push({name: 'customform', value: formid});
//                 params.push({name: 'lead_id', value: $("#lead_id").val()});
//                 params.push({name: 'leademail_id', value: $("#leademail_id").val()});
//                 params.push({name: 'brand', value: $("#customformviewbrand").val()});
//                 // var dat=$("form#msgstatus").serializeArray();
//                 $.post(url, params, function(response){
//                     if (response.errors=='') {
//                         $("#pageModal").modal('hide');
//                         $(".newcustomformsinfo").empty().html(response.data.totalnew);
//                         initCustomFormPagination();
//                     } else {
//                         show_error(response);
//                     }
//                 }, 'json');
//             }
//         }
//     });
//     // New Lead
//     $(".sbcustomform_newlead").unbind('click').click(function(){
//         if ($(this).hasClass('active')) {
//             var brand = $("#customformviewbrand").val();
//             var params = new Array();
//             params.push({name: 'type', value: 'CustomQuote'});
//             params.push({name: 'customquote', value: formid});
//             params.push({name: 'brand', value: brand});
//             var url="/leads/create_leadmessage";
//             $.post(url, params, function(response){
//                 if (response.errors=='') {
//                     $("#pageModal").modal('hide');
//                     $(".newcustomformsinfo").empty().html(response.data.totalnew);
//                     show_new_lead(response.data.leadid,'customquote', brand);
//                 } else {
//                     show_error(response);
//                 }
//             }, 'json');
//         }
//     })
// }
function init_customform_modal(formid) {
    $(".name-file").unbind('click').click(function(){
        var url = $(this).data('imgsrc');
        // Open new window
        window.open(url, 'customformwin', 'width=600, height=800,toolbar=1')
    });
    // Prepare assign
    $(".customform_leadcheck").unbind('click').click(function(){
        var checkval = $("#customform_leadcheck").val();
        if (parseInt(checkval)==0) {
            $("#customform_leadcheck").val(1);
            $(".customform_leadcheck").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
            $(".customform_leadcheck_label").addClass('active');
            $("select#lead_id").prop('disabled', false);
            $(".customform_lead_assign").addClass('active');
            $(".sbcustomform_newlead").removeClass('active');
            $("select#lead_id").focus();
        } else {
            $("#customform_leadcheck").val(0);
            $(".customform_leadcheck").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
            $(".customform_leadcheck_label").removeClass('active');
            $("select#lead_id").prop('disabled', true);
            $("select#lead_id").val('');
            $(".customform_lead_assign").removeClass('active');
            $(".sbcustomform_newlead").addClass('active');
        }
    });
    // Assign
    $(".customform_lead_assign").unbind('click').click(function(){
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
                params.push({name: 'brand', value: $("#customformviewbrand").val()});
                // var dat=$("form#msgstatus").serializeArray();
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $("#pageModal").modal('hide');
                        $(".newcustomformsinfo").empty().html(response.data.totalnew);
                        initCustomFormPagination();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            }
        }
    });
    // New Lead
    $(".sbcustomform_newlead").unbind('click').click(function(){
        if ($(this).hasClass('active')) {
            var brand = $("#customformviewbrand").val();
            var params = new Array();
            params.push({name: 'type', value: 'CustomQuote'});
            params.push({name: 'customquote', value: formid});
            params.push({name: 'brand', value: brand});
            var url="/leads/create_leadmessage";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#pageModal").modal('hide');
                    $(".newcustomformsinfo").empty().html(response.data.totalnew);
                    show_new_lead(response.data.leadid,'customquote', brand);
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    });
    $("#shape_type").unbind('change').change(function(){
        var params = new Array();
        params.push({'name' : 'custom_quote_id', value: $(this).data('form')});
        params.push({'name' : 'fld', value: $(this).data('fld')});
        params.push({'name' : 'newval', value: $(this).val()});
        var url = "/leads/customform_update";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("button.close").unbind('click').click(function(){
        $("#pageModal").modal('hide');
        initCustomFormPagination();
    })
}

function assign_custom(formid) {
    var url="/leads/change_status";
    var params = new Array();
    params.push({name: 'type', value: 'CustomQuote'});
    params.push({name: 'customform', value: formid});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModal").find('div.modal-dialog').css('width','565px');
            $("#artModalLabel").empty().html('Lead Assign');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* Change Lead data */
            $("select#lead_id").select2({
                dropdownParent: $('#artModal'),
                matcher: matchStart
            });
            init_assignform_modal(formid);
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_assignform_modal(formid) {
    $("select#lead_id").change(function(){
        var lead_id=$("#lead_id").val();
        if (lead_id!='') {
            var url="/leads/change_leadrelation";
            $.post(url, {'lead_id':lead_id}, function(response){
                if (response.errors=='') {
                    $("div.modal-body div.leaddate").empty().html(response.data.lead_date);
                    $("div.modal-body div.leadcustomer").empty().html(response.data.lead_customer);
                    $("div.modal-body div.leadcustommail").empty().html(response.data.lead_mail);
                    $("div.modal-body div.savequest").addClass('active');
                    init_assignform_modal(formid);
                } else {
                    show_error(response);
                }
            }, 'json')
        }  else {
            $("div.modal-body div.leaddate").empty();
            $("div.modal-body div.leadcustomer").empty();
            $("div.modal-body div.leadcustommail").empty();
            $("div.modal-body div.savequest").removeClass('active');
            init_assignform_modal(formid);
        }
    })
    $(".savequest.active").unbind('click').click(function(){
        var url="/leads/savecustomformstatus";
        var params = new Array();
        params.push({name: 'customform', value: formid});
        params.push({name: 'lead_id', value: $("#lead_id").val()});
        params.push({name: 'leademail_id', value: $("#leademail_id").val()});
        // var dat=$("form#msgstatus").serializeArray();
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artModal").modal('hide');
                initCustomFormPagination();
            } else {
                show_error(response);
            }
        }, 'json');

    })
    $("div.updatequest_status").find("div.leads_addnew").unbind('click').click(function(){
        var params = new Array();
        params.push({name: 'type', value: 'CustomQuote'});
        params.push({name: 'customquote', value: formid});
        var brand = $("#customformviewbrand").val();
        var url="/leads/create_leadmessage";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artModal").modal('hide');
                show_new_lead(response.data.leadid,'customquote', brand);
            } else {
                show_error(response);
            }
        }, 'json');
    })
}

function initCustomFormTotals(viewtype) {
    var params = new Array();
    params.push({name: 'brand',value:$("#customformviewbrand").val()});
    params.push({name: 'viewtype', value: viewtype});
    var url= '/leads/customformstotals';
    $.post(url, params, function (response){
        if (response.errors=='') {
            if (viewtype=='chart') {
                const ctx = document.getElementById('myChart');
                const myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            label: '# of Custom Forms',
                            data: response.data.data,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                $(".customform_total_tabledat").empty().html(response.data.content);
                new SimpleBar(document.getElementById('customform_total_tabledat'), { autoHide: false });
            }
        } else {
            show_error(response);
        }
    },'json');
}

function initCustomFormMonthtotal(viewtype) {
    var params = new Array();
    params.push({name: 'brand',value:$("#customformviewbrand").val()});
    params.push({name: 'viewtype', value: viewtype});
    var url= '/leads/customformsmonths';
    $.post(url, params, function (response){
        if (response.errors=='') {
            if (viewtype=='table') {
                $(".customform_monthtotal_tabledat").empty().html(response.data.content);
                new SimpleBar(document.getElementById('customform_monthtotal_tabledat'), { autoHide: false });
            } else {
                const ctx = document.getElementById('myMonthChart');
                const myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            label: '# of Custom Forms',
                            data: response.data.data,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        } else {
            show_error(response);
        }
    },'json');
}