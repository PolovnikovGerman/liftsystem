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
            init_leadpopupcontent();
            init_quoteformcontent();
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    },'json');
}

function edit_lead(lead_id) {
    // var lead_id=obj.id.substr(7);
    var url=mainurl+"/edit_lead";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            $("#leadformModalLabel").empty().html(response.data.title);
            $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
            $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#leadformModal").modal({backdrop: 'static', keyboard: false, show: true});
            // init_lead_cloneemail();
            init_leadpopupcontent();
            init_quoteformcontent();
            init_leadpopupedit();
            if (parseInt($("#leadmapuse").val())==1) {
                initCustomerAddressAutocomplete();
            }
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_leadpopupcontent(){
    $("select#lead_item").select2({
        dropdownParent: $('#leadformModal'),
        matcher: matchStart,
    });
    $("input#lead_needby").datepicker({
        autoclose: true,
        todayHighlight: true
    });
    // Attachment
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button btn-attach">+ add attachment</div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('btn-attach'),
        action: '/utils/save_leadattach',
        uploadButtonText: '',
        multiple: true,
        debug: false,
        template: upload_templ,
        params: {
        },
        allowedExtensions: [],
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                $(".qq-upload-list").hide();
                var url= mainurl+'/lead_attachment_add';
                var params=new Array();
                params.push({name: 'lead', value: $("#leadeditid").val()});
                params.push({name: 'attachdoc', value: responseJSON.filename});
                params.push({name: 'sourcename', value: responseJSON.source});
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $(".list-attachfiles").empty().html(response.data.content);
                        init_leadpopupedit();
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

}

function init_quoteformcontent() {
    $("select.quoteform-locations[data-location='1']").removeClass('inactive');
    if ($("#quoteformcustomitem").val()==1) {
        $("select.quoteform-locations[data-location='1']").val(5);
    } else {
        $("select.quoteform-locations[data-location='1']").val(1);
    }
}
function init_leadpopupedit() {
    // Input change
    $("input.leadmainedit").unbind('change').change(function (){
        var field_name = $(this).data('fld');
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: field_name});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (field_name=='lead_needby') {
                    $("input#lead_needby").val(response.data.newdate);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Select Changes
    $("select.leadmainedit").unbind('change').change(function (){
        var params = new Array();
        var field_name = $(this).data('fld');
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: field_name});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // TextArea Changes
    $("textarea.leadmainedit").unbind('change').change(function(){
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Address Change
    $("input.leadaddressedit").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_address_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (parseInt(response.data.zipchange)==1) {
                    $("input.leadaddressedit[data-fld='city']").val(response.data.city);
                    $(".leadaddressedit[data-fld='state']").val(response.data.state);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.leadaddressedit").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_address_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (field_name=='country_id') {
                    $("#lead_address_states").empty().html(response.data.states_view);
                }
                init_leadpopupedit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Item Change
    $(".leadpopupitem").unbind('change').change(function (){
        var field_name = 'lead_item_id';
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: field_name});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                var newtxt = $("#lead_item option:selected").text();
                $("#select2-lead_item-container").empty().html(newtxt);
                if (parseInt(response.data.show_custom)==1) {
                    $("div.lead-descr").addClass('active');
                } else {
                    $("div.lead-descr").removeClass('active');
                }
                $("textarea[data-fld='other_item_name']").val('');
                // Re init Quote form
                $(".lead-quotesform").empty().html(response.data.quote_form);
                init_quoteformcontent();
                init_leadpopupedit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Contacts change
    $("input.leadcontactedit").unbind('change').change(function (){
        var params = new Array();
        var fld = $(this).data('fld');
        var contact = $(this).data('contact');
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'contact', value: contact});
        params.push({name: 'field_name', value: fld});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_contact_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
                $("input.leadcontactedit[data-contact='"+contact+"'][data-fld='"+fld+"']").val(response.data.oldval);
            }
        },'json');
    });
    // Copy field in clipboard
    $(".dp-contactemailbtn").unbind('click').click(function (){
        var contact = $(this).data('contact');
        var element = document.querySelector(".dp-contactemail[data-contact='"+contact+"']");
        copyEmailToClipboard(element);
        $(element).show();
    });
    // Open assign popup
    $(".leadtopreps-addbtn").unbind('click').click(function(){
        $(".leadtopassign-popup").show();
        init_leadpopup_assign();
    });
    // Delete replica
    $(".repsuserbox-icn").unbind('click').click(function (){
        var leadname = $(this).data('usrname');
        if (confirm('Remove '+leadname+' from Lead Reps ?')==true) {
            var params=new Array();
            params.push({name: 'lead', value: $("#leadeditid").val()});
            params.push({name: 'leadusr', value: $(this).data('usr')});
            var url = mainurl+'/userreplica_remove_popup';
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $("#leadtopreplicacontent").empty().html(response.data.content);
                    init_leadpopupedit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Quote request change active
    $("input[name='pricecheck']").unbind('click').click(function (){
        var pricetype = $(this).val();
        $(".qtypricelist-box").removeClass('active');
        $(".qtypricelist-box").find('input.qtybox-qty').prop('readonly',true);
        $(".qtypricelist-box").find('input.qtybox-price').prop('readonly',true);
        $(".qtypricelist-box[data-price='"+pricetype+"']").addClass('active');
        $(".qtypricelist-box[data-price='"+pricetype+"']").find('input.qtybox-qty').prop('readonly',false);
        $(".qtypricelist-box[data-price='"+pricetype+"']").find('input.qtybox-price').prop('readonly',false);
    });
    // Unlock discount
    $("input[name='discountcheckbox']").unbind('click').click(function (){
        var chkdisc = 0;
        if ($(this).prop('checked')==true) {
            chkdisc = 1;
        }
        if (chkdisc==0) {
            $("div.quotesform-discount").addClass('inactive');
            $("input.discount-code").prop('readonly',true);
            $("input.discount-price").prop('readonly',true);
            $("input.discount-exp").prop('readonly',true);
        } else {
            $("div.quotesform-discount").removeClass('inactive');
            $("input.discount-code").prop('readonly', false);
            $("input.discount-price").prop('readonly', false);
            $("input.discount-exp").prop('readonly', false);
        }
    });
    // Change location value
    $("select.quoteform-locations").unbind('change').change(function (){
        var loc = $(this).data('location');
        if ($(this).val()=='0') {
            $("select.quoteform-locations[data-location='"+loc+"']").addClass('inactive');
        } else {
            $("select.quoteform-locations[data-location='"+loc+"']").removeClass('inactive');
        }
    });
    // Show - hide messages
    $("span.quoteform-expandview").unbind('click').click(function (){
        var expandview = 0;
        if ($("#quoteformexpandview").val()==0) {
            expandview = 1;
        }
        $("#quoteformexpandview").val(expandview);
        if (expandview==1) {
            $("div.messagequote-block").show();
            $("span.quoteform-expandview").empty().html('<i class="fa fa-caret-up" aria-hidden="true"></i>');
            $(".leadquotetabl-body").addClass('shortview');
            $(".btn-createquote.fixedview").hide();
        } else {
            $("div.messagequote-block").hide();
            $("span.quoteform-expandview").empty().html('<i class="fa fa-caret-down" aria-hidden="true"></i>');
            $(".leadquotetabl-body").removeClass('shortview');
            $(".btn-createquote.fixedview").show();
        }
    });
    // Add Quote
    $(".btn-createquote").unbind('click').click(function (){
        add_leadquote();
    })
    // Show Quote PDF
    $(".leadquotetabl-doc").unbind('click').click(function (){
        var quote = $(this).data('quote');
        var url = '/leadquote/quotepdfdoc';
        $.post(url, {'quote_id': quote}, function (response){
            if (response.errors=='') {
                var newWin = window.open(response.data.docurl,"Quoute PDF","width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
            } else {
                show_error(response);
            }
        },'json');
    });
    // Open Attachment
    $(".attachfile").unbind('click').click(function (){
        var docurl = $(this).data('link');
        var wintitle = $(this).data('title');
        var newWin = window.open(docurl,wintitle,"width=800,height=580,top=120,left=320,resizable=yes,scrollbars=yes,status=yes");
    })
    // Add Proof requst
    $(".btn-newproofreq").unbind('click').click(function (){
        var lead_num = $(".leadnumber").find('span').text();
        var msg="You will now save the updates of the "+lead_num+" by creating the proof request.  Ok?";
        if (confirm(msg)==true) {
            var url=mainurl+"/lead_addproofrequst";
            var params = new Array();
            params.push({name: 'lead', value: $("#leadeditid").val()});
            $("#loader").show();
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $("#loader").hide();
                    $("#leadformModal").modal('hide');
                    artproof_lead(response.data.proof_id,'leadsview');
                } else {
                    $("#loader").hide();
                    show_error(response);
                }
            },'json');
        }
    });
    $(".proofreqbox-number").unbind('click').click(function (){
        var proof = $(this).data('proof');
        // Save changes in Lead
        var url = mainurl+'/lead_popup_save';
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#loader").hide();
                $("#leadformModal").modal('hide');
                artproof_lead(proof, 'leadsview');
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    })
    // Save button
    $(".lead-savebtn").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        var url = mainurl+'/lead_popup_save';
        $("#loader").show();
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#leadformModal").modal('hide');
                initLeaddataPagination();
                // initProofPagination();
                init_customform_interest();
                $("#loader").hide();
                $("#leadformModal").modal('hide');
                // $('.modal-backdrop').hide();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
}

