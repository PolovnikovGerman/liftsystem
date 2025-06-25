function init_leadsview() {
    initLeaddataPagination();
    show_leadpriority();
    show_leadtasks();
    show_newleads();
    show_ordermissinfo();
    init_leadmanage_content();
}

function init_leadmanage_content() {
    $(".leads_viewclosedflag").unbind('click').click(function () {
        var showclose = 0;
        if (parseInt($("#leadviewshowclosed").val())==0) {
            showclose = 1;
        }
        if (showclose == 1) {
            $(".leads_viewclosedflag").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
        } else {
            $(".leads_viewclosedflag").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
        }
        $("#leadviewshowclosed").val(showclose);
        search_leadsdata();
    });
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
        show_leadtasks();
        show_newleads();
        show_ordermissinfo();
    });
    // Search all
    $("div.leadsearchall").unbind('click').click(function(){
        $("#leadviewuser").val('');
        $("#leadviewshowclosed").val(1);
        $(".leads_viewclosedflag").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
        search_leadsdata();
        show_leadpriority();
        show_leadtasks();
        show_newleads();
        show_ordermissinfo();
    });
    $("div.leadsearchusr").unbind('click').click(function(){
        var usr = $(this).data('user');
        $("#leadviewuser").val(usr);
        search_leadsdata();
        show_leadpriority();
        show_leadtasks();
        show_newleads();
        show_ordermissinfo();
    })
    // Clean
    $("div.leadsearchclear").unbind('click').click(function(){
        $("input.lead_searchinput").val('');
        search_leadsdata();
        show_leadpriority();
        show_leadtasks();
        show_newleads();
        show_ordermissinfo();
    });
    // Input search
    $("input.lead_searchinput").keypress(function(event){
        if (event.which == 13) {
            $("#leadviewuser").val('');
            $("#leadviewshowclosed").val(1);
            $(".leads_viewclosedflag").empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
            search_leadsdata();
            show_leadpriority();
            show_leadtasks();
            show_newleads();
            show_ordermissinfo();
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
    var url = 'leads/leadview_data';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadslistdata").empty().html(response.data.content);
            $("#leadviewcurpage").val(page_index);
            init_leaddata_manage();
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
    // if (search=='') {
    //     usrreplic=$("#leads_replica").val();
    // }
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
            init_leaddata_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json')
}

function show_leadtasks() {
    var params = leadpaginationparams(0);
    params.push({name: 'sorttime', value: $("#leadtasksort").val()});
    var url = 'leads/leadview_tasks';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadstasksdata").empty().html(response.data.content);
            init_leaddata_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function show_newleads() {
    var params = leadpaginationparams(0);
    params.push({name: 'sorttime', value: $("#leadnewleadsort").val()});
    var url = 'leads/leadview_newleads';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#newleadslistdata").empty().html(response.data.content);
            init_leaddata_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function show_ordermissinfo() {
    var params = leadpaginationparams(0);
    params.push({name: 'sorttime', value: $("#ordermissinfosort").val()});
    var url = 'leads/orders_missinfo';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#ordermissinfodata").empty().html(response.data.content);
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
}