$(document).ready(function(){
    init_creditappfunc();
});

function init_creditappfunc() {
    initCreditAppPagination();
    $(".creditapp_popup").find("input.creditapptemplate").keypress(function(event){
        if (event.which == 13) {
            search_creaditappdata();
        }
    });
    $(".creditapp_popup").find("div.searchbtn").unbind('click').click(function(){
        search_creaditappdata();
    });    
    $(".creditapp_popup").find("div.cleansearchbtn").unbind('click').click(function(){
        search_creaditappdata();
    });    
    $(".creditapp_popup").find("select.filterdataselect").unbind('change').change(function(){
        search_creaditappdata();
    });    
    $(".creditapp_popup").find("div.addnew").unbind('click').click(function(){
        add_creditapp();
    });
    
}

function initCreditAppPagination() {
    // count entries inside the hidden content
    var num_entries = $('#crapptotals').val();
    // var perpage = itemsperpage;
    var perpage = $("#crappperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".credapp_pagination").empty();
        pageCreditAppCallback(0);
    } else {
        var curpage = $("#crappcurrentpage").val();
        // Create content inside pagination element
        $(".proford_pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageCreditAppCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageCreditAppCallback(page_index) {
    var search=$("input.creditapptemplate").val();
    var params=new Array();
    params.push({name:'search', value:search});
    params.push({name:'limit', value:$("#crappperpage").val()});
    params.push({name:'status', value:$(".creditapp_popup").find("select.filterdataselect").val()});
    params.push({name:'offset', value:page_index});
    var url='/creditapplication/creditappldata';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#crappcurrentpage").val(page_index);
            $('.creditappdataarea').empty().html(response.data.content);
            init_creditapp_manage();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}


function search_creaditappdata() {
    var search=$("input.creditapptemplate").val();
    var params=new Array();
    params.push({name:'search', value:search});    
    params.push({name:'status', value:$(".creditapp_popup").find("select.filterdataselect").val()});    
    var url='/creditapplication/creditapplsearch';    
    $.post(url,params,function(response){
        if (response.errors=='') {    
            $('#crapptotals').val(response.data.totals);
            $("#crappcurrentpage").val(0);
            initCreditAppPagination();
        } else {            
            show_error(response);
        }
    },'json');    
}

function init_creditapp_manage() {
    $(".creditapp_popup").find("div.edit.active").unbind('click').click(function(){
        var creditapp=$(this).data('appdat');
        var url="/creditapplication/creditappledit";
        $.post(url, {'creditapp': creditapp}, function(response){
            if (response.errors=='') {
                $('.creditappdataarea').animate({ scrollTop: 0 }, "slow");
                $("div.creditappdatarow[data-appdat='"+creditapp+"']").empty().html(response.data.content).show();
                $("div.creditappdatarow[data-appdat='"+creditapp+"']").find('input.customer').focus();
                $("div.creditapp_popup").find('div.cancelapp').unbind('click').click(function(){
                    restore_creditapp_content(creditapp);                    
                });
                init_creditapp_edit();
            } else {
                show_error(response);
            }
        },'json');        
    });
    // Approve / Reject
    $(".creditapp_popup").find("div.status.pending").unbind('click').click(function(){
        var creditapp=$(this).data('appdat');
        prepare_approve_creditapp(creditapp);
    });
    
}

// Cancel edit
function restore_creditapp_content(creditapp) {
    var url="/creditapplication/creditapplcanceledit";
    $.post(url,{'creditapp': creditapp}, function(response){
        if (response.errors=='') {
            $("div.creditappdatarow[data-appdat='"+creditapp+"']").empty().html(response.data.content);
            init_creditapp_manage();
        } else {
            show_error(response);
        }
    },'json');    
}

function add_creditapp() {
    var url="/creditapplication/creditappledit";
    $.post(url, {'creditapp': 0}, function(response){
        if (response.errors=='') {
            $('.creditappdataarea').animate({ scrollTop: 0 }, "slow");
            $("div.creditappdatarow.newapp").empty().html(response.data.content).show();
            $("div.creditappdatarow.newapp").find('input.customer').focus();
            $("div.creditapp_popup").find('div.cancelapp').unbind('click').click(function(){
                $("div.creditappdatarow.newapp").empty().hide();
            });
            init_creditapp_edit();
        } else {
            show_error(response);
        }
    },'json');
}

function init_creditapp_edit() {
    $("div.creditapp_popup").find('div.saveapp').unbind('click').click(function(){
        var url="/creditapplication/creditapplsave";
        $.post(url,{}, function(response){
            if (response.errors=='') {
                initCreditAppPagination();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.creditappinpt").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/creditapplication/creditapplchange";
        $.post(url, params, function(response){
            if (response.errors=='') {                
            } else {
                show_error(response);
            }
        },'json');
    })
}

function prepare_approve_creditapp(creditapp) {
    var url="/creditapplication/creditapplpreapprove";
    $.post(url,{'creditapp': creditapp}, function(response){
        if (response.errors=='') {
            $.colorbox({html:response.data.content});
            $.colorbox.resize();
            init_creditappapproveedit(creditapp);
        } else {
            show_error(response);
        }
    },'json');    
}

function init_creditappapproveedit(creditapp) {
    var params=new Array();
    var url="/creditapplication/creditapplapprove";
    params.push({name: 'creditapp', value: creditapp});
    $("div.approveappbutton").unbind('click').click(function(){
        params.push({name:'approve', value: 1});
        params.push({name:'reject', value: 0});
        params.push({name:'notes', value: $("textarea#review_notes").val()});        
        $.post(url, params, function(response){
            if (response.errors=='') {
                $.colorbox.close();
                initCreditAppPagination();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.rejectappbutton").unbind('click').click(function(){
        params.push({name:'approve', value: 0});
        params.push({name:'reject', value: 1});
        params.push({name:'notes', value: $("textarea#review_notes").val()});        
        $.post(url, params, function(response){
            if (response.errors=='') {
                $.colorbox.close();
                initCreditAppPagination();
            } else {
                show_error(response);
            }
        },'json');
    });
}