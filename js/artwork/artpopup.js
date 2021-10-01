/* Order Popup */
function order_artstage(order_id, callpage, brand) {
    var url="/leadorder/leadorder_change";
    var params = {order: order_id, 'page': callpage, 'edit': 0, 'brand': brand};
    $.post(url, params, function(response){
        if (response.errors=='') {
            // show_popup('popup_area');
            $(".popover").popover('hide');
            $("#artModalLabel").empty().html(response.data.header);
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','1004px');
            $("#artModal").find('div.modal-footer').html('<input type="hidden" id="root_call_page" value="'+callpage+'"/><input type="hidden" id="root_brand" value="'+brand+'"/>');
            $("#artModal").modal({backdrop: 'static', keyboard: false, show: true});
            if (parseInt(order_id)==0) {
                init_onlineleadorder_edit();
            } else {
                navigation_init();
            }
        } else {
            show_error(response);
        }
    },'json');
}

/* Proof Request Art Popup */
function artproof_lead(mailid, callpage) {
    //mailid=mailid.substr(7);
    /* ART POPUP */
    var url="/art/proof_artdata";
    $.post(url,{'proof_id':mailid, 'callpage': callpage},function(response){
        if (response.errors==='') {
            $(".popover").popover('hide');
            $("#artModalLabel").empty().html('Artwork Edit');
            $("#artModal").find('div.modal-body').empty().html(response.data.content);
            $("#artModal").find('div.modal-dialog').css('width','928px');
            $("#artModal").modal({keyboard: false, show: true}); // {backdrop: 'static', keyboard: false, show: true}
            // $("div#pop_content").empty().html(response.data.content);
            /* SAVE, EMAIL, etc buttons */
            init_popupcontent();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function init_popupcontent() {
    /* $("div.artpopup_content").jqTran */
    // $("a#popupContactClose").click(function(){
    //    disablePopup();
    // })
    jQuery.balloon.init();
    $("div.artpopup_save").click(function(){
        save_art();
    })
    init_message();
    init_commondata();
    /* Save */
    init_templateview();
    /* Init Proofs */
    init_proofs();
    init_approved();
    /* Parsed Alert click */
    $("div.proofparsed_alert").click(function(){
        show_parsed_data();
    })
    init_locations();
    $("div#artworkdataadd").click(function(){
        var artwork=$(this).data('artworkid');
        var art_type=$("select.artdataadd").val();
        add_location(artwork,art_type);
    });
    // Init History
    $("div.artpopup_historydatalabel.active").unbind('click').click(function(){
        show_art_history();
    })
}
/* ------------ OLD FUNCTIONS  ----- */
/* Work with Left part of POPUP form */
function init_message() {
    /* Truncate user message */
    /*var txtheigh = $('div#artpopup_custominstucttxt').css('height');
    txtheigh=parseInt(txtheigh);
    var lheigh=$('div#artpopup_custominstucttxt').css('line-height');
    lheigh=parseInt(lheigh);
    var showheigh=lheigh*6;
    if (showheigh<txtheigh) {
        var perc = parseInt(showheigh/txtheigh*100);
        $('div#artpopup_custominstucttxt').truncate({
            charLength:perc,
            perOT:true,
            minTrail:10,
            moreTitle: "...more",
            lessTitle: "hide extra",
            speed:2000});
    }*/
    $("textarea#customer_instruct").change(function(){
        common_update('customer_instruct',$("textarea#customer_instruct").val());
    });
    /* Change Update Message */
    $("textarea.artupdate").change(function(){
        common_update('update_msg',$("textarea.artupdate").val());
    })
}
/* Fix COMMON Data fields in SESSION */
function common_update(fldname,value) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name:'field', value: fldname});
    params.push({name:'value', value: value});
    var url="/artproofrequest/art_commonupdate";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.artpopup_save").show();
        } else {
            show_error(response);
        }

    }, 'json');
}
/* Change Popup COMMON DATA Area */
function init_commondata() {

    // $("select#order_item_id").searchable();
    /* Show other data */
    var itemid=parseInt($("select#order_item_id").val());
    if (itemid<1) {
        $("div.artpopup_otheritemarea").show();
    } else {
        $("div.artpopup_otheritemarea").hide();
    }
    /* Change Art Other */
    $("input#other_item").change(function(){
        common_update('other_item',$("input#other_item").val());
    })
    $("select#order_item_id").change(function(){
        itemnum_change();
    })
    /* Change Customer Name */
    $("input#customer_name").change(function(){
        common_update('customer_name',$("input#customer_name").val());
    })
    /* Contact */
    $("input#order_contact").change(function(){
        common_update('contact',$("input#order_contact").val());
    })
    /* Phone */
    $("input#order_phone").change(function(){
        common_update('customer_phone',$("input#order_phone").val());
    })
    /* Customer email */
    $("input#customer_email").change(function(){
        common_update('customer_email',$("input#customer_email").val());
    })
    /* Order Notes */
    $("textarea.order_notes").change(function(){
        common_update('notes',$("textarea.order_notes").val());
    })
    /* Rush Check */
    $("input#rushval").change(function(){
        var rush=0;
        if ($("input#rushval").prop('checked')==true) {
            rush=1;
        }
        common_update('rush', rush);
        if (rush==1) {
            $('div.artworksarea').each(function(){
                var logoid=$(this).data('artworkartid');
                /* get redraw value */
                if ($("input.artredraw[data-artworkartid="+logoid+"]").prop('checked')==true) {
                    change_location('rush', rush, logoid);
                    $("input.artrush[data-artworkartid="+logoid+"]").prop('checked',true);
                }
            })
        }
    });
    /* Blank check */
    $("input#blankval").change(function(){
        var blank=0;
        if ($(this).prop('checked')==true) {
            blank=1;
        }
        common_update('blank', blank);
    })
    /* Item Color */
    $("input#order_colors").change(function(){
        common_update('item_color', $("input#order_colors").val());
    })
    /* Assign Order */
    $("div#assign_order").click(function(){
        var artid=$(this).data('artworkid');
        assign_order(artid);
    })
    /* Show Parsed Message Body */
    /*$("div.parsedproofrequest").click(function(){
        var history_id=$(this).data('arthistoryid');
        show_parsedbody(history_id);
    })*/
}

