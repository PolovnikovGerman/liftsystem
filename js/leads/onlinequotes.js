/* Open page */
function init_quotes() {
    initQuotesPagination();
    // Change Brand
    $("#onlinequotesbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#onlinequotesbrand").val(brand);
        $("#onlinequotesbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#onlinequotesbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#onlinequotesbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_quotes();
    });
    $("select.quote_status_select").change(function(){
        search_quotes();
    })
    $("select.quotehideincl").change(function(){
        search_quotes();
    })
    /* Enter as start search */
    $("input#quotesearch").keypress(function(event){
        if (event.which == 13) {
            search_quotes();
        }
    });
    /* Search actions */
    $("a#clear_quote").click(function(){
        $("select#quote_status").val(1);
        $("select#quotebrand").val("");
        $("input#quotesearch").val('');
        search_quotes();
    })
    $("a#find_quote").click(function(){
        search_quotes();
    })
}
function initQuotesPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalquotes').val();
    var perpage = $("#perpagequotes").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        // $("div#onlinequote .Pagination").empty();
        $(".quotes_pagination").empty();
        $("#curpagequotes").val(0);
        pageQuotesCallback(0);
    } else {
        var curpage = $("#curpagequest").val();
        // Create content inside pagination element
        // $("div#onlinequote .Pagination").empty().mypagination(num_entries, {
        $(".quotes_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageQuotesCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageQuotesCallback(page_index) {
    var params = new Array();
    params.push({name: 'limit', value: $("#perpagequotes").val()});
    params.push({name: 'maxval', value: $('#totalquotes').val()});
    params.push({name: 'search', value: $("input#quotesearch").val()});
    params.push({name: 'assign', value: $("select#quote_status").val()});
    params.push({name: 'brand', value: $("#onlinequotesbrand").val()});
    params.push({name: 'offset', value: page_index});
    params.push({name: 'order_by', value: $("#orderquotes").val()});
    params.push({name: 'direction', value: $("#direcquotes").val()});
    params.push({name: 'hideincl', value: $("select#quoteincl").val()});
    var url='/leads/quotesdat';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.quotes_tabledat").empty().html(response.data.content);
            $("#curpagequest").val(page_index);
            quote_content_init();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function quote_content_init() {
    $("div.quotes_tabrow").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        }
    );
    $("a.openquotadoc").unbind('click').click(function(){
        var winname='showquotadoc';
        var url=$(this).data('link');
        var params = "left=200,top=200,width=820,height=480, menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes"
        window.open(url, winname, params);
    })
    $("div.quotes_tabrow").find('.quotecalldetails').click(function(){
        var quote = $(this).parent().data('email')
        showquotedetails(quote);
    })
    $("div.quoteassign").click(function(){
        var mailid=$(this).data('quoteid');
        change_quotereplic(mailid);
        return false;
    });
    $("div.quote_brand").click(function(){
        var quote_id=$(this).data('quoteid');
        quote_include(quote_id);
        return false;
    });
    $("div.quotelead").unbind('click').click(function (){
        var lead_id=$(this).data('lead');
        if (parseInt(lead_id)!=0) {
            var url="/leadmanagement/edit_lead";
            $.post(url, {'lead_id':lead_id}, function(response){
                if (response.errors=='') {
                    $("#leadformModalLabel").empty().html(response.data.title);
                    $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
                    $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
                    $("#leadformModal").modal({backdrop: 'static', keyboard: false, show: true});
                    init_lead_cloneemail();
                    init_leadpopupedit();
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    })
}

function quote_include(quote_id) {
    var url="/leads/quote_include";
    var show_incl=$("select#quoteincl").val();
    $.post(url, {'quote_id':quote_id}, function(response){
        if (response.errors=='') {
            $("div.quote_brand_dat[data-quoteid="+quote_id+"]").empty().html(response.data.newicon);
            // Change Number and tab class
            $("a#onlinequotelnk").removeClass('curmail').removeClass('empval').addClass(response.data.newclass);
            $("div#newonlinequotes").empty().html(response.data.newmsg);
            if (show_incl=='1') {
                search_quotes();
            }
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_quotereplic(quote_id) {
    var url="/leads/change_status";
    $.post(url, {'quest_id':quote_id,'type':'quote'}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html('Change Quote Status');
            $("#pageModal").find('div.modal-dialog').css('width','590px');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* Activate close */
            $("select#lead_id").select2({
                dropdownParent: $('#pageModal'),
                matcher: matchStart
            });
            /* Change Lead data */
            $("select#lead_id").change(function(){
                change_leaddata();
            })
            $("a.savequest").click(function(){
                update_quotestatus();
            })
            $("div.updatequest_status").find("div.leads_addnew").click(function(){
                create_leadquote();
            })
        } else {
            show_error(response);
        }
    }, 'json');
    return false;
}

function update_quotestatus() {
    var url="/leads/savequeststatus";
    var dat=$("form#msgstatus").serializeArray();
    $.post(url, dat, function(response){
        if (response.errors=='') {
            // disablePopup();
            $("#pageModal").modal('hide');
            initQuotesPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function create_leadquote() {
    var mail_id=$("input#mail_id").val();
    var type='Quote';
    var brand = $("#onlinequotesbrand").val();
    var leademail_id=$("input#leademail_id").val();
    var url="/leads/create_leadmessage";
    $.post(url, {'mail_id':mail_id, 'type':type,'leadmail_id':leademail_id}, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
            // $("div#newartproofs").empty().html(response.data.total_proof);
            // $("div#newonlinequotes").empty().html(response.data.total_quote);
            // $("div#newquestions").empty().html(response.data.total_quest);
            show_new_lead(response.data.leadid,'quote', brand);
        } else {
            show_error(response);
        }
    }, 'json');
}

function search_quotes() {
    var params = new Array();
    params.push({name: 'brand', value: $("#onlinequotesbrand").val()});
    params.push({name: 'assign', value: $("select#quote_status").val()});
    params.push({name: 'search', value: $("input#quotesearch").val()});
    params.push({name: 'hideincl', value: $("select#quoteincl").val()});
    var url="/leads/quotecount";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#totalquotes").val(response.data.total_rec);
            initQuotesPagination();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');

}
function showquotedetails(quote_id) {
    var url="/leads/quote_details";
    $.post(url,{'quote_id':quote_id},function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html('View Online Quote');
            $("#pageModal").find('div.modal-dialog').css('width','753px');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
        } else {
            show_error(response);
        }
    },'json');
}