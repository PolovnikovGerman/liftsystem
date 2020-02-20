function init_service_page() {
    window_alignment();
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    $(".service_mainimagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".service_imagesrc").unbind('click').click(function () {
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    })
    $(".edit_button[data-page='extraservice']").unbind('click').click(function () {
        init_servicepage_edit();
    });
}

function display_content() {
    if ($(".displaycontent").hasClass('show')) {
        $(".extraservicecontent-area").hide();
        $(".displaycontent").removeClass('show').addClass('hide').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".extraservicecontent-area").show();
        $(".displaycontent").removeClass('hide').addClass('show').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function init_servicepage_edit() {
    var params = new Array();
    params.push({name:'brand', value: $("#contentbrand").val()});
    var url = "/content/edit_servicecontent";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#serviceview").empty().html(response.data.content);
            $(".content_preview").on('click',function () {
                var url=$("#service_previewurl").val();
                console.log('URL '+url);
                $.fancybox.open({
                    src  : url,
                    type : 'iframe',
                    opts : {
                        afterShow : function( instance, current ) {
                        }
                    }
                });
            });
            init_servicepage_editcontent();
        } else {
            show_error(response);
        }
    },'json');
}

function init_servicepage_editcontent() {
    // Show / hide content, other
    $(".displaymeta").unbind('click').click(function () {
        display_metadata();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_content();
    });
    // Cancel Edit
    $(".cancel_button[data-page='extraservice']").unbind('click').click(function () {
        init_contentpage('extraservice');
    });
    // Save
    $(".save_button[data-page='extraservice']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#service_session").val()});
        params.push({name:'brand', value: $("#contentbrand").val()});
        var url="/content/save_servicepagecontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('extraservice');
            } else {
                show_error(response);
            }
        },'json');
    });
    // Meta
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#service_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_serviceparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#service_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_serviceparam";
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
        params.push({name: 'session', value: $("#service_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_serviceparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#service_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_serviceparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Main image
    if ($(".service_mainimagesrcempty").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('uploadservicemainimage'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session', value: $("#service_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'service_mainimage'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_serviceparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#service_mainimagearea").empty().html(response.data.content);
                            init_servicepage_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".remove_service_mainimage").unbind('click').click(function(){
        if (confirm('Remove Main Image?')) {
            var params=new Array();
            params.push({name: 'session', value: $("#service_session").val()});
            params.push({name: 'type', value: 'data'});
            params.push({name: 'field', value: 'service_mainimage'});
            params.push({name: 'newval', value: ''});
            var url="/content/change_serviceparam";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#service_mainimagearea").empty().html('<div class="service_mainimagesrcempty"><div class="uploadservicemainimage" id="uploadservicemainimage"/></div>');
                    init_servicepage_editcontent();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Services images
    $(".uploadserviceimage").each(function(){
        var uplid=$(this).prop('id');
        var imagenum = $(this).data('image');
        var service = $(this).data('service');
        var uploader = new qq.FileUploader({
            element: document.getElementById(uplid),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session', value: $("#service_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: imagenum});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'service', value: service});
                    var url="/content/change_serviceparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $(".service_serviceimageplace[data-service='"+service+"']").empty().html(response.data.content);
                            init_servicepage_editcontent();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    });
    $(".remove_service_image").unbind('click').click(function(){
        if (confirm('Remove Service Image?')) {
            var service=$(this).data('service');
            var params=new Array();
            params.push({name: 'session', value: $("#service_session").val()});
            params.push({name: 'type', value: 'data'});
            params.push({name: 'field', value: 'service_image'+service});
            params.push({name: 'newval', value: ''});
            var url="/content/change_serviceparam";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".service_serviceimageplace[data-service='"+service+"']").empty().html('<div class="service_imagesrcempty"><div class="uploadserviceimage" data-image="service_image'+service+'" data-service="'+service+'" id="uploadserviceimage'+service+'"></div></div>');
                    init_servicepage_editcontent();
                } else {
                    show_error(response);
                }
            },'json');

        }
    })

    $(".service_mainimagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".service_imagesrc").unbind('click').click(function () {
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    })


}