/* Show Available Orders */
function assign_order(artid) {
    var url="/artproofrequest/art_assignord";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'artwork_id', value: artid});
    $.post(url, params, function(response){
        if (response.errors == '') {
            $("#artNextModal").find('div.modal-dialog').css('width','722px');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModalLabel").empty().html('Assign Order');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("div.orderdata").click(function () {
                var order_id = $(this).data('orderid');
                var ordernum = $(this).find('div.orderassign_num').text();
                assignorder(order_id, ordernum);
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Save choice of assigned order */
function assignorder(order_id, ordernum) {
    if (confirm('Connect Art to Order # '+ordernum+" ?")) {        
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name: 'order_id', value:order_id});
        params.push({name:'order_num', value:ordernum});
        var url="/artproofrequest/art_newassign";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").modal('hide');
                $("div#assign_order").empty().html(ordernum);
                $("div#assign_order").unbind('click');
                $("div.artpopup_save").show();
            } else {
                show_error(response);
            }
        }, 'json')
    }

}

/* Change Item Name */
function itemnum_change() {
    var url="/artproofrequest/art_itemchange"
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'item_id', value: $("select#order_item_id").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("input#order_itemnum").val(response.data.item_number);
            // $("input#order_items").val(response.data.item_name);
            if (response.data.other_show==1) {
                $("div.artpopup_otheritemarea").show();
                $("div.artpopup_otheritemarea div.artpopup_itemlabel").empty().html(response.data.other_label);
            } else {
                $("div.artpopup_otheritemarea").hide();
            }
            if (parseInt(response.data.imprints)!=0) {
                for (var key in response.data.imprselect) {
                    var val = response.data.imprselect[key];
                    $("div.artworkoption_locselect[data-artworkartid="+key+"]").empty().html(val)
                }
                init_locations();
            }
        } else {
            show_error(response);
        }
    }, 'json');
}


