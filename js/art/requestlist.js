var empty_proofsearch='Customer,company, email..';
var main_proofurl="/art"
function init_proofdata() {
    $("select#proof_status").unbind('change').change(function(){
        search_proofs();
    })
    $("select#proofbrand").unbind('change').change(function(){
        search_proofs();
    })
    $("select#hidedelproofs").unbind('change').change(function(){
        search_proofs();
    })
    /* Enter as start search */
    $("input#proofsearch").keypress(function(event){
        if (event.which == 13) {
            search_proofs();
        }
    });
    /* Search actions */
    $("a#clear_proof").unbind('click').click(function(){
        $("select#proof_status").val(1);
        $("select#proofbrand").val("");
        $("input#proofsearch").val('');
        search_proofs();
    })
    $("a#find_proof").unbind('click').click(function(){
        search_proofs();
    })
    initProofPagination();
}

function initProofPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalproof').val();
    var perpage = $("#perpageproof").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div#proofpagination").empty();
        $("#curpageproof").val(0);
        pageProofsCallback(0);
    } else {
        var curpage = $("#curpageproof").val();
        // Create content inside pagination element
        $("div#proofpagination").empty().mypagination(num_entries, {
            current_page: curpage,
            callback: pageProofsCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageProofsCallback(page_index) {
    var search=$("input#proofsearch").val();
    if (search==empty_proofsearch) {
        search='';
    }
    var params=new Array();
    params.push({name:'search',value:search});
    params.push({name:'assign',value:$("select#proof_status").val()});
    params.push({name:'brand',value:$("select#proofbrand").val()});
    params.push({name:'offset',value:page_index});
    params.push({name:'limit',value:$("#perpageproof").val()});
    params.push({name:'maxval',value:$('#totalproof').val()});
    params.push({name:'order_by',value:$("#orderproof").val()});
    params.push({name:'direction',value:$("#direcproof").val()});
    params.push({name:'hideart',value:$("#hideartproof").val()});
    var showdel=$("input#hidedelproofs").prop('checked');
    /* var deleted=0;
    if (showdel==true) {
        deleted=1;
    }*/
    var deleted=$("select#hidedelproofs").val();
    params.push({name:'show_deleted',value:deleted})

    var url=main_proofurl+'/proof_listdata';
    $("#loader").css('display','block');
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.proof_tabledat").empty().html(response.data.content);
            $("#curpageproof").val(page_index);
            init_prooflistmanage();
            $("#loader").css('display','none');
        } else {
            $("#loader").css('display','none');
            show_error(response);
        }
    },'json');
    return false;
}

function init_prooflistmanage() {
    /* change size */
    var maxh=$("div.proof_tabledat").css('max-height');
    maxh=parseInt(maxh.replace('px',''));
    var dath=$("div.proof_tabledat").css('height');
    dath=parseInt(dath.replace('px', ''));
    if (dath<maxh) {
        $("div.proof_tabrow").css('width','969px');
    }
    var rowid='';
    $("div.proof_tabrow").hover(
        function(){
            rowid=this.id;
            $("#"+rowid).addClass("current_row");
        },
        function(){
            rowid=this.id;
            $("#"+rowid).removeClass("current_row");
        }
    );
    // $("div.proof_replica").click(function(){
    $("div.artdata").click(function(){
        var mailid=$(this).parent("div.artdataarea").data('proofid');
        artproof_lead(mailid);
        return false;
    });
    $("div.proof_brand_dat").click(function(){
        var mailid=$(this).data('proofid');
        artproof_lead(mailid);
        return false;
    })
    /* All other divs */
    $("div.showproofdetails").click(function(){
        var proofid=$(this).parent("div.proof_tabrow").prop('id');
        showproofdetails(proofid);
        return false;
    })
    $("div.proof_deldata").click(function(){
        var proof_id=$(this).data('proofid');
        var proofnum=$("div#profrow"+proof_id+" div.proof_brand_dat").text();
        var exectype=$(this).children('img').data('type');
        if (exectype=='delete') {
            delete_proof(proof_id, proofnum);
        } else {
            revert_proof(proof_id, proofnum);
        }
    })
    $("div.proof_leadnum_dat").click(function(){
        var lead_id=$(this).data('leadid');
        var mailid=$(this).data('proofid');
        if (lead_id==0) {
            prooflead(mailid);
        } else {
            profedit_lead(lead_id);
        }
    })

    $("div.proof_note_dat").click(function(){
        var mailid=$(this).data('proofid');
        edit_note(mailid);
    })
    // $("div.proof_note_dat").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 220,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "most",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    // $("div.proof_parsedata").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 220,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "most",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    //
    // $("div.prooflastmessageview").each(function(){
    //     $(this).bt({
    //         ajaxCache: false,
    //         fill : '#1DCD19',
    //         cornerRadius: 10,
    //         width: 220,
    //         padding: 10,
    //         strokeWidth: '2',
    //         positions: "most",
    //         strokeStyle : '#000000',
    //         strokeHeight: '18',
    //         cssClass: 'art_tooltip',
    //         ajaxPath: ["$(this).data('messageview')"]
    //     });
    // });

    $("div.proof_includ_dat").click(function(){
        var mailid=$(this).data('proofid');
        proof_include(mailid);
    })
}

