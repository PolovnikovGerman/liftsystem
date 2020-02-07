function init_terms_view() {
    window_alignment();
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    $(".displaytermsdata").unbind('click').click(function(){
        display_terms();
    });
    $(".edit_button[data-page='terms']").unbind('click').click(function () {
        init_termspage_edit();
    });
}

function display_content() {
    if ($(".displaycontent").hasClass('show')) {
        $(".termscontent-area").hide();
        $(".displaycontent").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".termscontent-area").show();
        $(".displaycontent").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function display_terms() {
    if ($(".displaytermsdata").hasClass('show')) {
        $(".termsdata-area").hide();
        $(".displaytermsdata").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".termsdata-area").show();
        $(".displaytermsdata").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function init_termspage_edit() {
    var url = "/content/edit_termscontent";
    var params = new Array();
    params.push({name:'brand', value: $("#contentbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("#termsview").empty().html(response.data.content);
            $(".content_preview").on('click', function () {
                var url = $("#terms_previewurl").val();
                $.fancybox.open({
                    src: url,
                    type: 'iframe',
                    opts: {
                        afterShow: function (instance, current) {
                            console.info('done!');
                        }
                    }
                });
            });
            init_termspage_editcontent();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_termspage_editcontent() {
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    $(".displaytermsdata").unbind('click').click(function(){
        display_terms();
    });
    // Cancel Edit
    $(".cancel_button[data-page='terms']").unbind('click').click(function () {
        init_contentpage('terms');
    });
    // Save
    $(".save_button[data-page='terms']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name:'brand', value: $("#contentbrand").val()});
        var url="/content/save_termspagecontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('terms');
            } else {
                show_error(response);
            }
        },'json');
    });
    // Meta
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Page content
    $("input[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='terms']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'type', value: 'terms'});
        params.push({name: 'term_id', value: $(this).data('term')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Remove
    $(".termsremove").unbind('click').click(function () {
        if (confirm('Remove Term?')) {
            var params=new Array();
            params.push({name: 'session', value: $("#terms_session").val()});
            params.push({name: 'term_id', value: $(this).data('term')});
            var url="/content/remove_termsparam";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".termsdata-area").empty().html(response.data.content);
                    init_termspage_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Add new
    $(".addnewterm").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        var url="/content/add_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".termsdata-area").empty().html(response.data.content);
                init_termspage_editcontent();
            } else {
                show_error(response);
            }
        },'json');
    })
    // Edit with editor
    $(".termsedit_params").unbind('click').click(function () {
        var term=$(this).data('term');
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'term_id', value: term });
        var url="/content/edit_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".termsdatarow[data-term='"+term+"']").empty().html(response.data.content);
                $('.uEditorCustom').uEditor({
                    toolbarItems : ['bold','italic','link','unorderedlist','orderedlist','htmlsource','formatblock'],
                    stylesheet : 'uEditorContent.css',
                    containerClass : 'uEditor'
                });
                init_termspage_editcontent();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Cancel editor
    $(".termscancel_edit").unbind('click').click(function(){
        var term=$(this).data('term');
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'term_id', value: term });
        var url="/content/canceledit_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".termsdatarow[data-term='"+term+"']").empty().html(response.data.content);
                init_termspage_editcontent();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".termssave_params").unbind('click').click(function () {
        var term=$(this).data('term');
        var newcontent = $(".content-row[data-term='"+term+"']").find('.uEditorIframe').contents().find("body").html();
        var params=new Array();
        params.push({name: 'session', value: $("#terms_session").val()});
        params.push({name: 'term_id', value: term });
        params.push({name: 'newcontent', value: newcontent});
        var url="/content/saveedit_termsparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".termsdatarow[data-term='"+term+"']").empty().html(response.data.content);
                init_termspage_editcontent();
            } else {
                show_error(response);
            }
        },'json');
    })
}