/* Init TEMPLATE VIEW Management */
function init_templateview() {
    /* Templates */
    $("div.artpopup_templview").click(function(){
        var artid=$("input#artwork_id").val();
        show_templates(artid);
    })
    $("div.empty_template").click(function(){
        var imgurl="/uploads/aitemp/proof_BT15000_customer_item.ai";
        openai(imgurl,'proof_BT15000_customer_item.ai');
    })
    /* Show Item AI */
    $("div.item_template").click(function(){
        var itemid=$("select#order_item_id").val();
        if (itemid=='') {
            alert('Please select an item first.  Your changes cannot be saved until you do this.')
        } else if(parseInt(itemid)<1) {
            var artid=$("input#artwork_id").val();
            show_templates(artid);
        } else {
            var url="/artproofrequest/art_showtemplate";
            $.post(url, {'item_id':itemid}, function(response){
                if (response.errors=='') {
                    openai(response.data.fileurl, response.data.filename);
                    // window.open(response.data.fileurl, 'itemtemplate', 'width=300,height=200,toolbar=0')
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    });
}
/* Show search templates select */
function show_templates(artid) {
    var url="/artproofrequest/art_showtemplates";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name:'artwork_id', value: artid});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','640px');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            // $("div#popupwin").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Init proofs Management */
function init_proofs() {
    /* $("div.addproof").unbind('click').click(function(){
        var artid=$(this).data('artworkid');
        add_proofs(artid);
    }); */
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background: none;"><img src="/img/artpage/artpopup_add_btn.png" alt="Add Proof"/></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('uploadproofdoc'),
        action: '/artproofrequest/proofattach',
        uploadButtonText: '',
        multiple: false,
        debug: false,
        template: upload_templ,
        params: {
            'artwork_id': $("#uploadproofdoc").data("artworkid")
        },
        allowedExtensions: ['pdf','PDF'],
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                var artwork_id = $("#uploadproofdoc").data("artworkid");
                var url="/artproofrequest/art_saveproofload";
                var params=new Array();
                params.push({name: 'artsession', value: $("input#artsession").val()});
                params.push({name: 'proofdoc', value: responseJSON.filename});
                params.push({name: 'sourcename', value: responseJSON.srcname});
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $("div#proofarea"+artwork_id).empty().html(response.data.content);
                        init_proofs();
                    } else {
                        show_error(response);
                    }
                },'json');
            } else {
                alert(responseJSON.error);
                $("div#loader").hide();
                $("div.qq-upload-button").css('visibility','visible');
            }
        }
    });

    $("div.proofnotapproved").unbind('click').click(function(){
        var proofid=$(this).data('proofid');
        var artid=$(this).data('artworkid');
        approve_proof(proofid, artid);
    })
    $("div.artpopup_maildat").unbind('click').click(function(){
        var artid=$(this).data('artworkid');
        approve_mail(artid);
    })
    $("div.removeproof").unbind('click').click(function(){
        var artid=$(this).data('artworkid');
        var proofid=$(this).data('proofid');
        delproof(proofid, artid);
    })
    $("div.artpopup_proofname").click(function(){
        var proof=$(this).data('proofid');
        show_proof(proof);
    })
    $("div.artpopup_proofname").popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        content: 'title'
    });
    $("div.artpopup_proofsend").popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        content: 'title'
    });

}
/* Delete proof */
function delproof(proof_id, art_id) {
    var proofname=$("div#proofarea"+art_id+" div.artpopup_proofname[data-proofid="+proof_id+"]").text();
    if (confirm('Delete Proof '+proofname+'?')) {
        var url="/artproofrequest/art_approveddelete";
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name:'artwork_id', value: art_id});
        params.push({name: 'proof_id', value :proof_id});        
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#approvedarea"+art_id).empty().html(response.data.content);
                $("div#proofarea"+art_id).empty().html(response.data.proof_content);
                init_proofs();
                init_approved();
            } else {
                show_error(response);
            }
        }, 'json' )
    }



}

/* Approve proof */
function approve_proof(proofid, artworkid) {
    /* Name */
    var proofname=$("div.artpopup_proofname[data-proofid="+proofid+"]").text();
    if (confirm('Aprrove '+proofname+'?')) {
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name: 'artwork_id', value:artworkid});
        params.push({name: 'proof_id', value :proofid});
        var url="/artproofrequest/art_aproveproof";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#proofarea"+artworkid).empty().html(response.data.proofcontent);
                $("div#approvedarea"+artworkid).empty().html(response.data.approvecontent);
                init_proofs();
                init_approved();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}