function proof_include(mailid) {
    var url=main_proofurl+"/proof_include";
    $.post(url, {'email_id': mailid}, function(response){
        if (response.errors=='') {
            $("div.proof_includ_dat[data-proofid="+mailid+"]").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    }, 'json');
}

function showproofdetails(objid) {
    var proof_id=objid.substr(7);
    var url=main_proofurl+"/proof_details";
    $.post(url, {'proof_id':proof_id}, function(response){
        if (response.errors=='') {
            show_popup('proof_dialog');
            $("div#pop_content").empty().html(response.data.content);
            $("a#popupContactClose").click(function(){
                disablePopup();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}


function create_leadproof() {
    var mail_id=$("input#mail_id").val();
    var type='Proof';
    var leademail_id=$("input#leademail_id").val();
    var url="/leads/create_leadmessage";
    $.post(url, {'mail_id':mail_id, 'type':type,'leadmail_id':leademail_id}, function(response){
        if (response.errors=='') {
            disablePopup();
            $("div#newprooftotal").empty().html(response.data.total_proof);
            $("div#newquotestotal").empty().html(response.data.total_quote);
            $("div#newquestionstotal").empty().html(response.data.total_quest);
            show_new_lead(response.data.leadid, 'proof');
        } else {
            show_error(response);
        }
    }, 'json');
}


function search_proofs() {
    var search=$("input#proofsearch").val();
    if (search==empty_proofsearch) {
        search='';
    }
    var assign=$("select#proof_status").val();
    var brand=$("select#proofbrand").val();
    // var showdel=$("input#hidedelproofs").prop('checked');
    var deleted=$("select#hidedelproofs").val();
    var url=main_proofurl+"/proof_count";
    $.post(url, {'assign':assign,'search':search, 'brand':brand,'show_deleted':deleted}, function(response){
        if (response.errors=='') {
            $("input#totalproof").val(response.data.total_rec);
            initProofPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Call email client */
function replyquestmail(mail) {
    var mailtourl = "mailto:"+mail;
    location.href = mailtourl;
    return false;
}

function delete_proof(proof_id, proofnum) {
    if (confirm('Mark proof request '+proofnum+' as VOID ?')) {
        var url=main_proofurl+"/proof_delete";
        $.post(url, {'proof_id':proof_id,'type':'delete'}, function(response){
            if (response.errors=='') {
                initProofPagination();
            } else {
                show_error(response);
            }
        }, 'json')
    }
}

function revert_proof(proof_id, proofnum) {
    if (confirm('Revert deleted proof request '+proofnum+' ?')) {
        var url=main_proofurl+"/proof_delete";
        $.post(url, {'proof_id':proof_id,'type':'revert'}, function(response){
            if (response.errors=='') {
                initProofPagination();
            } else {
                show_error(response);
            }
        }, 'json')
    }
}

function prooflead(mailid) {
    var url="/art/change_status";
    $.post(url, {'quest_id':mailid, 'type':'proof'}, function(response){
        if (response.errors=='') {
            // show_popup('editmail_form');
            // $("div#pop_content").empty().html(response.data.content);
            $("#artModalLabel").empty().html('Lead Assign');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").modal('show');
            /* Activate close */
            $("select#lead_id").searchable();
            /* Change Lead data */
            $("select#lead_id").change(function(){
                change_leaddata();
            })
            $("a.savequest").click(function(){
                update_queststatus();
            })
            $("div#pop_content div.leads_addnew").click(function(){
                create_leadproof();
            })
        } else {
            show_error(response);
        }
    }, 'json');
    return false;
}

function edit_note(mailid) {
    var url=main_proofurl+"/proof_openartnote";
    $.post(url, {'mail_id':mailid}, function(response){
        if (response.errors=='') {
            show_popup("edit_area");
            $("div#pop_content").empty().html(response.data.content);
            $("div#pop_content div.saveordernote").click(function(){
                save_proofnote();
            })
            $("a#popupContactClose").unbind('click').click(function(){
                disablePopup();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_proofnote() {
    var mail_id=$("div#pop_content input#order_id").val();
    var art_note=$("div#pop_content #art_note").val();
    var url=main_proofurl+"/proof_saveartnote";
    $.post(url, {'mail_id':mail_id,'art_note':art_note}, function(response){
        if(response.errors=='') {
            disablePopup();
            initProofPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function profedit_lead(lead_id) {
    var url="/leads/edit_lead";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            show_popup('leadpopupdat');
            $("div#pop_content").empty().html(response.data.content);
            init_edits();
        } else {
            show_error(response);
        }
    }, 'json');
}