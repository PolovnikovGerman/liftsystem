var empty_proofsearch='Customer,company, email..';
var main_proofurl="/proofrequests"
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
        $("input#proofsearch").val('');
        search_proofs();
    })
    $("a#find_proof").unbind('click').click(function(){
        search_proofs();
    });
    // Change Brand
    $("#proofrequestsbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#proofrequestsbrand").val(brand);
        $("#proofrequestsbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#proofrequestsbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#proofrequestsbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_proofs();
    });
    initProofPagination();
}

function search_proofs() {
    var search=$("input#proofsearch").val();
    if (search==empty_proofsearch) {
        search='';
    }
    var assign=$("select#proof_status").val();
    var brand=$("input#proofrequestsbrand").val();
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
    params.push({name:'brand',value:$("input#proofrequestsbrand").val()});
    params.push({name:'offset',value:page_index});
    params.push({name:'limit',value:$("#perpageproof").val()});
    params.push({name:'maxval',value:$('#totalproof').val()});
    params.push({name:'order_by',value:$("#orderproof").val()});
    params.push({name:'direction',value:$("#direcproof").val()});
    params.push({name:'hideart',value:$("#hideartproof").val()});

    var showdel=$("input#hidedelproofs").prop('checked');
    var deleted=$("select#hidedelproofs").val();
    params.push({name:'show_deleted',value:deleted})

    var url=main_proofurl+'/proof_listdata';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("#loader").hide();
            $("div.proof_tabledat").empty().html(response.data.content);
            $("#curpageproof").val(page_index);
            init_prooflistmanage();
            $("#loader").css('display','none');
        } else {
            $("#loader").hide();
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
        // POPUP
        // artproof_lead(mailid);
        return false;
    });
    $("div.proof_brand_dat").click(function(){
        var mailid=$(this).data('proofid');
        // POPUP
        artproof_lead(mailid, 'artprooflist');
        return false;
    })
    /* All other divs */
    /* NON Exist DIV
    $("div.showproofdetails").click(function(){
        var proofid=$(this).parent("div.proof_tabrow").prop('id');
        showproofdetails(proofid);
        return false;
    })
    */
    $("div.proof_deldata").click(function(){
        var proof_id=$(this).data('proofid');
        var proofnum=$("div#profrow"+proof_id+" div.proof_brand_dat").text();
        var exectype=$(this).children('img').data('type');
        if (exectype=='delete') {
            delete_proof(proof_id, proofnum);
        } else {
            revert_proof(proof_id, proofnum);
        }
    });
    $("div.proof_leadnum_dat").click(function(){
        var lead_id=$(this).data('leadid');
        var mailid=$(this).data('proofid');
        if (lead_id==0) {
            prooflead(mailid);
        } else {
            profedit_lead(lead_id);
        }
    });

    $("div.proof_note_dat").click(function(){
        var mailid=$(this).data('proofid');
        edit_note(mailid);
    });
    $("div.proof_note_dat").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom right',
            at: 'top left',
        },
        style: 'qtip-light'
    });
    $("div.proof_parsedata").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'bottom right',
            at: 'top left',
        },
        style: 'qtip-light'
    });

    $('div.prooflastmessageview').qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('messageview') // Use href attribute as URL
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
        style: 'art_lastmessage'
    });

    $("div.proof_includ_dat").click(function(){
        var mailid=$(this).data('proofid');
        proof_include(mailid);
    })
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
    var url=main_proofurl+"/change_status";
    $.post(url, {'quest_id':mailid, 'type':'proof'}, function(response){
        if (response.errors=='') {
            $("#artModal").find('div.modal-dialog').css('width','565px');
            $("#artModalLabel").empty().html('Lead Assign');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").modal('show');
            /* Activate close */
            $("select#lead_id").select2({
                minimumInputLength: 3, // only start searching when the user has input 3 or more characters
                dropdownParent: $('#artModal')
            });
            /* Change Lead data */
            $("select#lead_id").change(function(){
                change_leaddata();
            })
            $("a.savequest").click(function(){
                update_queststatus();
            })
            $("div.leads_addnew").click(function(){
                create_leadproof();
            })
        } else {
            show_error(response);
        }
    }, 'json');
    return false;
}

function change_leaddata() {
    var lead_id=$("#lead_id").val();
    var url=main_proofurl+"/change_leadrelation";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            $("div#artModal div.leaddate").empty().html(response.data.lead_date);
            $("div#artModal div.leadcustomer").empty().html(response.data.lead_customer);
            $("div#artModal div.leadcustommail").empty().html(response.data.lead_mail);
        } else {
            show_error(response);
        }
    }, 'json')
}

function update_queststatus() {
    var url=main_proofurl+"/savequeststatus";
    var dat=$("form#msgstatus").serializeArray();
    $.post(url, dat, function(response){
        if (response.errors=='') {
            // disablePopup();
            $("#artModal").modal('hide');
            initProofPagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function create_leadproof() {
    var mail_id=$("input#mail_id").val();
    var type='Proof';
    var leademail_id=$("input#leademail_id").val();
    var url=main_proofurl+"/create_leadmessage";
    $.post(url, {'mail_id':mail_id, 'type':type,'leadmail_id':leademail_id}, function(response){
        if (response.errors=='') {
            $("#artModal").modal('hide');
            // POPUP
            // Add Brand
            show_new_lead(response.data.leadid, 'proof');
        } else {
            show_error(response);
        }
    }, 'json');
}

function profedit_lead(lead_id) {
    var url="/leadmanagement/edit_lead";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            // POPUP
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','970px');
            $("#pageModal").modal('show');
            $("select#lead_item").select2({
                dropdownParent: $('#pageModal')
            }).on("change", function(e){
                var newid = $(this).val();
                var url=mainurl+"/lead_itemchange"
                // var item_id=$("select#lead_item").val();
                $.post(url, {'item_id': newid}, function(response){
                    if (response.errors=='') {
                        // $("input#lead_item").val(response.data.item_name);
                        if (response.data.other==1) {
                            $("div.item_otheritemarea").show();
                            $("div.item_otheritem_label").empty().html(response.data.other_label);
                        } else {
                            $("div.item_otheritemarea").hide();
                            $("textarea#other_item_name").val('');
                        }
                    } else {
                        show_error(response);
                    }
                }, 'json');

            });
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

function edit_note(mailid) {
    var url=main_proofurl+"/proof_openartnote";
    $.post(url, {'mail_id':mailid}, function(response){
        if (response.errors=='') {
            $("#artModalLabel").empty().html(response.data.title);
            $("#artModal").find('div.modal-dialog').css('width','569px');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").modal('show');
            $("div#artModal div.saveordernote").click(function(){
                save_proofnote();
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_proofnote() {
    var mail_id=$("input#order_id").val();
    var art_note=$("#art_note").val();
    var url=main_proofurl+"/proof_saveartnote";
    $.post(url, {'mail_id':mail_id,'art_note':art_note}, function(response){
        if(response.errors=='') {
            $("#artModal").modal('hide');
            initProofPagination();
        } else {
            show_error(response);
        }
    }, 'json');
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
