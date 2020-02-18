function init_leadsview() {
    initLeaddataPagination();
    initLeadClosed();
    // Change Brand
    $("#leadsviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#leadsveiwbrand").val(brand);
        $("#leadsviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#leadsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#leadsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        $("#cursign").val(0);
        initLeaddataPagination();
        initLeadClosed();
    });
}

// Management
function init_leads_management() {
    $("select#leads_replica").unbind('change').change(function(){
        search_leads();
        initLeadClosed();
    });
    $("div.leads_add").unbind('click').click(function(){
        add_lead();
    });
    $("div.leadsearchall").unbind('click').click(function(){
        search_leads();
    });
    $("div.leadsearchclear").unbind('click').click(function(){
        $("input.lead_searchinput").val('');
        search_leads();
    });
    $("select#sorttime").unbind('change').change(function(){
        initLeaddataPagination();
    });
    $("#sortprior").unbind('change').change(function(){
        search_leads();
    })
    $("input.lead_searchinput").keypress(function(event){
        if (event.which == 13) {
            search_leads();
        }
    });
}

function initLeaddataPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totallead').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpagelead").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.leadlist_pagination").empty();
        $("#curpagelead").val(0);
        pageLeaddataCallback(0);
    } else {
        var curpage = $("#curpagelead").val();
        // Create content inside pagination element
        $("div.leadlist_pagination").empty().mypagination(num_entries, {
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
    var params=new Array();
    params.push({name:'limit',value:$("#perpagelead").val()});
    params.push({name:'maxval', value:$('#totallead').val()});
    params.push({name:'offset', value: page_index});
    var usrreplic='';
    var leadtype='';
    var search=$("input.lead_searchinput").val();
    if (search=='') {
        usrreplic=$("#leads_replica").val();
        leadtype=$("#sortprior").val();
    }
    params.push({name:'search', value:search});
    params.push({name:'usrrepl', value:usrreplic});
    params.push({name:'sorttime', value:$("select#sorttime").val()});
    params.push({name:'leadtype', value:leadtype});
    params.push({name: 'brand', value: $("#leadsveiwbrand").val()});
    var url='/leads/leadpage_data';
    $("#loader").css('display','block');
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.lead_listdata").empty().html(response.data.leadcontent);
            // $("div.leadscoreboard").empty().html(response.data.scoredat);
            $("#curpagelead").val(page_index);
            init_leadpage_manage();
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function init_leadpage_manage() {
    $("div.lead_datarow").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        });
    $("div.lead_datarow").unbind('click').click(function(){
        edit_lead($(this).data('lead'));
    });
    $("div.closeprevview.active").unbind('click').click(function(){
        var date=$(this).data('start');
        show_closed_totals(date,'prev');
    });
    $("div.closenextview.active").unbind('click').click(function(){
        var date=$(this).data('start');
        show_closed_totals(date,'next');
    });
    $("div.closedtotalshowfeature").unbind('click').click(function(){
        var curstate=$("input#showfuturereport").val();
        if (curstate==0) {
            $("input#showfuturereport").val('1');
            $("div.closedtotalshowfeature").empty().html('Hide Future Weeks');
        } else {
            $("input#showfuturereport").val('0');
            $("div.closedtotalshowfeature").empty().html('Show Future Weeks');
        }
        initLeadClosed();
    });
}

function initLeadClosed() {
    params = new Array();
    params.push({name: 'brand', value: $("#leadsveiwbrand").val()});
    params.push({name: 'user_id', value: $("#leads_replica").val()});
    params.push({name: 'showfeature', value: $("input#showfuturereport").val()});
    var url="/leads/leadsclosed_data";
    $("#loader").show();
    $.post(url,{'user_id': user_id,'showfeature':show_featue}, function(response){
        if (response.errors=='') {
            $("div#leadcloseddataarea").empty().html(response.data.content);
            init_leadclosed_content();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_leadclosed_content() {
    $("div.weekdatarow div.weekday").unbind('click').click(function(){
        var week=$(this).parent('div').data('week');
        // .is(":visible");
        if ($("div.weekdatadetails[data-week='"+week+"']").is(":visible")==false) {
            // Get Data, show details
            var bgn=$(this).parent('div').data('start');
            var end=$(this).parent('div').data('end');
            show_week_details(week, bgn, end);
        } else {
            $("div.weekdatadetails[data-week='"+week+"']").hide(400);
        }
    });
    /* Current points cell */
    $("div.weekdatarow div.curpoints").each(function(){
        var id=$(this).prop('id');
        var href=$(this).data('viewsrc');
        if (href!='') {
            $("div#"+id).qtip({
                content : {
                    text: function(event, api) {
                        $.ajax({
                            // url: href // Use href attribute as URL
                            url: api.elements.target.data('viewsrc') // Use href attribute as URL
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
                show: {
                    event: 'click'
                },
                position: {
                    my: 'bottom right',
                    at: 'middle left',
                },
                style: {
                    classes: 'curpoints_tooltip'
                },
            });
        }
    });
    $("div.weekdatarow div.ordersnum").each(function(){
        var id=$(this).prop('id');
        var href=$(this).data('viewsrc');
        if (href!='') {
            init_weekorders_tips(id);
        }
    });
    $("div.weekdatarow div.ordersrevenue").each(function(){
        var id=$(this).prop('id');
        var href=$(this).data('viewsrc');
        if (href!='') {
            init_weekorders_tips(id);
        }
    });
    $("div.weekdatarow div.ordersprofit").each(function(){
        var id=$(this).prop('id');
        var href=$(this).data('viewsrc');
        if (href!='') {
            init_weekorders_tips(id);
        }
    });
    $("div.weekdatarow div.newleads").each(function(){
        var id=$(this).prop('id');
        var href=$(this).data('viewsrc');
        if (href!='') {
            init_weekleads_tips(id);
        }
    });
    $("div.weekdatarow div.workleads").each(function(){
        var id=$(this).prop('id');
        var href=$(this).data('viewsrc');
        if (href!='') {
            init_weekleads_tips(id);
        }
    });
    $("div.weekdatarowtotal").children('div.weekday').qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: '/leads/leadsclosed_yeartotals' // Use href attribute as URL
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
        show : {
            'event' : 'click',
        },
        position: {
            my: 'bottom right',
            at: 'middle left',
        },
        style: {
            classes : 'qtip-plain yeartodate_tooltip',
        }
    });
}

function init_weekorders_tips(id) {
    $("div#"+id).qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('viewsrc') // Use href attribute as URL
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                    $("div.ordertotaldatarow div.ordernum").unbind('click').click(function(){
                        var order=$(this).data('order');
                        $("div.bt-wrapper").css('visibility','hidden');
                        order_artstage(order);
                    });
                }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to error
                    api.set('content.text', status + ': ' + error);
                });
                return 'Loading...'; // Set some initial text
            }
        },
        show : {
            'event' : 'click',
        },
        hide : {
            'event' : 'click',
        },
        position: {
            my: 'bottom right',
            at: 'middle left',
        },
        style: {
            classes : 'qtip-plain weekuserorder_tooltip',
        }
    });
}