/* Send email to Request Proof Approve */
function approve_mail(artid) {
    /* Calc that exist checked */
    var numsend=$("div#proofarea"+artid+" input.artproofdatasend:checked").length;
    if (numsend==0) {
        alert('Check Proofs for Sending');
    } else {
        var email_template=$("input#email_template_id").val();        
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name: 'email_template', value :email_template});
        var url="/artproofrequest/art_approvemail";
        $.post(url, params , function(response){
            if (response.errors=='') {
                $("#artNextModal").find('div.modal-dialog').css('width', '388px');
                $("#artNextModalLabel").empty().html('Send Proof Message');
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});

                // $("div#popupwin").empty().html(response.data.content);
                $("div.addbccapprove").click(function(){
                    var bcctype=$(this).data('applybcc');
                    if (bcctype=='hidden') {
                        $(this).data('applybcc','show').empty().html('hide bcc');
                        $("div#emailbccdata").show();
                        $("textarea.aprovemail_message").css('height','222');
                    } else {
                        $(this).data('applybcc','hidden').empty().html('add bcc');
                        $("div#emailbccdata").hide();
                        $("textarea.aprovemail_message").css('height','241');
                    }
                });
                $("div.approvemail_send").click(function(){
                    send_approvemail();
                })
            } else {
                show_error(response);
            }
        }, 'json');
    }
}
/* Seand Approved Email */
function send_approvemail() {
    var artwork=$("input#artwork_id").val();
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});    
    params.push({name:'artwork_id',value:$("input#artwork_id").val()});
    params.push({name:'from',value: $("input#approvemail_from").val()});
    params.push({name:'customer',value:$("input#approvemail_to").val()});
    params.push({name:'subject',value:$("input#approvemail_subj").val()});
    params.push({name:'message', value:$("textarea.aprovemail_message").val()});
    var bcctype=$("div.addbccapprove").data('applybcc');
    var bccmail='';
    if (bcctype=='show') {
        bccmail=$("input#approvemail_copy").val();
    }
    params.push({name:'cc', value:bccmail});
    var proofs='';
    var num=0;
    $("div#proofarea"+artwork+" input.artproofdatasend:checked").each(function(){
        var proofid=$(this).data('proofid');
        proofs=proofs+proofid+"|";
        num++;
    })
    params.push({name:'proofs',value:proofs});
    params.push({name:'numproofs',value:num});
    var url="/artproofrequest/art_sendproofs";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            reinit_artworkpopup();
        } else {
            show_error(response);
        }
    }, 'json');
    /* */
}

/* Approved DATA Management */
function init_approved() {
    /* Delete Approved  */
    $("div.delapproved").click(function(){
        var artid=$(this).data('artworkid');
        var profid=$(this).data('proofid');
        delapproved(profid, artid);
    })
    /* Show Approved */
    $("div.artpopup_approvedname").click(function(){
        var proof=$(this).data('proofid');
        show_proof(proof);
    });
    $("div.artpopup_approvedname").popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        content: 'title'
    });
}

/* Delete approved */
function delapproved(profid, artid) {
    var proofname=$("div#approvedarea"+artid+" div.artpopup_approvedname[data-proofid="+profid+"]").text();
    if (confirm('Revert Approved '+proofname+'?')) {
        // { }
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name: 'artwork_id', value :artid});
        params.push({name: 'proof_id', value :profid});        
        var url="/artproofrequest/art_approvedrevert";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#approvedarea"+artid).empty().html(response.data.content);
                $("div#proofarea"+artid).empty().html(response.data.proof_content);
                init_proofs();
                init_approved();
            } else {
                show_error(response);
            }
        }, 'json' )
    }

}
/* Show content of Proof Doc */
function show_proof(proof) {    
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'proof_id', value :proof});
    var url="/artproofrequest/art_approvedshow";
    $.post(url,params,function(response){
        if (response.errors=='') {
            $.fileDownload('/artproofrequest/art_openimg', {httpMethod : "POST", data: {url : response.data.url, file: response.data.filename}});
            return false; //this is critical to stop the click event which will trigger a normal file download!            return false; //this is critical to stop the click event which will trigger a normal file download!
            window.open(response.data.url, 'showfile');
        } else {
            show_error(response);
        }
    },'json');
}

