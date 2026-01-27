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
                init_rushpast();
                if (parseInt($("#ordermapuse").val())==1) {
                    // Init simple Shipping address
                    initShipOrderAutocomplete();
                    if ($("#billorder_line1").length > 0) {
                        initBillOrderAutocomplete();
                    }
                }
            } else {
                if (parseInt(response.data.cancelorder)===1) {
                    $("#artModal").find('div.modal-header').addClass('cancelorder');
                } else {
                    $("#artModal").find('div.modal-header').removeClass('cancelorder');
                }
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
            $("#proofRequestModalLabel").empty().html('PROOF REQUEST');
            $("#proofRequestModal").find('div.modal-body').empty().html(response.data.content);
            $("#proofRequestModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* SAVE, EMAIL, etc buttons */
            init_popupcontent();
        } else {
            show_error(response);
        }
    },'json');
    return false;
}

function init_popupcontent() {
    // Close Popup
    $("div.leadorderclose").unbind('click').click(function (){
        $("#proofRequestModal").modal('hide');
    });
    // Save
    $(".pr-btnsave").unbind('click').click(function(){
        save_art();
    });
    // Scrolls
    if (parseInt($("#locationtotal").val()) > 0) {
        new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
    }
    if (parseInt($("#proofdoctotal").val()) > 0) {
        new SimpleBar(document.getElementById('proofreqproofdocs_table'), { autoHide: false });
    }
    if (parseInt($("#appovdoctotal").val()) > 0) {
        new SimpleBar(document.getElementById('proofreqapprovdocs_table'), { autoHide: false });
    }
    // Item Select
    $("select#proofrequestitem").select2({
        dropdownParent: $('#proofRequestModal'),
        matcher: matchStart,
    });

    init_commondata();
    init_templateview();
    /* Init Proofs */
    init_proofs();
    init_approved();
    /* Parsed Alert click */
    // $("div.proofparsed_alert").click(function(){
    //     show_parsed_data();
    // })
    init_locations();
    $(".newartwork-text").click(function(){
        var artwork=$(this).data('artwork');
        var art_type=$("select.artlocationaadd").val();
        add_location(artwork,art_type);
    });
    // Init History
    $(".viewhistory.active").unbind('click').click(function(){
        show_art_history();
    })
}

function save_art() {
    // var dat=$("form#artdetailsform").serializeArray();
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    var url="/artproofrequest/artwork_save";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#proofRequestModal").modal('hide');
            /* Check current page */
            var callpage = response.data.callpage;
            if (callpage=='artprooflist') {
                initProofPagination();
            } else if (callpage=='leadsview') {
                init_proofrequest_interest();
            }
        } else {
            show_error(response);
        }
    }, 'json')
}

