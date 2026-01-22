function init_leadsview() {
    init_customform_interest();
    init_webquest_interest();
    init_webquotes_interest();
    init_proofrequest_interest();
    init_repeatreminder();
    init_interest_content();
    initLeaddataPagination();
    show_leadpriority();
    show_ordermissinfo();
    init_leadmanage_content();
}
function init_leadmanage_content() {
    // $(".leads_viewclosedflag").unbind('click').click(function () {
    //     var showclose = 0;
    //     if (parseInt($("#leadviewshowclosed").val())==0) {
    //         showclose = 1;
    //     }
    //     if (showclose == 1) {
    //         $(".leads_viewclosedflag").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
    //     } else {
    //         $(".leads_viewclosedflag").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
    //     }
    //     $("#leadviewshowclosed").val(showclose);
    //     search_leadsdata();
    // });
    $(".leadsort_updatedate").unbind('click').click(function () {
        var entity = '';
        if ($(this).hasClass('leads')) {
            entity = 'leads';
        } else if ($(this).hasClass('priority')) {
            entity = 'priority';
        } else {
            entity = 'tasks';
        }
        if (entity == 'leads') {
            if (parseInt($('#leaddatasort').val())==2) {
                // Change Icon, value, call pagination
                $(".leadsort_updatedate.leads").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
                $(".leadsort_createdate.leads").empty().html('<i class="fa fa-circle-thin" aria-hidden="true"></i>');
                $("#leaddatasort").val(1);
                pageLeaddataCallback(parseInt($("#leadviewcurpage").val()));
            }
        } else if (entity == 'priority') {
            if (parseInt($('#leadpriorsort').val())==2) {
                $(".leadsort_updatedate.priority").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
                $(".leadsort_createdate.priority").empty().html('<i class="fa fa-circle-thin" aria-hidden="true"></i>');
                $("#leadpriorsort").val(1);
                show_leadpriority();
            }
        } else {
            if (parseInt($('#leadtasksort').val())==2) {
                $(".leadsort_updatedate.tasks").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
                $(".leadsort_createdate.tasks").empty().html('<i class="fa fa-circle-thin" aria-hidden="true"></i>');
                $("#leadtasksort").val(1);
                show_leadtasks();
            }
        }
    });
    $(".leadsort_createdate").unbind('click').click(function () {
        var entity = '';
        if ($(this).hasClass('leads')) {
            entity = 'leads';
        } else if ($(this).hasClass('priority')) {
            entity = 'priority';
        } else {
            entity = 'tasks';
        }
        if (entity == 'leads') {
            if (parseInt($('#leaddatasort').val())==1) {
                // Change Icon, value, call pagination
                $(".leadsort_createdate.leads").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
                $(".leadsort_updatedate.leads").empty().html('<i class="fa fa-circle-thin" aria-hidden="true"></i>');
                $("#leaddatasort").val(2);
                pageLeaddataCallback(parseInt($("#leadviewcurpage").val()));
            }
        } else if (entity == 'priority') {
            if (parseInt($('#leadpriorsort').val())==1) {
                $(".leadsort_createdate.priority").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
                $(".leadsort_updatedate.priority").empty().html('<i class="fa fa-circle-thin" aria-hidden="true"></i>');
                $("#leadpriorsort").val(2);
                show_leadpriority();
            }
        } else {
            if (parseInt($('#leadtasksort').val())==2) {
                $(".leadsort_createdate.tasks").empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
                $(".leadsort_updatedate.tasks").empty().html('<i class="fa fa-circle-thin" aria-hidden="true"></i>');
                $("#leadtasksort").val(2);
                show_leadtasks();
            }
        }
    });
    $("select#leads_replica").unbind('change').change(function(){
        $("#leadviewuser").val($("select#leads_replica").val());
        search_leadsdata();
        show_leadpriority();
        // show_leadtasks();
        // show_newleads();
        // show_ordermissinfo();
    });
    // Search all
    $("div.leadsearchall").unbind('click').click(function(){
        $("#leadviewuser").val('');
        $("#leadviewshowclosed").val(1);
        $(".leads_viewclosedflag").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
        search_leadsdata();
        show_leadpriority();
        // show_leadtasks();
        // show_newleads();
        // show_ordermissinfo();
    });
    $("div.leadsearchusr").unbind('click').click(function(){
        var usr = $(this).data('user');
        $("#leadviewuser").val(usr);
        search_leadsdata();
        show_leadpriority();
        // show_leadtasks();
        // show_newleads();
        // show_ordermissinfo();
    })
    // Clean
    $("div.leadsearchclear").unbind('click').click(function(){
        $("input.lead_searchinput").val('');
        var usr = $(".leadsearchusr").data('user');
        $("#leadviewuser").val(usr);
        $("#leadviewshowclosed").val(0);
        $(".leads_viewclosedflag").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
        search_leadsdata();
        show_leadpriority();
        // show_leadtasks();
        // show_newleads();
        // show_ordermissinfo();
    });
    // Input search
    $("input.lead_searchinput").keypress(function(event){
        if (event.which == 13) {
            $("#leadviewuser").val('');
            $("#leadviewshowclosed").val(1);
            $(".leads_viewclosedflag").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
            search_leadsdata();
            show_leadpriority();
            // show_leadtasks();
            // show_newleads();
            // show_ordermissinfo();
        }
    });
    // Add New Lead
    $("div.leads_add").unbind('click').click(function(){
        var brand = $("#leadviewbrand").val();
        if (brand=='ALL') {
        } else {
            add_lead(brand);
        }
    });
}

