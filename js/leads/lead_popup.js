var mainurl = '/leadmanagement';
function show_new_lead(lead_id,type, brand) {
    var url=mainurl+"/edit_lead";
    params = new Array();
    params.push({name: 'lead_id', value :lead_id});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadformModalLabel").empty().html(response.data.title);
            $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
            $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#leadformModal").modal({backdrop: 'static', keyboard: false, show: true});
            // Init clone email
            init_lead_cloneemail();
            init_leadpopupedit();
            // Save Button
            $("a.saveleaddat").unbind('dblclick');
            $("a.saveleaddat").unbind('click').click(function(){
                $("#loader").show();
                save_lead();
                $("#loader").hide();
                if (type=='quote') {
                    initQuotesPagination();
                } else if (type=='question') {
                    initQuestionPagination();
                } else if (type=='customquote') {
                    initCustomFormPagination();
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
            // $("#pageModalLabel").empty().html(response.data.title);
            // $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            // $("#pageModal").find('div.modal-dialog').css('width','970px');
            // $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            // init_leadpopupedit();
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
/* Edit Lead */
function edit_lead(lead_id) {
    // var lead_id=obj.id.substr(7);
    var url=mainurl+"/edit_lead";
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

function init_leadpopupedit() {
    var lead_id=$("input#lead_id").val();
    if (lead_id==0) {
        $("input#lead_company").focus();
    }
    $("img.leadcalendbtn").datepicker({
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (e) {
        $("#lead_needby").val(e.format(0,"mm/dd/yyyy"));
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
            $("div.lead_history").removeClass('expandhistory');
        } else {
            $("div.item_otheritemarea").hide();
            $("div.lead_history").addClass('expandhistory');
        }
    }
    $("select#lead_item").searchable();
    $("select#lead_item").unbind('change').change(function(){
        lead_itemchange($("select#lead_item").val());
    })

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
    // Attachments
    $("div.lead_attach_view").unbind('click').click(function () {
        var link = $(this).data('link');
        window.open(link, 'attachwin', 'width=600, height=800,toolbar=1')
    });
    // Delete attachment
    $("div.lead_attach_remove").unbind('click').click(function () {
        if (confirm('Remove attachment ?')==true) {
            var attachid = $(this).data('attachid');
            delete_lead_attachment(attachid);
        }
    });

    if ($("#addleadattachment").length > 0) {
        var qq_template= '<div class="qq-uploader"><div class="btn-addfile qq-upload-button">'+
            '+ Add Attachment</div>' +
            '<ul class="qq-upload-list"></ul>' +
            '<ul class="qq-upload-drop-area"></ul>'+
            '<div class="clear"></div></div>';


        var uploader = new qq.FileUploader({
            element: document.getElementById('addleadattachment'),
            action: '/utils/save_leadattach',
            template: qq_template,
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG','pdf','PDF','ai','AI','psd','PSD','eps','EPS'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    // $("li.qq-upload-success").hide();
                    $("ul.qq-upload-list").css('display','none');
                    var params=new Array();
                    params.push({name: 'session_id', value: $("#session_attach").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'src', value: responseJSON.source});
                    var url=mainurl+"/lead_attachment_add";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".lead_popup_attachs").empty().html(response.data.content);
                            init_leadpopupedit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
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
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $('#artNextModal').on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("div.leadreplicadddatasave").unbind('click').click(function(){
                    addnewleaderpl();
                });
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add new Custom Quote
    $(".quotesaddnew").unbind('click').click(function () {
        addnewcustomquote();
    });
}

function delete_lead_attachment(attachid) {
    var url=mainurl+"/lead_attachment_delete";
    var params=new Array();
    params.push({name: 'session_id', value: $("#session_attach").val()});
    params.push({name: 'attach_id', value: attachid});
    $.post(url,params, function (response) {
        if (response.errors=='') {
            $(".lead_popup_attachs").empty().html(response.data.content);
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    },'json');
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
        var url=mainurl+"/lead_addrep_save";
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
        dat.push({name: 'session_attach', value: $("#session_attach").val()});
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
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
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
            $("#leadformModalLabel").empty().html(response.data.title);
            $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
            $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
            init_lead_cloneemail();
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    }, 'json')
}


function lead_itemchange(item_id) {
    var url=mainurl+"/lead_itemchange"
    $.post(url, {'item_id':item_id}, function(response){
        if (response.errors=='') {
            if (response.data.other==1) {
                $("div.item_otheritemarea").show();
                $("div.item_otheritem_label").empty().html(response.data.other_label);
                $("div.lead_history").removeClass('expandhistory');
            } else {
                $("div.item_otheritemarea").hide();
                $("textarea#other_item_name").val('');
                $("div.lead_history").addClass('expandhistory');
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
   dat.push({name: 'session_attach', value: $("#session_attach").val()});
   var lead_number=$("div.lead_popup_number").text();
   if (confirm("Are you sure you want to duplicate "+lead_number+" ?")==true) {
       var url=mainurl+"/dublicatelead";
       $.post(url, dat, function(response){
            if (response.errors=='') {
                // $("#pageModal").modal('hide');
                initLeaddataPagination();
                $("#leadformModalLabel").empty().html(response.data.title);
                $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
                $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
                init_lead_cloneemail();
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
   dat.push({name: 'session_attach', value: $("#session_attach").val()});
   var url=mainurl+"/save_lead";
   $.post(url, dat, function(response){
       if (response.errors=='') {
           initLeaddataPagination();
           initProofPagination();
           $("#loader").hide();
           $("#leadformModal").modal('hide');
       } else {
           if (option==true) {
                $("input[type=checkbox]").attr('disabled',true);
           }
           $("#loader").hide();
           show_error(response);
       }
   }, 'json');
}

function init_lead_cloneemail() {

    var copyTextareaBtn = document.querySelector('.lead_popup_mailclone');

    copyTextareaBtn.addEventListener('click', function(event) {
        var copyTextarea = document.querySelector('.js-copytextarea');
        copyTextarea.focus();
        copyTextarea.select();

        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            alert('Email address copied to clipboard');
        } catch (err) {
            console.log('Oops, unable to copy');
        }
    });

}

function addnewcustomquote() {
    var lead_num=$("div.lead_popup_number").text();
    var msg="You will now save the updates of the "+lead_num+" by creating the quote.  Ok?";
    if (confirm(msg)==true) {
        var url=mainurl+"/lead_addquote";
        var dat=$("form#leadeditform").serializeArray();
        // var dat = new Array();
        dat.push({name:'lead_item_id', value: $("select#lead_item").val()});
        dat.push({name:'session_id', value: $("#session").val()});
        dat.push({name: 'session_attach', value: $("#session_attach").val()});
        dat.push({name: 'lead_type', value: $("#lead_type").val()});
        $.post(url, dat, function (response) {
            if (response.errors=='') {
                $("#quotepopupdetails").empty().html(response.data.quotecontent);
                $("#quotepopupdetails").show();
                $(".quotepopupclose").show();
            } else {
                show_error(response);
            }
        },'json');
    }
}