function add_location(artwork,art_type) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'artwork_id', value :artwork});
    params.push({name: 'art_type', value :art_type});
    var url="/artproofrequest/art_newlocation";
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (art_type=='Logo') {
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").find('.modal-title').empty().html('New Logo Location');
                $("#artNextModal").find('.modal-dialog').css('width','305px');
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                })
                $("#artNextModal").css('z-index',2000);
                init_logoupload();
                $("div.artlogouploadsave_data").click(function(){
                    save_newlogoartloc(art_type);
                });
            } else if(art_type=='Text') {
                $("#proofreqlocation_table").empty().html(response.data.content);
                $("#locationtotal").val(response.data.locationtotal);
                if (parseInt($("#locationtotal").val()) > 0) {
                    new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
                }
                init_locations();
            } else if(art_type=='Reference') {
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").find('.modal-title').empty().html('Select / Upload Reference Logo');
                $("#artNextModal").find('.modal-dialog').css('width','365px');
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                });
                $("#artNextModal").css('z-index',2000);
                init_artlogoupload();
                init_referenceslogo_manage();
            } else {
                // Copy
                $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                $("#artNextModal").find('.modal-title').empty().html('New Repeat Location');
                $("#artNextModal").find('.modal-dialog').css('width','305px');
                $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#artNextModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                });
                $("#artNextModal").css('z-index',2000);
                $("div.orderarchive_save").unbind('click').click(function(){
                    var order_num=$("input#archiveord").val();
                    var artwork_id=$("input#newartid").val();
                    if (order_num!='') {
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

function init_commondata() {
    // Order Notes, Instruction, Update Message
    $("textarea.proofreqcommon").unbind('change').change(function (){
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name:'field', value: $(this).data('fld')});
        params.push({name:'value', value: $(this).val()});
        var url="/artproofrequest/art_commonupdate";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // $("div.artpopup_save").show();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Item change
    $("select#proofrequestitem").unbind('change').change(function (){
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name:'item_id', value: $(this).val()});
        var url="/artproofrequest/art_itemchange";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input[name='other_item']").val(response.data.other_label);
                if (parseInt(response.data.other_show)==1) {
                    $("input[name='other_item']").addClass('active');
                    $("input[name='other_item']").focus();
                } else {
                    $("input[name='other_item']").removeClass('active');
                }
                var newtxt = $("#proofrequestitem option:selected").text();
                $("#select2-proofrequestitem-container").empty().html(newtxt);
                // Locations
                $("#proofreqlocation_table").empty().html(response.data.content);
                init_locations();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Other comon info
    $("input.proofreqcommon").unbind('change').change(function (){
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name:'field', value: $(this).data('fld')});
        params.push({name:'value', value: $(this).val()});
        var url="/artproofrequest/art_commonupdate";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // $("div.artpopup_save").show();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    /* Show other data */
    /* Assign Order */
    $("div.pr-orderboxconnect").click(function(){
        assign_order();
    })
}
function init_templateview() {
    // Empty Template
    $(".templates-linkbox.emptytemplate").unbind('click').click(function(){
        var imgurl = $(this).data('link');
        var title = $(this).data('title');
        openai(imgurl, title);
    })

    // Item Template
    $("div.itemtemplate").unbind('click').click(function(){
        var itemid=$("select#proofrequestitem").val();
        if (itemid=='') {
            alert('Please select an item first.  Your changes cannot be saved until you do this.')
        } else if(parseInt(itemid)<1) {
            show_templates();
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

    $("div.searchitemtemplate").click(function(){
        show_templates();
    })
    /* Show Item AI */
}

function show_templates() {
    var url="/artproofrequest/art_showtemplates";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-dialog').css('width','640px');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("#artNextModal").css('z-index',2000);
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_locations() {
    // Show source
    $("div.artw-srclogo").unbind('click').click(function () {
        var url = $(this).data('link');
        if (url!='') {
            var filename = $(this).data('file');
            $.fileDownload('/artproofrequest/art_openimg', {httpMethod : "POST", data: {url : url, file: filename}});
            return false; //this is critical to stop the click event which will trigger a normal file download!            return false; //this is critical to stop the click event which will trigger a normal file download!
            window.open(url, 'showfile');
        }
    })
    // Delete location
    $("div.artw-btndelete").unbind('click').click(function () {
        var art_id = $(this).data('art');
        var artname = $(this).data('locname');
        if (confirm('Delete Artwork ' + artname + '?')) {
            var params=new Array();
            params.push({name: 'artsession', value: $("input#artsession").val()});
            params.push({name: 'art_id', value:art_id});
            var url="/artproofrequest/art_dellocation";
            $.post(url, params , function(response){
                if (response.errors=='') {
                    $("#proofreqlocation_table").empty().html(response.data.content);
                    $("#locationtotal").val(response.data.locationtotal);
                    if (parseInt($("#locationtotal").val()) > 0) {
                        new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
                    }
                    init_locations();
                } else {
                    show_error(response);
                }
            }, 'json');
        }
    })
    // Change Font
    $("input.proofreqestfont").unbind('click').click(function () {
        var art_id = $(this).data('art');
        var value = $(this).val();
        change_font(value, art_id);
    });
    // Check box - redraw, redo, rush
    $("input[type=checkbox].proofreqestlocation").unbind('change').change(function (){
        var art_id = $(this).data('art');
        var fld = $(this).data('fld');
        var newval = 0;
        if ($(this).prop('checked')==true) {
            newval = 1;
        }
        change_location(fld, newval, art_id);
    });
    // Customer text
    $("div.artw-srcfont-icn").unbind('click').click(function () {
        var art_id = $(this).data('art');
        change_usertxt(art_id, 'customer_text');
    })
    // Redo notes
    $("span.artw-rdrnotes-icon").unbind('change').change(function () {
        var art_id = $(this).data('art');
        change_usertxt(art_id, 'redraw_message');
    });
    // Num colors
    $("select.proofreqestlocation").unbind('change').change(function () {
        var art_id = $(this).data('art');
        var newval = $(this).val();
        var locitem = $(this).data('fld')
        change_location(locitem, newval, art_id)
    })
    // Color
    $("div.artwoptions-colorbox").unbind('click').click(function () {
        var art_id = $(this).data('art');
        var color_num = $(this).data('fld');
        edit_color(art_id, color_num);
    });
    // Repeat order
    $("input[type=text].proofreqestlocation").unbind('change').change(function (){
        var art_id = $(this).data('art');
        var newval = $(this).val();
        var locitem = $(this).data('fld')
        change_location(locitem, newval, art_id)
    });
}

function change_font(value, art_id) {
    var url="/artproofrequest/art_fontselect";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('Select Font');
            $("#artNextModal").find('.modal-dialog').css('width','1010px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("#artNextModal").css('z-index',2000);
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
            show_error(response);
        }
    }, 'json')
}

function change_usertxt(art_id, locitem) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value :art_id});
    params.push({name: 'locitem', value: locitem});
    var url="/artproofrequest/art_changeusrtxt";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModal").find('.modal-title').empty().html('Edit Text Location');
            $("#artNextModal").find('.modal-dialog').css('width','470px');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            });
            $("#artNextModal").css('z-index',2000);
            $("div.vectorsave_data").show();
            $("div.vectorsave_data").click(function(){
                save_usertext(art_id, locitem);
            });
        } else {
            show_error(response);
        }
    }, 'json');
}

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
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("#artNextModal").css('z-index', 2000);
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
function change_location(locitem, value, art_id) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'field', value : locitem});
    params.push({name: 'value', value: value});
    params.push({name: 'art_id', value: art_id});
    var url="/artproofrequest/art_locationupdate";
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("#proofreqlocation_table").empty().html(response.data.content);
            $("#locationtotal").val(response.data.locationtotal);
            if (parseInt($("#locationtotal").val()) > 0) {
                new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
            }
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_usertext(art_id, locitem) {
    var usrtxt=$("textarea.artworkusertext").val();
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'art_id', value: art_id});
    params.push({name: 'newval', value: usrtxt});
    params.push({name: 'field', value: locitem});
    var url="/artproofrequest/art_saveusertext";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("#proofreqlocation_table").empty().html(response.data.content);
            if (parseInt($("#locationtotal").val()) > 0) {
                new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
            }
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
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
            $("#proofreqlocation_table").empty().html(response.data.content);
            if (parseInt($("#locationtotal").val()) > 0) {
                new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
            }
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');

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
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            })
            $("#artNextModal").css('z-index', 2000);
        } else {
            show_error(response);
        }
    },'json');
}
function init_proofs() {
    // Upload
    var upload_templ= '<div class="qq-uploader"><div class="custom_upload qq-upload-button" style="background: none;"><div class="prproofs-btnadd">add</div></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('uploadproofdoc'),
        action: '/artproofrequest/proofattach',
        uploadButtonText: '',
        multiple: true,
        debug: false,
        template: upload_templ,
        params: {
            'artwork_id': $("#uploadproofdoc").data("art")
        },
        allowedExtensions: ['pdf','PDF'],
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success==true) {
                var url="/artproofrequest/art_saveproofload";
                var params=new Array();
                params.push({name: 'artsession', value: $("input#artsession").val()});
                params.push({name: 'proofdoc', value: responseJSON.filename});
                params.push({name: 'sourcename', value: responseJSON.srcname});
                $.post(url, params, function (response) {
                    if (response.errors=='') {
                        $("#proofdoctotal").val(response.data.proofdoctotal);
                        $("#proofreqproofdocs_table").empty().html(response.data.content);
                        new SimpleBar(document.getElementById('proofreqproofdocs_table'), { autoHide: false });
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

    $("div.approveflag").unbind('click').click(function(){
        if ($(this).hasClass('approved')) {
        } else {
            var proofid=$(this).data('art');
            var proofname=$("div.proofdocname[data-art="+proofid+"]").data('title');
            if (confirm('Aprrove '+proofname+'?')) {
                var params=new Array();
                params.push({name: 'artsession', value: $("input#artsession").val()});
                params.push({name: 'proof_id', value :proofid});
                var url="/artproofrequest/art_aproveproof";
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $("#proofdoctotal").val(response.data.proofdoctotal);
                        $("#appovdoctotal").val(response.data.appovdoctotal);
                        $("#proofreqproofdocs_table").empty().html(response.data.proofcontent);
                        $("#proofreqapprovdocs_table").empty().html(response.data.approvecontent);
                        new SimpleBar(document.getElementById('proofreqproofdocs_table'), { autoHide: false });
                        new SimpleBar(document.getElementById('proofreqapprovdocs_table'), { autoHide: false });
                        init_proofs();
                        init_approved();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            }
        }
    })
    $("div.prproofs-btnemail").unbind('click').click(function(){
        var numsend=$("input.proofdocsinpt:checked").length;
        if (numsend==0) {
            alert('Check Proofs for Sending');
        } else {
            var params=new Array();
            params.push({name: 'artsession', value: $("input#artsession").val()});
            var url="/artproofrequest/art_approvemail";
            $.post(url, params , function(response){
                if (response.errors=='') {
                    $("#artNextModal").find('div.modal-dialog').css('width', '388px');
                    $("#artNextModalLabel").empty().html('Send Proof Message');
                    $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
                    $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
                    $("#artNextModal").on('hidden.bs.modal', function (e) {
                        $(document.body).addClass('modal-open');
                    })
                    $("#artNextModal").css('z-index',2000);
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
    })
    $("div.proofdocremove").unbind('click').click(function(){
        var proofid=$(this).data('art');
        var proofname=$("div.proofdocname[data-art="+proofid+"]").data('title');
        if (confirm('Delete Proof '+proofname+'?')) {
            var url="/artproofrequest/art_approveddelete";
            var params=new Array();
            params.push({name: 'artsession', value: $("input#artsession").val()});
            params.push({name: 'proof_id', value : proofid});
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#proofdoctotal").val(response.data.proofdoctotal);
                    $("#appovdoctotal").val(response.data.appovdoctotal);
                    $("#proofreqproofdocs_table").empty().html(response.data.proof_content);
                    $("#proofreqapprovdocs_table").empty().html(response.data.content);
                    if (parseInt($("#proofdoctotal").val()) > 0) {
                        new SimpleBar(document.getElementById('proofreqproofdocs_table'), { autoHide: false });
                    }
                    if (parseInt($("#appovdoctotal").val()) > 0) {
                        new SimpleBar(document.getElementById('proofreqapprovdocs_table'), { autoHide: false });
                    }
                    init_proofs();
                    init_approved();
                } else {
                    show_error(response);
                }
            }, 'json' )

        }
    })
    $("div.proofdocname").click(function(){
        var proof=$(this).data('art');
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
    });
    // $("div.artpopup_proofname").popover({
    //     html: true,
    //     trigger: 'hover',
    //     placement: 'left',
    //     content: 'title'
    // });
    // $("div.artpopup_proofsend").popover({
    //     html: true,
    //     trigger: 'hover',
    //     placement: 'left',
    //     content: 'title'
    // });
}
function init_approved() {
    // Revert approve
    $("div.approveddocremove").unbind('click').click(function (){
        var profid = $(this).data('art');
        var proofdoc = $(this).data('title');
        if (confirm('Revert Approvement '+proofdoc+'?')) {
            var params=new Array();
            params.push({name: 'artsession', value: $("input#artsession").val()});
            params.push({name: 'proof_id', value :profid});
            var url="/artproofrequest/art_approvedrevert";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#proofdoctotal").val(response.data.proofdoctotal);
                    $("#appovdoctotal").val(response.data.appovdoctotal);
                    $("#proofreqproofdocs_table").empty().html(response.data.proof_content);
                    $("#proofreqapprovdocs_table").empty().html(response.data.content);
                    if (parseInt($("#proofdoctotal").val()) > 0) {
                        new SimpleBar(document.getElementById('proofreqproofdocs_table'), { autoHide: false });
                    }
                    if (parseInt($("#appovdoctotal").val()) > 0) {
                        new SimpleBar(document.getElementById('proofreqapprovdocs_table'), { autoHide: false });
                    }
                    init_proofs();
                    init_approved();
                } else {
                    show_error(response);
                }
            }, 'json' )
        }
    });
    // Show document
    $(".approveddocname").unbind('click').click(function(){
        var proof=$(this).data('art');
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
            $("#artNextModal").modal('hide');
            $("#locationtotal").val(response.data.locationtotal);
            $("#proofreqlocation_table").empty().html(response.data.content);
            if (parseInt($("#locationtotal").val()) > 0) {
                new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
            }
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
}

function save_newcopy(artwork_id, order_num) {
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    params.push({name: 'artwork_id', value :artwork_id});
    params.push({name: 'repeat_text', value :order_num});
    params.push({name: 'art_type', value :'Repeat'});
    var url="/artproofrequest/art_addlocation";
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("#artNextModal").modal('hide');
            $("#locationtotal").val(response.data.locationtotal);
            $("#proofreqlocation_table").empty().html(response.data.content);
            if (parseInt($("#locationtotal").val()) > 0) {
                new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
            }
            init_locations();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_logoupload() {
    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: ['jpg','gif', 'jpeg', 'pdf', 'ai', 'eps','doc', 'docx', 'png'],
        action: '/artproofrequest/art_redrawattach',
        multiple: true,
        debug: false,
        uploadButtonText:'',
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success) {
                var params=new Array();
                params.push({name: 'artsession', value: $("input#artsession").val()});
                params.push({name: 'artwork_id', value: $("input#newartid").val()});
                params.push({name:'art_type', value: $("#newlogotype").val()});
                params.push({name: 'logo', value: responseJSON.uplsource});
                var url = '/artproofrequest/art_addlocation';
                $.post(url, params, function(response) {
                    if (response.errors=='') {
                        $("#artNextModal").modal('hide');
                        $("#proofreqlocation_table").empty().html(response.data.content);
                        init_locations();
                    } else {
                        show_error(response);
                    }
                },'json');
            }
        }
    });
}

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

