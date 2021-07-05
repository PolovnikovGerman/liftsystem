function init_faqpage_view(brand) {
    // window_alignment();
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    $(".displayfaqsection").unbind('click').click(function(){
        var section = $(this).data('faqsection');
        display_faqsection(section);
    });
    $(".edit_button[data-link='faqview']").unbind('click').click(function () {
        init_faqpage_edit(brand);
    });
}

function display_content() {
    if ($(".displaycontent").hasClass('show')) {
        $(".faqcontent-area").hide();
        $(".displaycontent").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".faqcontent-area").show();
        $(".displaycontent").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function display_faqsection(section) {
    if ($(".displayfaqsection[data-faqsection='"+section+"']").hasClass('show')) {
        $(".faqsection_area[data-faqsection='"+section+"']").hide();
        $(".displayfaqsection[data-faqsection='"+section+"']").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".faqsection_area[data-faqsection='"+section+"']").show();
        $(".displayfaqsection[data-faqsection='"+section+"']").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function init_faqpage_edit(brand) {
    var url = "/content/edit_faqcontent";
    var params = new Array();
    params.push({name:'brand', value: brand});
    $.post(url, params, function (response) {
        if (response.errors=='') {
            if (brand=='SB') {
                $("#sbfaqview").empty().html(response.data.content);
                $(".submenu_manage[data-link='sbfaqview']").find('div.submenu_label').empty().html('Edit Mode');
                $(".submenu_manage[data-link='sbfaqview']").find('div.buttons').empty().html(response.data.buttons);
            } else {
                $("#btfaqview").empty().html(response.data.content);
                $(".submenu_manage[data-link='btfaqview']").find('div.submenu_label').empty().html('Edit Mode');
                $(".submenu_manage[data-link='btfaqview']").find('div.buttons').empty().html(response.data.buttons);
            }
            $(".content_preview").on('click',function () {
                var url=$("#faq_previewurl").val();
                $.fancybox.open({
                    src  : url,
                    type : 'iframe',
                    opts : {
                        afterShow : function( instance, current ) {
                            console.info( 'done!' );
                        }
                    }
                });
            });
            init_faqpage_editcontent(brand);
        } else {
            show_error(response);
        }
    },'json');
}

function init_faqpage_editcontent(brand) {
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    $(".displayfaqsection").unbind('click').click(function(){
        var section = $(this).data('faqsection');
        display_faqsection(section);
    });
    // Cancel Edit
    $(".cancel_button[data-page='faq']").unbind('click').click(function () {
        init_contentpage('faq', brand);
    });
    // Save
    $(".save_button[data-page='faq']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name:'brand', value: brand});
        var url="/content/save_faqpagecontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('faq', brand);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Meta
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_faqparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_faqparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_faqparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_faqparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='faq']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'type', value: 'faq_section'});
        params.push({name: 'faq_section', value: $(this).data('section')});
        params.push({name: 'faq_id', value: $(this).data('faq')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_faqparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    })
    $("textarea[data-content='faq']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'type', value: 'faq_section'});
        params.push({name: 'faq_section', value: $(this).data('section')});
        params.push({name: 'faq_id', value: $(this).data('faq')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_faqparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add Question
    $(".add_new_question").unbind('click').click(function () {
        var section = $(this).data('faqsection');
        var params=new Array();
        params.push({name: 'session', value: $("#faq_session").val()});
        params.push({name: 'faq_section', value: section});
        var url="/content/add_faqquestion";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".faqsection_area[data-faqsection='"+section+"']").empty().html(response.data.content);
                init_faqpage_editcontent(brand);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Remove Question
    $(".faq_questiondelete").unbind('click').click(function () {
        if (confirm('Remove question?')) {
            var section = $(this).data('faqsection');
            var params=new Array();
            params.push({name: 'session', value: $("#faq_session").val()});
            params.push({name: 'faq_section', value: section});
            params.push({name: 'faq_id', value: $(this).data('faq')});
            var url="/content/remove_faqquestion";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".faqsection_area[data-faqsection='"+section+"']").empty().html(response.data.content);
                    init_faqpage_editcontent(brand);
                } else {
                    show_error(response);
                }
            },'json');
        }

    })
}