function copyEmailToClipboard(element) {
    $(element).show();
    $(element).focus();
    $(element).select();
    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Msg '+msg);
    } catch (err) {
        console.log('Oops, unable to copy');
    }
    $(element).hide();
}

function add_leadquote() {
    if ($("input[name=pricecheck]:checked").length==0) {
        alert('Chose Item Quote QTY');
    } else if ($("#lead_item").val()=='') {
        alert('Chose Item for Quote');
    } else {
        var leadnum = $(".leadnumber").find('span').text();
        var msg="You will now save the updates of the "+leadnum+" by creating the quote.  Ok?";
        if (confirm(msg)==true) {
            var price = 0; var qty = 0;
            var pricecheck = $("input[name=pricecheck]:checked").val();
            if (pricecheck=='custom') {
                price = $("input.qtybox-price[data-price='custom']").val();
                qty = $("input.qtybox-qty[data-price='custom']").val();
            } else {
                qty = $("div.qtypricelist-box[data-price='"+pricecheck+"']").data('promoqty');
                if ($("#quoteformcustomitem").val()==1) {
                    price = $("input.qtybox-price[data-price='"+pricecheck+"']").val();
                } else {
                    price = $("div.qtypricelist-box[data-price='"+pricecheck+"']").find('div.qtyprice-pricebox').data('promoprice');
                }
            }
            var design = 0;
            if ($("input[name='quotesform-design']").length > 0) {
                design = $("input[name='quotesform-design']").val();
            }
            var printprice = $("input[name='quotesform-prints']").val();
            var setupprice = $("input[name='quotesform-setup']").val();
            // Discount
            var discount_label = '';
            var discount_val = 0;
            var discount_exp = '';
            if ($("input[name='discountcheckbox']:checked").length > 0) {
                discount_label = $("input[name='quotesform-discount']").val();
                if (discount_label=='') {
                    discount_label = 'Courtesy Discount';
                }
                discount_val = $("input[name='quotesform-price']").val();
                discount_exp = $("input[name='quotesform-exp']").val();
            }
            var quotezip = $("input[name='quotesform-zipcode']").val();
            var other_note = $("textarea.quoteform_othernotes").val();
            var repcontact_note = $("textarea.quoteform_repcontact").val();
            var params = new Array();
            params.push({name: 'lead', value: $("#leadeditid").val()});
            params.push({name: 'promoprice', value: pricecheck});
            params.push({name: 'itemqty', value: qty});
            params.push({name: 'itemprice', value: price});
            params.push({name: 'custom_item', value: $("#quoteformcustomitem").val()});
            params.push({name: 'printprice', value: printprice});
            params.push({name: 'setupprice', value: setupprice});
            params.push({name: 'design', value: design});
            params.push({name: 'discount_label', value: discount_label});
            params.push({name: 'discount_val', value: discount_val});
            params.push({name: 'discount_exp', value: discount_exp});
            params.push({name: 'quotezip', value: quotezip});
            params.push({name: 'other_note', value: other_note});
            params.push({name: 'repcontact_note', value: repcontact_note});
            for (let i = 1; i < 13; i++) {
                if ($("select.quoteform-locations[data-location='"+i+"']").length > 0) {
                    params.push({name: 'location'+i, value: $("select.quoteform-locations[data-location='"+i+"']").val()});
                }
            }
            var url = mainurl+'/add_leadquote';
            $.post(url, params, function(response){
                if (response.errors=='') {
                    // Refresh data
                    var lurl=mainurl+"/edit_lead";
                    $.post(lurl, {'lead_id': response.data.lead_id}, function(lresponse){
                        if (lresponse.errors=='') {
                            $("#leadformModalLabel").empty().html(lresponse.data.title);
                            $("#leadformModal").find('div.modal-body').empty().html(lresponse.data.content);
                            $("#leadformModal").find('div.modal-footer').empty().html(lresponse.data.footer);
                            init_leadpopupcontent();
                            init_quoteformcontent();
                            init_leadpopupedit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                } else {
                    show_error(response);
                }
            },'json');
        }
    }
}

function init_leadpopup_assign() {
    $(".leadusrreplicacancel").unbind('click').click(function(){
        $("input[name='leadusercandidat']").prop('checked',false);
        $(".leadtopassign-popup").hide();
    });
    $(".leadusrreplicasave").unbind('click').click(function (){
        var usrrepl = new Array();
        $("input[name='leadusercandidat']:checked").each(function (index){
            usrrepl[index]=$(this).data('usr');
        });
        if (usrrepl.length==0) {
            $(".leadtopassign-popup").hide();
        } else {
            var params = new Array();
            params.push({name: 'lead', value: $("#leadeditid").val()});
            params.push({name: 'replicas', value: usrrepl});
            var url = mainurl+'/userreplica_popup';
            $.post(url, params, function (response){
                if (response.errors=='') {
                    $("#leadtopreplicacontent").empty().html(response.data.content);
                    $(".leadtopassign-popup").hide();
                    init_leadpopupedit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
}