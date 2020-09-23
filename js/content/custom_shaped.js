function init_customshape_view() {
    window_alignment();
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_customcontent();
    });
    $(".displaygallery").unbind('click').click(function(){
        display_gallery();
    });
    $(".displaycasestudy").unbind('click').click(function () {
        display_casestudy();
    });
    $(".edit_button[data-page='custom']").unbind('click').click(function () {
        init_customshape_edit();
    });
    // Open image
    $(".custom_mainimagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });

    $(".custom_homepageimagesrc").unbind('click').click(function () {
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });

    $("[data-fancybox]").fancybox({
        protect: true
    });
    $(".custom_casestudyimage").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });

}

function display_customcontent() {
    if ($(".displaycontent").hasClass('show')) {
        $(".customcontent-area").hide();
        $(".displaycontent").removeClass('show').addClass('hiden').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".customcontent-area").show();
        $(".displaycontent").removeClass('hiden').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function display_gallery() {
    if ($(".displaygallery").hasClass('show')) {
        $(".custom_galleries_area").hide();
        $(".displaygallery").removeClass('show').addClass('hiden').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".custom_galleries_area").show();
        $(".displaygallery").removeClass('hiden').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function display_casestudy() {
    if ($(".displaycasestudy").hasClass('show')) {
        $(".custom_casestudies_area").hide();
        $(".displaycasestudy").removeClass('show').addClass('hiden').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".custom_casestudies_area").show();
        $(".displaycasestudy").removeClass('hiden').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function init_customshape_edit() {
    var url = "/content/edit_customcontent";
    var params = new Array();
    params.push({name:'brand', value: $("#contentbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#customshappedview").empty().html(response.data.content);
            $(".content_preview").on('click',function () {
                var url=$("#custom_previewurl").val();
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
            init_customshape_editcontent();
        } else {
            show_error(response);
        }
    },'json');
}

function init_customshape_editcontent() {
    var uploadtemplate= '<div class="qq-uploader">' +
        '<div class="custom_upload"><span></span></div>' +
        '</div>';
    // Show / view content
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_customcontent();
    });
    $(".displaygallery").unbind('click').click(function(){
        display_gallery();
    });
    $(".displaycasestudy").unbind('click').click(function () {
        display_casestudy();
    });
    // Cancel Edit
    $(".cancel_button[data-page='custom']").unbind('click').click(function () {
        init_contentpage('custom');
    });
    // Save
    $(".save_button[data-page='custom']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name:'brand', value: $("#contentbrand").val()});
        var url="/content/save_customcontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('custom');
            } else {
                show_error(response);
            }
        },'json');
    });
    // Meta
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // input
    $("input[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // textarea
    $("textarea[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Remove main image
    $(".custom_mainimageremove").unbind('click').click(function(){
        $(".custom_mainimagesrc").unbind('click');
        if (confirm('Delete Main Image?')) {
            var params=new Array();
            params.push({name: 'session', value: $("#custom_session").val()});
            params.push({name: 'type', value: 'data'});
            params.push({name: 'field', value: 'custom_mainimage'});
            params.push({name: 'newval', value: ''});
            var url="/content/change_customparam";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#custom_mainimagearea").empty().html('<div class="custom_mainimageempty"><div class="custom_mainimageupload" id="mainimageupload"></div></div>');
                    init_customshape_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        } else {
            init_customshape_editcontent();
        }
    });
    // Add main image
    if ($("#mainimageupload").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('mainimageupload'),
            action: '/utils/save_itemimg',
            /* template: temp,            */
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session', value: $("#custom_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'custom_mainimage'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_customparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#custom_mainimagearea").empty().html(response.data.content);
                            init_customshape_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    // Open image
    $(".custom_mainimagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    // Remove collage
    $(".custom_homeimageremove").unbind('click').click(function(){
        if (confirm('Delete Homepage Collage Image?')) {
            var params=new Array();
            params.push({name: 'session', value: $("#custom_session").val()});
            params.push({name: 'type', value: 'data'});
            params.push({name: 'field', value: 'custom_homepageimage'});
            params.push({name: 'newval', value: ''});
            var url="/content/change_customparam";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#custom_homepageimagearea").empty().html('<div class="custom_homepageimageempty"><div class="custom_mainimageupload" id="homepageimageupload"></div></div>');
                    init_customshape_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    if ($("#homepageimageupload").length>0) {
        // Add main image
        var uploader = new qq.FileUploader({
            element: document.getElementById('homepageimageupload'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session', value: $("#custom_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'custom_homepageimage'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_customparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#custom_homepageimagearea").empty().html(response.data.content);
                            init_customshape_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    // View
    $(".custom_homepageimagesrc").unbind('click').click(function () {
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    // Gallery
    $("input[data-content='gallery']").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'gallery'});
        params.push({name: 'custom_gallery_id', value: $(this).data('gallery')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Click on place for image
    $(".custom_galleryimageupload").each(function(){
        var gallery = $(this).data('gallery');
        var img = $(this).prop('id');
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
                    params.push({name: 'session', value: $("#custom_session").val()});
                    params.push({name: 'imageorder', value: gallery});
                    params.push({name: 'imagetype', value: 'gallery_image'});
                    params.push({name: 'imagesrc', value: responseJSON.filename});
                    var url='/content/save_imageupload_custom';
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".custom_galleries_area").empty().html(response.data.content);
                            init_customshape_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    })
    // Remove image
    $(".custom_galleryimagedelete").unbind('click').click(function(){
        if (confirm('Delete image from Gallery?')) {
            var params = new Array();
            params.push({name: 'session', value: $("#custom_session").val()});
            params.push({name: 'custom_gallery_id', value: $(this).data('gallery')});
            var url="/content/remove_customgalleryimage";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".custom_galleries_area").empty().html(response.data.content);
                    init_customshape_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // View
    // $(".custom_galleryitem").unbind('click').click(function(){
    //     var imgsrc = $(this).find('img').prop('src');
    //     $.fancybox.open({
    //         src  : imgsrc,
    //         type : 'image',
    //         autoSize : false
    //     });
    // });
    $("[data-fancybox]").fancybox({
        protect: true
    });
    // Add gallery
    $(".add_new_gallery").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        var url="/content/add_customgallery";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".custom_galleries_area").empty().html(response.data.content);
                init_customshape_editcontent();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[type='checkbox'][data-field='gallery_show']").unbind('change').change(function(){
        var newval=0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'gallery'});
        params.push({name: 'custom_gallery_id', value: $(this).data('gallery')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: newval});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Delete gallery
    $("div.custom_gallerydelete").unbind('click').click(function(){
        if (confirm('Remove Gallery?')) {
            var params = new Array();
            params.push({name: 'session', value: $("#custom_session").val()});
            params.push({name: 'custom_gallery_id', value: $(this).data('gallery')});
            var url="/content/remove_customgallery";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".custom_galleries_area").empty().html(response.data.content);
                    init_customshape_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    // Item in gallery
    $(".custom_galleryitemdelete").unbind('click').click(function(){
        if (confirm('Delete image from Gallery?')) {
            var params = new Array();
            params.push({name: 'session', value: $("#custom_session").val()});
            params.push({name: 'custom_gallery_id', value: $(this).data('gallery')});
            var url="/content/remove_customgalleryimage";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".custom_galleries_area").empty().html(response.data.content);
                    init_customshape_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // CaseStudy
    // Upload
    // $(".custom_casestudyimage_empty").unbind('click').click(function(){
    //     var casestudy = $(this).data('casestudy');
    //     upload_customshaped_image('casestudy_image',casestudy);
    // });
    $(".custom_casestudyimage_empty").each(function () {
        var casestudy = $(this).data('casestudy');
        var img = $(this).find('div.custom_casestudyitemupload').prop('id');
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
                    params.push({name: 'session', value: $("#custom_session").val()});
                    params.push({name: 'imageorder', value: casestudy});
                    params.push({name: 'imagetype', value: 'casestudy_image'});
                    params.push({name: 'imagesrc', value: responseJSON.filename});
                    var url='/content/save_imageupload_custom';
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".custom_casestudies_area").empty().html(response.data.content);
                            init_customshape_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    });
    // Delete
    $(".custom_casestudyimagedelete").unbind('click').click(function(){
        if (confirm('Delete Case Study?')) {
            var params=new Array();
            params.push({name: 'session', value: $("#custom_session").val()});
            params.push({name: 'custom_casestudy_id', value: $(this).data('casestudy')});
            var url="/content/remove_customcasestudy";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $(".custom_casestudies_area").empty().html(response.data.content);
                    init_customshape_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // View
    $(".custom_casestudyimage").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    // Change params
    $("input[data-content='casestudy']").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'casestudy'});
        params.push({name: 'custom_casestudy_id', value: $(this).data('casestudy')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='casestudy']").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name: 'type', value: 'casestudy'});
        params.push({name: 'custom_casestudy_id', value: $(this).data('casestudy')});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_customparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}