/* PARSED email Label click */
function show_parsed_data() {
    var url="/art/proof_parsedview";
    $.post(url,{},function(response){
        if (response.errors=='') {
            $("div.proofparsed_alert").hide();
            $("div.proofparsed_data").show().empty().html(response.data.content);
            $("div.artpopup_locationsarea").empty().html(response.data.artlocs);
            /* Unlock Items for Fill */
            $("input.order_customer").prop('readonly',false);
            $("input.order_contact").prop('readonly',false);
            $("input.order_phone").prop('readonly',false);
            $("input.order_email").prop('readonly',false);
            $("input.order_itemname").prop('readonly',false);
            $("textarea.order_notes").prop('readonly',false);
            $("input.order_itemnum").prop('readonly',false);
            $("input.order_colors").prop('readonly',false);
            $("div.parced_cancel").click(function(){
                disablePopup();
            })
            $("div.parced_save").click(function(){
                save_parsed();
            })
            init_locations();
            $("div#artworkdataadd").click(function(){
                var artwork=$(this).data('artworkid');
                var art_type=$("select.artdataadd").val();
                add_location(artwork,art_type);
            })
        } else {
            show_error(response);
        }
    },'json')
}

/* Save parsed data */
function save_parsed() {
    var url="/art/proofparsed_save";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            disablePopup();
            /* Check current page */
            var curtab;
            if ($("ul.tabNavigation a.selected").length!=0) {
                curtab=$("ul.tabNavigation a.selected").prop('id');
            } else {
                curtab=$("ul.tabNavigation a.selectedleft").prop('id');
            }

            if (curtab=='taskviewlnk') {
                init_tasks_page();
            } else if (curtab=='requestlistlnk') {
                initProofPagination();
            } else if (curtab=='orderlistlnk') {
                initArtPagination();
            } else if (curtab=='onlineprooflnk') {
                initProofPagination();
            } else if (curtab=='leadsorderslnk') {
                search_leadorders();
            }
        } else {
            show_error(response);
        }
    }, 'json');
}
function save_art() {
    var dat=$("form#artdetailsform").serializeArray();    
    var url="/artproofrequest/artwork_save";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            $("#artModal").modal('hide');
            /* Check current page */
            var callpage = response.data.callpage;
            if (callpage=='artprooflist') {
                initProofPagination();
            }
        } else {
            show_error(response);
        }
    }, 'json')
}

/* ------------ END OLD FUNCTIONS  ----- */
/* Add Location */
function add_location(artwork,art_type) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'artwork_id', value :artwork});
    params.push({name: 'art_type', value :art_type});    
    var url="/artproofrequest/art_newlocation";
    $.post(url, params, function(response){
        if (response.errors=='') {
            // show_popup1('logoupload');
            if (art_type=='Logo' || art_type=='Reference') {
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").find('.modal-title').empty().html('New Logo Location');
                $("#artNextModal").find('.modal-dialog').css('width','305px');
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_artlogoupload();
                $("div.artlogouploadsave_data").click(function(){
                    save_newlogoartloc(art_type);
                });
            } else if(art_type=='Text') {
                $("div.artpopup_locations").append(response.data.content);
                init_locations();
            } else {
                // Copy
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").find('.modal-title').empty().html('New Repeat Location');
                $("#artNextModal").find('.modal-dialog').css('width','305px');
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("div.orderarchive_save").click(function(){
                    var order_num=$("input#archiveord").val();
                    var artwork_id=$("input#newartid").val();
                    if (order_id!='') {
                        save_newcopy(artwork_id, order_num);
                    } else {
                        alert('Enter Order Number');
                    }
                });
            }
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Uploader for Logo files */
function init_artlogoupload() {

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: ['jpg','gif', 'jpeg', 'pdf', 'ai', 'eps','doc', 'docx', 'png'],
        action: '/artproofrequest/art_redrawattach',
        multiple: false,
        debug: false,
        uploadButtonText:'',
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success) {
                var url="/artproofrequest/art_newartupload";
                $("ul.qq-upload-list").css('display','none');
                // $.post(url, {'filename':responseJSON.filename,'doc_name':fileName}, function(response){
                $.post(url, {'filename':responseJSON.uplsource,'doc_name':fileName}, function(response){
                    if (response.errors=='') {
                        $("#orderattachlists").empty().html(response.data.content);
                        $("#file-uploader").hide();
                        $("div.artlogouploadsave_data").show();
                        $("div.delvectofile").click(function(){
                            $("#orderattachlists").empty();
                            $("#file-uploader").show();
                            $("div.artlogouploadsave_data").hide();
                        })
                    } else {
                        alert(response.errors);
                        if(response.data.url !== undefined) {
                            window.location.href=response.data.url;
                        }
                    }
                }, 'json');
            }
        }
    });
}

