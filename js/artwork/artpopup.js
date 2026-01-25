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
    $("div.artpopup_save").click(function(){
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
    init_message();
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
function init_message() {}
function init_commondata() {}
function init_templateview() {}
function init_locations() {
    
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
function init_proofs() {}
function init_approved() {}

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