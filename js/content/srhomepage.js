function init_srhomepage_editcontent() {
    // Cancel Edit
    $(".cancel_button[data-page='home']").unbind('click').click(function () {
        init_contentpage('home', 'SR');
    });
    // Save
    $(".save_button[data-page='home']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name:'brand', value: 'SR'});
        var url="/content/save_homecontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('home', 'SR');
            } else {
                show_error(response);
            }
        },'json');
    });
    init_srhome_upload();
    // Meta
    $("input[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_homepageparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='meta']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name: 'type', value: 'meta'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_homepageparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });

    $("input[data-content='content']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/content/change_homepageparam";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Show big image
    $(".homepage_imagesrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
}

function init_srhome_upload() {
    $(".slider_imageremove").unbind('click').click(function () {
        var url='/content/remove_homeimages';
        var slidernum = $(this).data('slider');
        var params = new Array();
        params.push({name: 'session', value: $("#homepage_session").val()});
        params.push({name: 'type', value: 'data'});
        params.push({name: 'field', value: 'slider_image_'+slidernum});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#homepage_slider"+slidernum).empty().html(response.data.content);
                init_srhome_upload();
            } else {
                show_error(response);
            }
        },'json');
    });
    if ($("#sliderupload1").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('sliderupload1'),
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
                    params.push({name: 'session', value: $("#homepage_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'slider_image_1'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_homepageparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#homepage_slider1").empty().html(response.data.content);
                            init_srhome_upload();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    if ($("#sliderupload2").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('sliderupload2'),
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
                    params.push({name: 'session', value: $("#homepage_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'slider_image_2'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_homepageparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#homepage_slider2").empty().html(response.data.content);
                            init_srhome_upload();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    if ($("#sliderupload3").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('sliderupload3'),
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
                    params.push({name: 'session', value: $("#homepage_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'slider_image_3'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_homepageparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#homepage_slider3").empty().html(response.data.content);
                            init_srhome_upload();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    if ($("#sliderupload4").length>0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('sliderupload4'),
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
                    params.push({name: 'session', value: $("#homepage_session").val()});
                    params.push({name: 'type', value: 'data'});
                    params.push({name: 'field', value: 'slider_image_4'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/content/change_homepageparam";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#homepage_slider4").empty().html(response.data.content);
                            init_srhome_upload();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }

}