function save_newlogoartloc(art_type) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'artwork_id', value: $("input#newartid").val()});
    params.push({name:'logo', value:$("input#filename").val()});
    params.push({name:'art_type', value: art_type});    
    var url="/artproofrequest/art_addlocation";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.artpopup_locations").append(response.data.content);
            $("#artNextModal").modal('hide');
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
}
/* Send art  for copy artwork locations */
function save_newcopy(artwork_id,order_num) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'artwork_id', value :artwork_id});
    params.push({name: 'repeat_text', value :order_num});
    params.push({name: 'art_type', value :'Repeat'});
    var url="/artproofrequest/art_addlocation";
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("div.artpopup_locations").append(response.data.content);
            $("#artNextModal").modal('hide');
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');

}

/* Init Location Management */
function init_locations() {
    $("div.artworksource").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        var file_type = 'redraw';
        show_file(art_id, file_type);
    })
    $("div.artworkvector").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        var file_type = 'vectored';
        show_file(art_id, file_type);
    });
    $("div.artworkdelete").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        var artname = $("div.artworklabel[data-artworkartid=" + art_id + "]").text();
        if (confirm('Delete Artwork ' + artname + '?')) {
            delete_art(art_id, artname);
        }
    })
    $("input.artfont").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        var value = $(this).val();
        change_font(value, art_id);
    });
    $("div.artworkusrtxt").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        change_usertxt(art_id);
    })
    $("input.artredraw").unbind('change').change(function () {
        var art_id = $(this).data('artworkartid');
        var redraw = 0;
        if ($(this).prop('checked') == true) {
            redraw = 1;
        }
        change_redraw(art_id, redraw);
    });
    $("input.artrush").unbind('change').change(function () {
        var art_id = $(this).data('artworkartid');
        var rushval = 0;
        if ($(this).prop('checked') == true) {
            rushval = 1;
        }
        change_location('rush', rushval, art_id);
    })
    $("input.artundo").unbind('change').change(function () {
        var art_id = $(this).data('artworkartid');
        var undoval = 0;
        if ($(this).prop('checked') == true) {
            undoval = 1;
        }
        change_location('redo', undoval, art_id);
    })
    $("div.artworkrdrnote").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        edit_rdnote(art_id);
    })
    $("select.artnumcolors").unbind('change').change(function () {
        var art_id = $(this).data('artworkartid');
        var numcolors = $(this).val();
        edit_artnumcolors(art_id, numcolors);
    })
    $("div.artworkoption_color_choice").unbind('click').click(function () {
        var art_id = $(this).data('artworkartid');
        var color_num = $(this).data('colornum');
        edit_color(art_id, color_num);
    });
    $("select.artworkoption_location").unbind('change').change(function () {
        var art_id = $(this).data('artworkartid');
        var location = $(this).val();
        edit_imprintval(art_id, location);
    });
    $("div.artworkusrtxt img").popover({
        html: true,
        trigger: 'hover',
        placement: 'left'
    });
    $("div.artworkrdrnote img").popover({
        html: true,
        trigger: 'hover',
        placement: 'left'
    });
    $("div.artworkoption_color_choice").popover({
        html: true,
        trigger: 'hover',
        placement: 'left'
    });
    // $("div.artworksource.viewsource").qtip({
    //     content: {
    //         text: function(event, api) {
    //             $.ajax({
    //                 url: api.elements.target.data('viewsrc') // Use href attribute as URL
    //             }).then(function(content) {
    //                 // Set the tooltip content upon successful retrieval
    //                 api.set('content.text', content);
    //             }, function(xhr, status, error) {
    //                 // Upon failure... set the tooltip content to error
    //                 api.set('content.text', status + ': ' + error);
    //             });
    //             return 'Loading...'; // Set some initial text
    //         }
    //     },
    //     // style: 'art_lastmessage'
    // });
}
function change_usertxt(art_id) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value :art_id});
    var url="/artproofrequest/art_changeusrtxt";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('Edit Text Location');
            $("#artNextModal").find('.modal-dialog').css('width','470px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("div.vectorsave_data").show();
            $("div.vectorsave_data").click(function(){
                save_usertext(art_id);
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_usertext(art_id) {
    var usrtxt=$("textarea.artworkusertext").val();
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value: art_id});
    params.push({name: 'customer_text', value: usrtxt});    
    var url="/artproofrequest/art_saveusertext";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.artworkusrtxt[data-artworkartid="+art_id+"]").empty().html(response.data.content).prop('title',usrtxt);
            $("#artNextModal").modal('hide');
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Font Popup */
function change_font(value, art_id) {
    var url="/artproofrequest/art_fontselect";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('Select Font');
            $("#artNextModal").find('.modal-dialog').css('width','1010px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            // $("div.imprintfonts").jqTransform();
            $("div#popupwin input.fontmanual").change(function(){
                var fontval=$(this).val();
                $("input#fontselectfor").val(fontval);
                $("div.font_button_select").addClass('active');
            })
            $("input.fontoption").click(function(){
                var fontval=$(this).val();
                $("input#fontselectfor").val(fontval);
                $("div.font_button_select").addClass('active');
            })
            /* Init Management */
            $("div.font_button_select").click(function(){
                var fontval=$("input#fontselectfor").val();
                $("input.artfont[data-artworkartid="+art_id+"]").val(fontval);
                $("#artNextModal").modal('hide');
                change_location('font',fontval,art_id);
            })
            // active
        } else {

        }
    }, 'json')
}
function change_location(locitem, value, art_id) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'field', value : locitem});
    params.push({name: 'value', value: value});
    params.push({name: 'art_id', value: art_id});
    // {'field':locitem,'value':value,'art_id':art_id}
    var url="/artproofrequest/art_locationupdate";
    $.post(url, params , function(response){
        if (response.errors=='') {
            if (locitem=='redo') {
                if (value==1) {
                    $("div.artworkdata[data-artworkartid="+art_id+"]").removeClass('redrawn').addClass('source');
                } else {
                    $("div.artworkdata[data-artworkartid="+art_id+"]").removeClass('source').addClass('redrawn');
                }
            }
        } else {
            show_error(response);
        }
    }, 'json');
}