function init_weekleads_tips(id) {
    var usrrepl = $("#leads_replica").val();
    var popupwidth = '386px';
    if (usrrepl == '') {
        popupwidth = '212px';
    }
    $("div#"+id).qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('viewsrc') // Use href attribute as URL
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                    $("div.leadtotaldatarow div.leadnumber").unbind('click').click(function () {
                        var lead = $(this).data('lead');
                        $("div.bt-wrapper").css('visibility', 'hidden');
                        edit_lead(lead);
                    });
                }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to error
                    api.set('content.text', status + ': ' + error);
                });
                return 'Loading...'; // Set some initial text
            }
        },
        show : {
            'event' : 'click',
        },
        hide : {
            'event' : 'click',
        },
        position: {
            my: 'bottom right',
            at: 'middle left',
        },
        style: {
            classes : 'qtip-plain weekuserleads_tooltip',
        }
    })
}

function show_week_details(week, bgn, end) {
    var params=new Array();
    params.push({name: 'week', value: week});
    params.push({name: 'start', value: bgn});
    params.push({name: 'end', value: end});
    params.push({name: 'user_id', value: $("select#leads_replica").val()});
    var url="/leads/leadsclosed_details";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.weekdatadetails[data-week='"+week+"']").empty().html(response.data.content).show(400);
        } else {
            show_error(response);
        }
    },'json')
}

/* Search in user Leads */
function search_usrdat(userid) {
    var url="/leads/search_leads";
    var search=$("#leadsearch").val();
    var params=new Array();
    params.push({name: 'usrrepl', value:userid});
    params.push({name: 'leadtype', value: $("#sortprior").val()});
    params.push({name: 'search', value: search});
    params.push({name: 'brand', value: $("#leadsveiwbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#totallead").val(response.data.totalrec);
            initLeaddataUserPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Change sorting option */
function search_leads() {
    var search=$("input.lead_searchinput").val();
    var params = new Array();
    params.push({name: 'brand', value: $("#leadsveiwbrand").val()});
    params.push({name: 'search', value: search});
    var usrreplic='';
    var leadtype='';
    if (search=='') {
        usrreplic=$("#leads_replica").val();
        leadtype=$("#sortprior").val();
    }
    params.push({name: 'usrrepl', value: usrreplic});
    params.push({name: 'leadtype', value: leadtype});

    var url="/leads/search_leads";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#totallead").val(response.data.totalrec);
            initLeaddataPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function show_closed_totals(date, direction) {
    var user_id=$("#leads_replica").val();
    var url="/leads/leadsclosed_totals";
    $.post(url, {'user_id':user_id, 'date':date,'direction': direction}, function(response){
        if (response.errors=='') {
            $("div#leadclosedtotalarea").empty().html(response.data.content);
            init_leadpage_manage();
        } else {
            show_error(response);
        }
    },'json');
}


/* ?????? */
function show_quotedetails(obj) {
    var quote_id=obj.id.substr(7);
    var url="/leads/show_quote_detail";
    var formdat=$("form#leadeditform").serializeArray();
    formdat.push({name: "quote_id", value: quote_id});
    $.post(url, formdat, function(response){
        if (response.errors=='') {
            window.open(response.data.url, 'quotewin', 'width=600, height=800,toolbar=1')
        } else {
            show_error(response);
        }
    }, 'json');

}

function show_questdetails(obj) {
    var quest_id=obj.id.substr(7);
    var url="/leads/show_question_detail";
    var formdat=$("form#leadeditform").serializeArray();
    formdat.push({name: "quest_id", value: quest_id});
    $.post(url, formdat, function(response){
        if (response.errors=='') {
            $("div#pop_content").empty().html(response.data.content);
            $("div#popupContact").css('width','727px');
            $("a#popupContactClose").unbind('click').click(function(){
                restore_leadform();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function show_proofdetails(obj) {
    var proof_id=obj.id.substr(6);
    var url="/leads/show_proof_details";
    var formdat=$("form#leadeditform").serializeArray();
    formdat.push({name: "proof_id", value: proof_id});
    $.post(url, formdat, function(response){
        if (response.errors=='') {
            $("div#pop_content").empty().html(response.data.content);
            $("div#popupContact").css('width','889px');
            $("a#popupContactClose").unbind('click').click(function(){
                restore_leadform();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}