function search_leadsdata() {
    var params = leadpaginationparams(0);
    var url = '/leads/search_leads';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#leadviewtotalrec").val(response.data.totalrec);
            $("#leadviewcurpage").val(0);
            initLeaddataPagination();
        } else {
            show_error(response);
        }
    },'json')
}

function init_customform_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/customform_interest';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#sbcustomformstable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='sbcustomform']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='sbcustomform']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='sbcustomform']").addClass('emptycontent');
                $("#sbcustomformstable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='sbcustomform']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='sbcustomform']").removeClass('emptycontent');
                $("#sbcustomformstable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('sbcustomformstable'), { autoHide: false })
            }
            init_interest_management();
        } else {
            show_error(response);
        }
    },'json');
}

function init_webquest_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/webquest_interest';
    $.post(url,params, function (response){
        if (response.errors=='') {
            $("#webquestiontable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='webquestions']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='webquestions']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='webquestions']").addClass('emptycontent');
                $("#webquestiontable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='webquestions']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='webquestions']").removeClass('emptycontent');
                $("#webquestiontable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('webquestiontable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_webquotes_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/webquotes_interest';
    $.post(url,params, function (response){
        if (response.errors=='') {
            $("#onlinequotetable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='onlinequotes']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='onlinequotes']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='onlinequotes']").addClass('emptycontent');
                $("#onlinequotetable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='onlinequotes']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='onlinequotes']").removeClass('emptycontent');
                $("#onlinequotetable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('onlinequotetable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_proofrequest_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/proofrequest_interest';
    $.post(url,params, function (response){
        if (response.errors=='') {
            $("#proofrequesttable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='proofrequests']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='proofrequests']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='proofrequests']").addClass('emptycontent');
                $("#proofrequesttable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='proofrequests']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='proofrequests']").removeClass('emptycontent');
                $("#proofrequesttable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('proofrequesttable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_repeatreminder() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    params.push({name: 'customorders', value: $("#leadviewremindcustom").val()});
    params.push({name: 'orderrich', value: $("#leadviewremindrichy").val()});
    params.push({name: 'date', value: $("#leadviewremindmonth").val()});
    var url = '/leads/reminder_interest';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#repeatremandtable").empty().html(response.data.content);
            if (parseInt(response.data.cntrec)==0) {
                $(".repeatremandheader").addClass('emptycontent');
                $(".repeatremand_subheader").addClass('emptycontent');
                $("#repeatremandtable").addClass('emptycontent');
            } else {
                $(".repeatremandheader").removeClass('emptycontent');
                $(".repeatremand_subheader").removeClass('emptycontent');
                $("#repeatremandtable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('repeatremandtable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_interest_content() {
    $(".repeatremand_filter_check[data-filtr='revenue']").unbind('click').click(function (){
        var check = 0;
        if ($("#leadviewremindrichy").val()==0) {
            check = 1;
        }
        $("#leadviewremindrichy").val(check);
        if (check==0) {
            $(".repeatremand_filter_check[data-filtr='revenue']").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
        } else {
            $(".repeatremand_filter_check[data-filtr='revenue']").empty().html('<i class="fa fa-check-square" aria-hidden="true"></i>');
        }
        init_repeatreminder();
    });
    $(".repeatremand_filter_check[data-filtr='custom']").unbind('click').click(function (){
        var check = 0;
        if ($("#leadviewremindcustom").val()==0) {
            check = 1;
        }
        $("#leadviewremindcustom").val(check);
        if (check==0) {
            $(".repeatremand_filter_check[data-filtr='custom']").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
        } else {
            $(".repeatremand_filter_check[data-filtr='custom']").empty().html('<i class="fa fa-check-square" aria-hidden="true"></i>');
        }
        init_repeatreminder();
    })
}

function init_interest_management() {
    $(".newunassign_tasktable").find('div.datarow').hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        });
    // Click on SB forms row
    $("#sbcustomformstable").find('div.datarow').unbind('click').click(function (){
        var task = parseInt($(this).data('task'));
        if (task > 0) {
            showcustomformdetails(task);
        }
    })
}

function initLeaddataPagination() {
    var num_entries = parseInt($('#leadviewtotalrec').val());
    var perpage = parseInt($("#leadviewperpage").val());
    if (num_entries < perpage) {
        $("#mainleadpagination").empty();
        $("#leadviewcurpage").val(0);
        pageLeaddataCallback(0);
    } else {
        var curpage = $("#curpagelead").val();
        // Create content inside pagination element
        $("#mainleadpagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeaddataCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeaddataCallback(page_index) {
    var params = leadpaginationparams(page_index);
    params.push({name: 'sorttime', value: $("#leaddatasort").val()});
    var url = '/leads/leadview_data';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadslistdata").empty().html(response.data.content);
            $("#leadviewcurpage").val(page_index);
            init_leaddata_manage();
            if (parseInt(response.data.cntrec) > 0) {
                new SimpleBar(document.getElementById('leadslistdata'), { autoHide: false })
            }
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}

function leadpaginationparams(page_index) {
    var params = new Array();
    var usrreplic='';
    var search=$("input.lead_searchinput").val();
    var usrreplic = $("#leadviewuser").val();
    var showcloded = $("#leadviewshowclosed").val();
    params.push({name: 'search', value: search});
    params.push({name: 'userrepl', value: usrreplic});
    params.push({name: 'showcloded', value: showcloded});
    params.push({name: 'limit', value: $("#leadviewperpage").val()});
    params.push({name: 'maxval', value: $("#leadviewtotalrec").val()});
    params.push({name: 'offset', value: page_index});
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    return params;
}

function show_leadpriority() {
    var params = leadpaginationparams(0);
    params.push({name: 'sorttime', value: $("#leadpriorsort").val()});
    var url = 'leads/leadview_priority';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadsprioritydata").empty().html(response.data.content);
            if (parseInt(response.data.cntrec) > 0) {
                new SimpleBar(document.getElementById('leadsprioritydata'), { autoHide: false })
            }
            init_leaddata_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}

function show_ordermissinfo() {
    var params = leadpaginationparams(0);
    params.push({name: 'sorttime', value: $("#ordermissinfosort").val()});
    var url = 'leads/orders_missinfo';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#ordermissinfodata").empty().html(response.data.content);
            if (parseInt(response.data.cntrec) > 0) {
                new SimpleBar(document.getElementById('ordermissinfodata'), { autoHide: false })
            }
            init_leaddata_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_leaddata_manage() {
    $("div.leaddataarea").find('div.datarow').hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        });
    $("div.leaddataarea").find('div.datarow').unbind('click').click(function(){
        edit_lead($(this).data('lead'));
    });
    // Open order
    $(".missordernumber_dat").unbind('click').click(function (){
        var order = $(this).data('order');
        edit_missinfo(order);
    });
}

function edit_missinfo(order) {
    var callpage = 'leadsview';
    var brand = $("#leadsveiwbrand").val();
    var url="/leadorder/leadorder_change";
    var params = new Array();
    params.push({name: 'order', value: order});
    params.push({name: 'page', value: callpage});
    params.push({name: 'edit', value: 0});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(order)==0) {
                init_onlineleadorder_edit();
                init_rushpast();
                if (parseInt($("#ordermapuse").val())==1) {
                    // Init simple Shipping address
                    initShipOrderAutocomplete();
                    if ($("#billorder_line1").length > 0) {
                        initBillOrderAutocomplete();
                    }
                }
            } else {
                if (parseInt(response.data.cancelorder)===1) {
                    $("#artModal").find('div.modal-header').addClass('cancelorder');
                } else {
                    $("#artModal").find('div.modal-header').removeClass('cancelorder');
                }
                // // Hide edit button and navigation
                // $("#editbuttonarea").css('visibility','hidden');
                // $(".block_4").css('visibility','hidden');
                navigation_init();
            }
        } else {
            show_error(response);
        }
    },'json');
}