/* Open window with file */
function show_file(art_id, file_type) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value: art_id});
    params.push({name: 'type', value: file_type});
    var url="/artproofrequest/art_showfile";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $.fileDownload('/artproofrequest/art_openimg', {httpMethod : "POST", data: {url : response.data.url, file: response.data.filename}});
            return false; //this is critical to stop the click event which will trigger a normal file download!            return false; //this is critical to stop the click event which will trigger a normal file download!
            window.open(response.data.url, 'showfile');
        } else {
            show_error(response);
        }
    }, 'json');
}
/* Delete Art Location */
function delete_art(art_id) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value:art_id});    
    var url="/artproofrequest/art_dellocation";
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("div.artworksarea[data-artworkartid="+art_id+"]").remove();
        } else {
            show_error(response);
        }
    }, 'json');
}
/* Edit REDRAWN NOTES */
function edit_rdnote(art_id) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value: art_id});
    params.push({name: 'mode', value: 'edit'});
    // 
    var url='/artproofrequest/art_rdnoteview';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('Redraw Note');
            $("#artNextModal").find('.modal-dialog').css('width','459px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("div#popupwin").empty().html(response.data.content);
            $("div.vectorsave_data").show();
            $("div.vectorsave_data").click(function(){
                save_rdnote(art_id);
            });
        } else {
            show_error(response);
        }
    }, 'json')
}