function init_referenceslogo_manage() {
    $(".refattachview").unbind('click').click(function(){
        var link = $(this).data('link');
        window.open(link, 'attachwin', 'width=600, height=800,toolbar=1')
    });
    $(".reflogouploadsave_data").unbind('click').click(function(){
        // if ($("input.attachcurlogo:checked").length > 0) {
        var logostr = '';
        $("input.attachcurlogo:checked").each(function(){
            logostr=logostr+$(this).data('logoid')+'-';
        })
        var params=new Array();
        params.push({name: 'artsession', value: $("input#artsession").val()});
        params.push({name: 'artwork_id', value: $("input#newartid").val()});
        params.push({name:'logo', value: logostr });
        params.push({name:'art_type', value: 'Reference'});
        params.push({name:'uploadlogo', value:$("input#filename").val()});
        var url="/artproofrequest/art_addlocation";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#artNextModal").modal('hide');
                $("#locationtotal").val(response.data.locationtotal);
                $("#proofreqlocation_table").empty().html(response.data.content);
                if (parseInt($("#locationtotal").val()) > 0) {
                    new SimpleBar(document.getElementById('proofreqlocation_table'), { autoHide: false });
                }
                init_locations();
            } else {
                show_error(response);
            }
        }, 'json');
    })
}

