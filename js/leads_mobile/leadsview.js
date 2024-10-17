function init_leadsview() {
    initLeaddataPagination();
    // initLeadClosed();
    init_leads_management();
}

function init_leads_management() {
    $("select#leads_replica").unbind('change').change(function(){
        search_leads();
        // initLeadClosed();
    });
    $("div.leads_add").unbind('click').click(function(){
        var brand = $("#leadsveiwbrand").val();
        if (brand=='ALL') {
        } else {
            add_lead(brand);
        }
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
        $("nav.leadlist_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeaddataCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 3,
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
    $.post(url,params,function(response) {
        if (response.errors=='') {
            $("table.tbl-leads").find('tbody').empty().html(response.data.leadcontent);
            $("#curpagelead").val(page_index);
            init_leadpage_manage();
        } else {
            show_error(response);
        }
    },'json');
}

function init_leadpage_manage() {
    // $("div.lead_datarow").unbind('click').click(function(){
    //     edit_lead($(this).data('lead'));
    // });
    // $("div.closeprevview.active").unbind('click').click(function(){
    //     var date=$(this).data('start');
    //     show_closed_totals(date,'prev');
    // });
    // $("div.closenextview.active").unbind('click').click(function(){
    //     var date=$(this).data('start');
    //     show_closed_totals(date,'next');
    // });
    // $("div.closedtotalshowfeature").unbind('click').click(function(){
    //     var curstate=$("input#showfuturereport").val();
    //     if (curstate==0) {
    //         $("input#showfuturereport").val('1');
    //         $("div.closedtotalshowfeature").empty().html('Hide Future Weeks');
    //     } else {
    //         $("input#showfuturereport").val('0');
    //         $("div.closedtotalshowfeature").empty().html('Show Future Weeks');
    //     }
    //     initLeadClosed();
    // });

}