function save_rdnote(art_id) {
    var rdnote=$("textarea.artworkusertext").val();    
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value: art_id});
    params.push({name: 'redraw_message', value: rdnote});
    var url='/artproofrequest/art_rdnotesave';
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.artworkrdrnote[data-artworkartid="+art_id+"]").empty().html(response.data.content);
            $("#artNextModal").modal('hide');
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json')

}

function edit_artnumcolors(art_id, numcolors) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value: art_id});
    params.push({name: 'numcolors', value: numcolors});
    var url="/artproofrequest/art_savenumcolors";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.artworkoption_color_choices[data-artworkartid="+art_id+"]").empty().html(response.data.content);
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
}

/* show colors */
function edit_color(art_id, color_num) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value :art_id});
    params.push({name: 'color_num', value :color_num});
    var url="/artproofrequest/art_colorchoice";
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('Edit Color');
            $("#artNextModal").find('.modal-dialog').css('width','1004px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            // show_popup1('colorchoicearea');
            // $("div#popupwin").empty().html(response.data.content);
            /* save color choice */
            // $("div.imprintcolors_table").jqTransform();
            $(".colorradio").click(function(){
                var colorval=$(this).attr('id').substr(5);
                colorval="#"+colorval;
                if (this.id=='color2') {
                    $("#usrcolor").attr('readonly',false);
                } else {
                    $("#usrcolor").attr('readonly',true);
                    $("input#userchkcolor").val(colorval)
                }

                $("a#select_color").attr('disabled', false).addClass('active').click(function(){
                    var usercolor=$("input#userchkcolor").val();
                    save_artwork_color(art_id, color_num, usercolor);
                });
            });
        } else {
            show_error(response);
        }
    }, 'json')
}

function save_artwork_color(art_id, color_num, usercolor) {
    var url="/artproofrequest/art_savecolor";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value :art_id});
    params.push({name: 'color_num', value :color_num});
    params.push({name: 'color_code', value :usercolor});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("div.artworkoption_color_choices[data-artworkartid="+art_id+"]").empty().html(response.data.content);
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');

}

function edit_imprintval(art_id, location) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'field', value : 'art_location'});
    params.push({name: 'value', value: location});
    params.push({name: 'art_id', value: art_id});
    var url="/artproofrequest/art_locationupdate";
    $.post(url, params, function(response){
        if (response.errors=='') {
        } else {
            show_error(response);
        }
    },'json');
}

function change_redraw(art_id, redraw) {
    var url="/artproofrequest/art_redrawupd";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name:'art_id', value :art_id});
    params.push({name: 'redraw', value :redraw});    
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.artworkdata[data-artworkartid="+art_id+"]").removeClass('redrawn').removeClass('source_alert').removeClass('source').addClass(response.data.newclass);
        } else {
            show_error(response);
        }
    }, 'json');
}

function reinit_artworkpopup() {
    var dat=$("form#artdetailsform").serializeArray();
    var url="/artproofrequest/artwork_save";
    $("#loader").show();
    $.post(url, dat, function(response){
        if (response.errors=='') {
            // clearTimeout(blinktimer);
            var ordersrc=$("input#order_id").val();
            var order_id=0;
            if (ordersrc!='') {
                order_id=parseInt(ordersrc);
            }
            var params=new Array();
            if (order_id!=0) {
                url="/art/order_artdata";
                params.push({name:'order_id',value:order_id});
            } else {
                url="/art/proof_artdata";
                params.push({name:'proof_id',value:$("input#proof_id").val()});
            }
            $.post(url,params,function(resp){
                if (resp.errors=='') {
                    $("div#artModal").find('div.modal-body').empty().html(resp.data.content);
                    init_popupcontent();
                    $("#loader").hide();
                } else {
                    $("#loader").hide();
                    show_error(resp);
                }
            },'json');
        } else {
            show_error(response);
        }
    },'json');
}

function show_art_history() {
    var url="/artproofrequest/artwork_history";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});    
    $.post(url, params,function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('View History');
            $("#artNextModal").find('.modal-dialog').css('width','395px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
        } else {
            show_error(response);
        }
    },'json');
}