// Assign Order - prepare
function assign_order() {
    var url="/artproofrequest/art_assignord";
    var params=new Array();
    params.push({name: 'artsession', value: $("input#artsession").val()});
    $.post(url, params, function(response){
        if (response.errors == '') {
            $("#artNextModal").find('div.modal-dialog').css('width','722px');
            $("#artNextModal").find('div.modal-body').empty().html(response.data.content);
            $("#artNextModalLabel").empty().html('Assign Order');
            $("#artNextModal").modal({backdrop: 'static', keyboard: false, show: true});
            $("#artNextModal").on('hidden.bs.modal', function (e) {
                $(document.body).addClass('modal-open');
            });
            $("#artNextModal").css('z-index',2000);
            new SimpleBar(document.getElementById('orderassigndata_info'), { autoHide: false });
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
                $(".pr-orderboxconnect").empty().html(ordernum).removeClass('pr-orderboxconnect').addClass('pr-orderbox');
            } else {
                show_error(response);
            }
        }, 'json')
    }

}
/* Send email on approve */
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
    var proofid = '';
    $("input.proofdocsinpt:checked").each(function(){
        proofid=$(this).data('art');
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
}

function reinit_artworkpopup() {
    var nparams = new Array();
    nparams.push({name: 'artsession', value: $("input#artsession").val()});
    var url="/artproofrequest/artwork_save";
    $("#loader").show();
    $.post(url, nparams, function(response){
        if (response.errors=='') {
            var order_id = parseInt(response.data.order_id);
            var email_id = parseInt(response.data.email_id);
            var params=new Array();
            if (order_id!=0) {
                url="/art/order_artdata";
                params.push({name:'order_id',value:order_id});
            } else {
                url="/art/proof_artdata";
                params.push({name:'proof_id',value: email_id});
            }
            $.post(url,params,function(resp){
                if (resp.errors=='') {
                    $("#proofRequestModal").find('div.modal-body').empty().html(resp.data.content);
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