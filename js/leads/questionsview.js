function init_questions() {
    initQuestionPagination();
    $("select#questions_status").unbind('change').change(function(){
        search_questions();
    })
    $("select#questhideincl").unbind('change').change(function(){
        search_questions();
    });
    /* Enter as start search */
    $("input#questsearch").keypress(function(event){
        if (event.which == 13) {
            search_questions();
        }
    });
    /* Search actions */
    $("a#clear_quest").unbind('click').click(function(){
        $("select#questions_status").val(1);
        $("input#questsearch").val('');
        search_questions();
    })
    $("a#find_quest").unbind('click').click(function(){
        search_questions();
    });
    // Change Brand
    $("#questionsviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#questionsviewbrand").val(brand);
        $("#questionsviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#questionsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#questionsviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_questions();
    });
}


function initQuestionPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalquest').val();
    var perpage = $("#perpagequest").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#questions .Pagination").empty();
        $("#curpagequest").val(0);
        pageQuestionsCallback(0);
    } else {
        var curpage = $("#curpagequest").val();
        // Create content inside pagination element
        $("div#questions .Pagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageQuestionsCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageQuestionsCallback(page_index) {
    var perpage = $("#perpagequest").val();
    var maxval = $('#totalquest').val();
    var empty_search='Customer,company, email..';
    var search=$("input#questsearch").val();
    if (search==empty_search) {
        search='';
    }
    var params=new Array();
    params.push({name:'search',value: search});
    params.push({name:'assign',value:$("select#questions_status").val()});
    params.push({name:'brand',value:$("input#questionsviewbrand").val()});
    params.push({name:'hideincl',value:$("select#questhideincl").val()});
    params.push({name:'offset', value: page_index});
    params.push({name:'limit', value:perpage});
    params.push({name:'maxval',value:maxval});
    params.push({name:'order_by',value:$("input#sortquest").val()});
    params.push({name:'direction',value:$("input#sortdirquest").val()});

    var url='/leads/questionsdat';
    $("#loader").css('display','block');
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.question_tabledat").empty().html(response.data.content);
            $("#curpagequest").val(page_index);
            /* change size */
            init_questions_content();
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function init_questions_content() {
    $(".quest_tabrow").find('.quest_text').qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'right center',
            at: 'center left',
        },
        style: {
            classes: 'question_tooltip'
        }
    });
    $("div.quest_tabrow").hover(
        function(){
            $(this).addClass("current_row");
        },
        function(){
            $(this).removeClass("current_row");
        }
    );
    $("div.quest_tabrow").click(function(){
        var question = $(this).data('email');
        showquestdetails(question);
    })
    $("div.quest_websys_dat").click(function(){
        var quest_id=$(this).data('questid');
        quest_include(quest_id);
        return false;
    });
    $("div.questassign").click(function(){
        var questid=$(this).data('questid');
        change_questreplic(questid);
        return false;
    });

}

