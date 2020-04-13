var mainurl = '/leadmanagement';
function show_new_lead(lead_id,type) {
    var url=mainurl+"/edit_lead";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            // disablePopup();
            // show_popup('leadpopupdat');
            // $("div#pop_content").empty().html(response.data.content);
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','970px');
            $("#pageModal").modal('show');
            $("select#lead_item").select2({
                minimumInputLength: 3, // only start searching when the user has input 3 or more characters
                dropdownParent: $('#pageModal')
            });
            init_leadpopupedit();
            // $("a#popupContactClose").unbind('click').click(function(){
            //     disablePopup();
            //     if (type=='quote') {
            //         initQuotesPagination();
            //     } else if (type=='question') {
            //         initQuestionPagination();
            //     } else {
            //         initProofPagination();
            //     }
            // });
            /* Save Button */
            
            $("a.saveleaddat").unbind('dblclick');
            $("a.saveleaddat").unbind('click').click(function(){
                $("#loader").show();
                save_lead();
                $("#loader").hide();
                if (type=='quote') {
                    initQuotesPagination();
                } else if (type=='question') {
                    initQuestionPagination();
                } else {
                    initProofPagination();
                }
            });
        } else {
            show_error(response);
        }
    }, 'json');
}
/* New lead */
function add_lead(brand) {
    var lead_id=0;
    var url=mainurl+"/edit_lead";
    $.post(url, {'lead_id':lead_id, brand: brand}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','970px');
            $("#pageModal").modal('show');
            $("select#lead_item").select2({
                minimumInputLength: 3, // only start searching when the user has input 3 or more characters
                dropdownParent: $('#pageModal')
            });
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    }, 'json');
}
/* Edit Lead */
function edit_lead(lead_id) {
    // var lead_id=obj.id.substr(7);
    var url=mainurl+"/edit_lead";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','970px');
            $("#pageModal").modal('show');
            $("select#lead_item").select2({
                dropdownParent: $('#pageModal'),
                minimumInputLength: 3
            }).on("change", function(e){
                var newid = $(this).val();
                var url=mainurl+"/lead_itemchange"
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

function init_leadpopupedit() {
    var lead_id=$("input#lead_id").val();
    if (lead_id==0) {
        $("input#lead_company").focus();
    }
    $("img.leadcalendbtn").datepicker({
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (e) {
        $("#lead_needby").val(e.format(0,"dd/mm/yyyy"));
    });
    /* Check box other */
    $("#other_task").unbind('change').change(function(){
        var chk=$("#other_task").prop('checked');
        if (chk==false) {
            $("textarea#other").attr('readonly',true);
        } else {
            $("textarea#other").attr('readonly',false);
        }
    })

    var itemid=$("select#lead_item").val();
    if (itemid!='') {
        if (parseInt(itemid)<1) {
            $("div.item_otheritemarea").show();
        } else {
            $("div.item_otheritemarea").hide();
        }
    }
    // $("select#lead_item").searchable();
    /*$("select#lead_item").unbind('change').change(function(){
        lead_itemchange();
    }) */

    $("input.usrrepliccheck").unbind('change').change(function(){
        var value=1;
        if ($(this).prop('checked')==true) {
            value=0;
        }
        if ($(this).prop('readonly')==true) {
            if (value==1) {
                $(this).prop('checked',true);
            } else {
                $(this).prop('checked',false);
            }
        }
    })
    /* Question */
    $("div.lead_popup_questchck").unbind('click').click(function(){
        show_questdetails(this);
    });
    $("div.lead_popup_proofchck").unbind('click').click(function(){
        show_proofdetails(this);
    })
    $("div.lead_popup_quotechck").unbind('click').click(function(){
        show_quotedetails(this);
    })
    /* Duplicate Lead */
    $("div.lead_duplicate").unbind('click').click(function(){
        duplicatelead();
    })
    /* Save Button */
    $("a.saveleaddat").unbind('click').click(function(){
        save_lead();
    })
    $("div.lead_popup_addrequest img").unbind('click').click(function(){
        add_proofrequest();
    });
    $("div.lead_quoteprofnum").unbind('click').click(function(){
        var mail_id=$(this).data('leadid');
        var lead_id=$("input#lead_id").val();
        var dat=$("form#leadeditform").serializeArray();
        dat.push({name:'session_id', value: $("#session").val()});
        var url=mainurl+"/lead_proofrequest";
        $.post(url, dat, function(response){
            if (response.errors=='') {
                show_artdata(mail_id, lead_id,'old');
            } else {
                show_error(response);
            }
        }, 'json');
    })
    $("div.proofed").unbind('click').click(function(){
        var mail_id=$(this).data('leadid');
        var url=mainurl+"/lead_approvedshow";
        $.post(url,{'email_id':mail_id},function(response){
            if (response.errors=='') {
                var data=response.data.approved;
                for (var key in data) {
                    var val = data[key]['proof_name'];
                    var filename=data[key]['file_name'];
                    if (val) {
                        $.fileDownload('/art/art_openimg', {httpMethod : "POST", data: {url : val, file: filename}});
                    }
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Manage Replics
    $("div.lead_popup_replicausrchk").unbind('click').click(function(){
        var user=$(this).data('user');
        var replic=$(this).data('replic');
        if (confirm('Remove '+replic+' from Lead Reps ?')==true) {
            var data=new Array();
            data.push({name: 'user', value: user});
            data.push({name: 'session_id', value: $("#session").val()});
            var url=mainurl+"/lead_remove_rep";
            $.post(url,data, function(response){
                if (response.errors=='') {
                    $("div.lead_popup_replicas").empty().html(response.data.content);
                    init_leadpopupedit();
                } else {
                    show_error(response);
                }
            },'json')
        }
    });
    $("div.lead_popup_addreplica").unbind('click').click(function(){
        // editmail_form        
        var url=mainurl+"/lead_addrep_view";
        $.post(url, {'session_id':$("#session").val()}, function(response){
            if (response.errors=='') {
                $("#artNextModalLabel").empty().html('Add Lead Rep');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").find('div.modal-dialog').css('width','570px');
                $("#artNextModal").modal('show');
                $("div.leadreplicadddatasave").unbind('click').click(function(){
                    addnewleaderpl();
                });
            } else {
                show_error(response);
            }
        },'json');
    });
}

function addnewleaderpl() {    
    var params=new Array();
    var numpp=0;
    $("input.newuserreplchk[type=checkbox]").each(function(){
        if ($(this).prop('checked')==true) {
            numpp++;
            params.push({name: 'user'+numpp, value: $(this).val()});            
        }        
    });    
    if (numpp==0) {
        $("#artNextModal").modal('hide');
    } else {
        params.push({name:'session_id', value: $("#session").val()});
        var url=mainurl+"lead_addrep_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.lead_popup_replicas").empty().html(response.data.content);
                $("#artNextModal").modal('hide');
                init_leadpopupedit();
            } else {
                show_error(response);
            }
        },'json');
    }
    
}
/*
 * 
 $("input.newuserreplchk").each(function(e){
  console.log($(this).val();
});
 */
function add_proofrequest() {
    var lead_num=$("div.lead_popup_number").text();
    var msg="You will now save the updates of the "+lead_num+" by creating the proof request.  Ok?";
    if (confirm(msg)==true) {
        var url=mainurl+"/lead_addproofrequst";
        var dat=$("form#leadeditform").serializeArray();
        dat.push({name:'lead_item_id', value: $("select#lead_item").val()});
        dat.push({name:'session_id', value: $("#session").val()});
        $("#loader").show();
        $.ajax({
            url: url,
            type: "POST",
            data: dat,
            dataType: "json",
            timeout: 5000,
            success: function(response) {
                if (response.errors=='') {
                    $("#loader").hide();
                    show_artdata(response.data.email_id, response.data.lead_id,'new');
                } else {
                    $("#loader").hide();
                    alert(response.errors);
                }
            },
            error: function(x, t, m) {
                if(t==="timeout") {
                    $("#loader").hide();
                    alert('Request Time Out. Try again');
                } else {
                    $("#loader").hide();
                    alert(m);
                }
            }
        });
    }
}

// Call artdata from Lead
function show_artdata(mail_id, lead_id,relation_type) {
    var url="/art/proof_artdata";
    $.post(url,{'proof_id':mail_id},function(response){
        if (response.errors==='') {
            $("#pageModal").modal('hide');
            $("#artModalLabel").empty().html('Artwork Edit');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','928px');
            $("#artModal").modal('show');
            /* SAVE, EMAIL, etc buttons */
            init_artpopupcontent(lead_id, mail_id,relation_type);
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function init_artpopupcontent(lead_id, mail_id,relation_type) {
    init_popupcontent();
    $("#artModal").find('button.close').unbind('click').click(function(){
        if (relation_type=='new') {
            // disablePopup();
            /* Delete proof */
            var url = mainurl+"/lead_deleteproof";
            $.post(url,{'email_id':mail_id},function(response){
                if (response.errors=='') {
                    restore_leadform();
                } else {
                    show_error(response);
                }
            },'json');
        } else {
            restore_leadform();
        }

    })
    $("div.artpopup_save").unbind('click').click(function(){
        var dat=$("form#artdetailsform").serializeArray();
        var url="/artproofrequest/artwork_save";
        $.post(url, dat, function(response){
            if (response.errors=='') {
                $("#artModal").modal('hide');
                restore_leadform();
            } else {
                show_error(response);
            }
        }, 'json')
    })

}

function restore_leadform() {
    var url=mainurl+"/restore_ledform";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','970px');
            $("#pageModal").modal('show');
            $("#pageModal").find('button.close').unbind('click').click(function(){
                $("#pageModal").modal('hide');
            });
            $("select#lead_item").select2({
                minimumInputLength: 3, // only start searching when the user has input 3 or more characters
                dropdownParent: $('#pageModal')
            });
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    }, 'json')
}


function lead_itemchange(item_id) {
    var url=mainurl+"/lead_itemchange"
    // var item_id=$("select#lead_item").val();
    $.post(url, {'item_id':item_id}, function(response){
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

}

function duplicatelead() {
   // var lead_id=$("input#lead_id").val();
   var dat=$("form#leadeditform").serializeArray();
   dat.push({name:'session_id', value: $("#session").val()});
   var lead_number=$("div.lead_popup_number").text();
   if (confirm("Are you sure you want to duplicate "+lead_number+" ?")==true) {
       var url=mainurl+"/dublicatelead";
       $.post(url, dat, function(response){
            if (response.errors=='') {
                $("#pageModal").modal('hide');
                initLeaddataPagination();
                $("#pageModalLabel").empty().html(response.data.title);
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").find('div.modal-dialog').css('width','970px');
                $("#pageModal").modal('show');
                init_leadpopupedit();
            } else {
                show_error(response);
            }
       }, 'json');
   }
}

function save_lead() {
   var option=$("input[type=checkbox]").filter(":first").prop('disabled');
   $("input[type=checkbox]").attr('disabled',false);
   var dat=$("form#leadeditform").serializeArray();
   dat.push({name:'lead_item_id', value: $("select#lead_item").val()});
   dat.push({name: 'session_id', value: $("#session").val()});
   var url=mainurl+"/save_lead";
   $.post(url, dat, function(response){
       if (response.errors=='') {
           initLeaddataPagination();
           initProofPagination();
           $("#loader").hide();
           $("#pageModal").modal('hide');
           /* Read currrent tab */
            // var curtab;
            // if ($("ul.tabNavigation a.selected").length!=0) {
            //     curtab=$("ul.tabNavigation a.selected").prop('id');
            // } else {
            //     curtab=$("ul.tabNavigation a.selectedleft").prop('id');
            // }
            // if (curtab=='requestlistlnk') {
            //     initProofPagination();
            // } else if (curtab=='leadslnk') {
            //     initLeaddataPagination();
            // } else if (curtab=='onlineprooflnk') {
            //     initProofPagination();
            // }
       } else {
           if (option==true) {
                $("input[type=checkbox]").attr('disabled',true);
           }
           $("#loader").hide();
           show_error(response);
       }
   }, 'json');
}
