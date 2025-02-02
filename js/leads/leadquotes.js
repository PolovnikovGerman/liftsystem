function init_leadquotes() {
    initLeadQuotesPagination();
}

function initLeadQuotesPagination() {
    // count entries inside the hidden content
    var num_entries = $('#leadquotestotal').val();
    // var perpage = itemsperpage;
    var perpage = $("#perpageleadqoutes").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.leadqutespaginator").empty();
        $("#curpageleadquote").val(0);
        pageLeadQuotesCallback(0);
    } else {
        var curpage = $("#curpageleadquote").val();
        // Create content inside pagination element
        $("div.leadqutespaginator").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageLeadQuotesCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageLeadQuotesCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value: $("#perpageleadqoutes").val()});
    params.push({name:'maxval', value: $('#leadquotestotal').val()});
    params.push({name:'offset', value: page_index});
    params.push({name: 'brand', value: $("#leadquotesbrand").val()});
    params.push({name: 'search', value: $("#leadquotessearch").val()});
    params.push({name: 'replica', value: $("#quotareplica").val()});
    var url='/leads/leadquotesdata';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#leadquote_tabledat").empty().html(response.data.content);
            $("#curpageleadquote").val(page_index);
            // init_leadpage_manage();
            if (parseInt($('#leadquotestotal').val()) > 25) {
                $(".leadquote_tabledat").scrollpanel({
                    'prefix' : 'sp-'
                });
                leftmenu_alignment();
            }
            init_leadquotes_list();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_leadquotes_list() {
    // Search
    $("#leadquotessearch").unbind('keypress').keypress(function(event){
        if (event.which == 13) {
            search_leadquotes();
        }
    });
    $(".leadquotessearchall").unbind('click').click(function (){
        search_leadquotes();
    });
    $(".leadquotessearchclear").unbind('click').click(function (){
        $("#leadquotessearch").val('');
        search_leadquotes();
    });
    $("#quotareplica").unbind('change').change(function (){
        search_leadquotes();
    });
    $(".leadquote_pdf").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'quote_id', value: $(this).data('quote')});
        var url = '/leadquote/quotepdfdoc';
        $.post(url, params, function (response){
            if (response.errors=='') {
                var newWin = window.open(response.data.docurl,"Quoute PDF","width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
            } else {
                show_error(response);
            }
        },'json')
    });
    $(".leadquote_number").unbind('click').click(function(){
        var lead = $(this).data('lead');
        var quote = $(this).data('quote');
        var url="/leadmanagement/edit_lead";
        $.post(url, {'lead_id':lead}, function(response){
            if (response.errors=='') {
                $("#leadformModalLabel").empty().html(response.data.title);
                $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
                $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
                $("#leadformModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_lead_cloneemail();
                init_leadpopupedit();
                leadquote_edit(quote);
            } else {
                show_error(response);
            }
        }, 'json');
    });
}

function search_leadquotes() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadquotesbrand").val()});
    params.push({name: 'search', value: $("#leadquotessearch").val()});
    params.push({name: 'replica', value: $("#quotareplica").val()});
    var url='/leads/leadquotessearch';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $('#leadquotestotal').val(response.data.total);
            initLeadQuotesPagination();
        } else {
            show_error(response);
        }
    },'json');
}