function quest_include(quest_id) {
    var url="/leads/question_include";
    /* Show - hide */
    var show_incl=$("select#questhideincl").val();
    $.post(url, {'question_id':quest_id}, function(response){
        if (response.errors=='') {
            $("div.quest_websys_dat[data-questid="+quest_id+"]").empty().html(response.data.newicon);
            // Change Number and tab class
            $("a#questionslnk").removeClass('curmail').removeClass('empval').addClass(response.data.newclass);
            $("div#newquestions").empty().html(response.data.newmsg);
            if (show_incl=='1') {
                // Hide all not included
                search_questions();
            }
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_questreplic(quest_id) {
    // quest_id=quest_id.substr(4);
    var url="/leads/change_status";
    $.post(url, {'quest_id':quest_id,'type':'question'}, function(response){
        if (response.errors=='') {
            show_popup('editmail_form');
            $("div#pop_content").empty().html(response.data.content);
            /* Activate close */
            $("a#popupContactClose").click(function(){
                disablePopup();
            })
            /* Change Lead data */
            $("select#lead_id").searchable();
            $("select#lead_id").change(function(){
                change_leaddata();
            })
            $("a.savequest").click(function(){
                update_queststatus();
            })
            $("div#pop_content div.leads_addnew").click(function(){
                create_leadquest();
            })
        } else {
            show_error(response);
        }
    }, 'json');
    return false;
}

function change_leaddata() {
    var lead_id=$("#lead_id").val();
    if (lead_id!='') {
        var url="/leads/change_leadrelation";
        $.post(url, {'lead_id':lead_id}, function(response){
            if (response.errors=='') {
                $("div#pop_content div.leaddate").empty().html(response.data.lead_date);
                $("div#pop_content div.leadcustomer").empty().html(response.data.lead_customer);
                $("div#pop_content div.leadcustommail").empty().html(response.data.lead_mail);
            } else {
                show_error(response);
            }
        }, 'json')

    }
}

function update_queststatus() {
    var url="/leads/savequeststatus";
    var dat=$("form#msgstatus").serializeArray();
    $.post(url, dat, function(response){
        if (response.errors=='') {
            disablePopup();
            $("div#newartproofs").empty().html(response.data.total_proof);
            $("div#newonlinequotes").empty().html(response.data.total_quote);
            $("div#newquestions").empty().html(response.data.total_quest);
            if (response.data.sumquote=='') {
                $("a#onlinequotelnk").removeClass('curmail');
            } else {
                $("a#onlinequotelnk").addClass('curmail');
            }
            if (response.data.sumproofs=='') {
                $("a#onlineprooflnk").removeClass('curmail');
            } else {
                $("a#onlineprooflnk").addClass('curmail');
            }
            if (response.data.sumquest=='') {
                $("a#questionslnk").removeClass('curmail');
            } else {
                $("a#questionslnk").addClass('curmail');
            }
            if (response.data.type=='Leads') {
                initQuotesPagination();
            } else if (response.data.type=='Art_Submit') {
                initProofPagination();
            } else {
                initQuestionPagination();
            }
            /*  */
        } else {
            show_error(response);
        }
    }, 'json');
}



function change_questrow(mail_id) {
    var url="/leads/maildetails";
    var type="Question";
    var rowid='qrow'+mail_id;
    var rownum=$("#"+rowid+" div.quest_numrec_dat").text();

    $.post(url, {'mail_id':mail_id, 'type':type, 'rownum':rownum}, function(response){
        if (response.errors=='') {
            $("#"+rowid).empty().html(response.data.content);
            $("#"+rowid).removeClass('leadentered').addClass(response.data.rowclass);
        } else {
            show_error(response);
        }
    }, 'json');
}

function search_questions() {
    var search=$("input#questsearch").val();
    var params=new Array();
    params.push({name:'search',value: search});
    params.push({name:'assign',value:$("select#email_status").val()});
    params.push({name:'brand',value:$("input#questionsviewbrand").val()});
    params.push({name:'hideincl',value:$("select#questhideincl").val()});
    var url="/leads/questcount";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#totalquest").val(response.data.total_rec);
            initQuestionPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}
function showquestdetails(question) {
    var url="/leads/question_detail";
    $.post(url, {'quest_id':question}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html('View Question');
            $("#pageModal").find('div.modal-content').css('width','753px');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal('show');
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function replyquestmail(mail) {
    var mailtourl = "mailto:"+mail;
    location.href = mailtourl;
    return false;
}

function create_leadquest() {
    var mail_id=$("input#mail_id").val();
    var type='Question';
    var leademail_id=$("input#leademail_id").val();
    var url="/leads/create_leadmessage";
    $.post(url, {'mail_id':mail_id, 'type':type,'leadmail_id':leademail_id}, function(response){
        if (response.errors=='') {
            disablePopup();
            $("div#newartproofs").empty().html(response.data.total_proof);
            $("div#newonlinequotes").empty().html(response.data.total_quote);
            $("div#newquestions").empty().html(response.data.total_quest);
            if (response.data.sumquote=='') {
                $("a#onlinequotelnk").removeClass('curmail');
            } else {
                $("a#onlinequotelnk").addClass('curmail');
            }
            if (response.data.sumproofs=='') {
                $("a#onlineprooflnk").removeClass('curmail');
            } else {
                $("a#onlineprooflnk").addClass('curmail');
            }
            if (response.data.sumquest=='') {
                $("a#questionslnk").removeClass('curmail');
            } else {
                $("a#questionslnk").addClass('curmail');
            }
            show_new_lead(response.data.leadid,'question');
        } else {
            show_error(response);
        }
    }, 'json');
}