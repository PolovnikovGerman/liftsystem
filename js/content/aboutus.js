function init_aboutpage_view() {
    window_alignment();
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    $(".about_mainimagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".about_affilationsrc").unbind('click').click(function () {
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    })
    $(".edit_button[data-page='about']").unbind('click').click(function () {
        init_aboutpage_edit();
    });
}

function display_content() {
    if ($(".displaycontent").hasClass('show')) {
        $(".aboutuscontent-area").hide();
        $(".displaycontent").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".aboutuscontent-area").show();
        $(".displaycontent").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function init_aboutpage_edit() {
    var url = "/content/edit_aboutcontent";
    var params = new Array();
    params.push({name:'brand', value: $("#contentbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#aboutusview").empty().html(response.data.content);
            $(".content_preview").on('click',function () {
                var url=$("#previewurl").val();
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
            init_aboutpage_editcontent();
        } else {
            show_error(response);
        }
    },'json');
}

function init_aboutpage_editcontent() {
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    // Cancel Edit
    $(".cancel_button[data-page='about']").unbind('click').click(function () {
        init_contentpage('about');
    });
    // Save
    $(".save_button[data-page='about']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name:'brand', value: $("#contentbrand").val()});
        var url="/content/save_aboutpagecontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('about');
            } else {
                show_error(response);
            }
        },'json');
    });
    // Manage content
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_aboutparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_aboutparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_aboutparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_aboutparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='address']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name: 'type', value: 'address'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_aboutparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='address']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#about_session").val()});
        params.push({name: 'type', value: 'address'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_aboutparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Upload affilate image
    $(".about_affilationempty").each(function(){
        var imagenum = $(this).data('image');
        var img = $(this).find('div.about_affilationupload').prop('id');
        var uploader = new qq.FileUploader({
            element: document.getElementById(img),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session', value: $("#about_session").val()});
                    params.push({name: 'type', value: 'affilate_img'});
                    params.push({name: 'imageorder', value: imagenum});
                    params.push({name: 'imagesrc', value: responseJSON.filename});
                    var url='/content/save_aboutimage';
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".affilateimagearea[data-image='"+imagenum+"']").empty().html(response.data.content);
                            init_aboutpage_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    });
    $(".about_affilationremove").unbind('click').click(function(){
        if (confirm('Delete Affiliation Logo?')) {
            var imagenum = $(this).data('image');
            var params=new Array();
            params.push({name: 'session', value: $("#about_session").val()});
            params.push({name: 'type', value: 'affilate_img'});
            params.push({name: 'imageorder', value: imagenum});
            params.push({name: 'imagesrc', value: ''});
            var url='/content/save_aboutimage';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".affilateimagearea[data-image='"+imagenum+"']").empty().html(response.data.content);
                    init_aboutpage_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Main image remove
    $(".about_mainimageremove").unbind('click').click(function () {
        if (confirm('Delete Main Image?')) {
            var params=new Array();
            params.push({name: 'session', value: $("#about_session").val()});
            params.push({name: 'type', value: 'data'});
            params.push({name: 'field', value: 'about_mainimage'});
            params.push({name: 'newval', value: ''});
            var url="/content/change_customparam";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#about_mainimagearea").empty().html('<div class="about_mainimageempty"><div class="about_mainimageupload" id="mainimageupload"></div></div>');
                    init_aboutpage_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    if ($("#mainimageupload").length>0) {
        // Add main image
        var uploader = new qq.FileUploader({
            element: document.getElementById('mainimageupload'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session', value: $("#about_session").val()});
                    params.push({name: 'type', value: 'main_image'});
                    params.push({name: 'field', value: 'about_mainimage'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/save_aboutimage";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#about_mainimagearea").empty().html(response.data.content);
                            init_aboutpage_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".about_mainimagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".about_affilationsrc").unbind('click').click(function () {
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    })
}