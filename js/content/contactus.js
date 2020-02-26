function init_contactus_view() {
    window_alignment();
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content()
    });
    $(".edit_button[data-page='contactus']").unbind('click').click(function () {
        init_contactus_edit();
    });
}

function display_content() {
    if ($(".displaycontent").hasClass('show')) {
        $(".contactcontent-area").hide();
        $(".displaycontent").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".contactcontent-area").show();
        $(".displaycontent").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function init_contactus_edit() {
    var params = new Array();
    params.push({name:'brand', value: $("#contentbrand").val()});
    var url = "/content/edit_contactus";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#contactusview").empty().html(response.data.content);
            $(".content_preview").on('click',function () {
                var url=$("#contactus_previewurl").val();
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
            init_contactus_editcontent();
        } else {
            show_error(response);
        }
    },'json');
}

function init_contactus_editcontent() {
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content()
    });
    // Cancel Edit
    $(".cancel_button[data-page='contactus']").unbind('click').click(function () {
        init_contentpage('contactus');
    });
    // Save
    $(".save_button[data-page='contactus']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name:'brand', value: $("#contentbrand").val()});
        var url="/content/save_contactcontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('contactus');
            } else {
                show_error(response);
            }
        },'json');
    });
    // Meta
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_contactparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_contactparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Content
    $("input[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_contactparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_contactparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Address
    $("input[data-content='address']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name: 'type', value: 'address'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_contactparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='address']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#contact_session").val()});
        params.push({name: 'type', value: 'address'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_contactparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}