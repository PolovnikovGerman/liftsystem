function init_categories_page() {
    var category=$(".category_data_row").first().data('category');
    //get meta and content
    activate_categoryview(category);
}

function activate_categoryview(category) {
    var url="/database/get_category_details";
    $.post(url,{category_id:category}, function(response){
        if (response.errors=='') {
            $(".category_metadata_area").empty().html(response.data.meta);
            $(".categorycontent-area").empty().html(response.data.content);
            $("#button_category_name").empty().html('<span>Category: </span>'+response.data.category_name);
            $("#button_category_view").empty().html('You are in View Mode'+response.data.view_button);
            $(".category_data_row").removeClass('active');
            $(".category_data_row").find('div.category_pointer').empty();

            $(".category_data_row[data-category='"+category+"']").addClass('active');
            $(".category_data_row[data-category='"+category+"']").find('div.category_pointer').html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');
            init_categories_view();
        }
    },'json');
}

function init_categories_view() {
    // window_alignment();

    $("#sortable").sortable().on('sortupdate', function (e, ui) {
        var data = $("#sortsequence").serializeArray();
        var curcateg = $(".category_data_row.active").data('category');
        data.push({name: 'active', value: curcateg});
        $.ajax({
            data: data,
            type: 'POST',
            dataType: "json",
            url: '/database/category_sort'
        }).success(function (response) {
            $(".category_list_area").empty().html(response.data.content);
            init_categories_view();
        });
    });

    $(".displaymeta").unbind('click').click(function () {
        display_metacategory();
    });
    $(".displaycontent").unbind('click').click(function () {
        display_category_content();
    });
    // Activate category
    $("div.category_data_row").unbind('click').click(function () {
        var category = $(this).data('category');
        activate_categoryview(category);
    });
    // Open image
    $(".homepage_iconsrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    $(".dropdown_iconsrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    // Activate edit
    $(".edit_button").unbind('click').click(function () {
        var category=$(this).data('category');
        init_category_edit(category);
    });
}

function init_category_edit(category) {
    var url="/database/get_category_edit";
    $.post(url,{category_id:category}, function(response){
        if (response.errors=='') {
            $(".category_metadata_area").empty().html(response.data.meta);
            $(".categorycontent-area").empty().html(response.data.content);
            $("#button_category_view").addClass('editmode').removeClass('viewmode').empty().html('You are in Edit Mode'+response.data.view_button);
            $(".category_data_row").unbind('click');
            init_categorycontent_edit();
        }
    },'json');
}

function init_categorycontent_edit() {
    $(".cancel_button").unbind('click').click(function(){
        var category=$(this).data('category');
        activate_categoryview(category);
    });
    $(".save_button").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session_id', value: $("#category_session").val()});
        var url="/database/update_category_content";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".category_data_row[data-category='"+response.data.category+"']").find('div.category_label').empty().html(response.data.category_name);
                activate_categoryview(response.data.category);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input[data-content='category']").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'session_id', value: $("#category_session").val()});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/database/change_category_content";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea[data-content='category']").unbind('change').change(function () {
        var params=new Array();
        params.push({name: 'session_id', value: $("#category_session").val()});
        params.push({name: 'field', value: $(this).data('field')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/database/change_category_content";
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Upload images
    // Image homepage
    $(".remove_homepage_icon").unbind('click').click(function () {
        if (confirm('Delete Homepage Collage Image?')) {
            var params=new Array();
            params.push({name: 'session_id', value: $("#category_session").val()});
            params.push({name: 'field', value: 'icon_homepage'});
            params.push({name: 'newval', value: ''});
            var url="/database/change_category_content";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#homepageicon_area").empty().html('<div class="homepage_iconempty"><div class="homepageupload" id="newhomepageimg"></div></div>');
                    init_categorycontent_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Add main image
    if ($("#newhomepageimg").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('newhomepageimg'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session_id', value: $("#category_session").val()});
                    params.push({name: 'field', value: 'icon_homepage'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/database/change_category_content";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#homepageicon_area").empty().html(response.data.content);
                            init_categorycontent_edit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".homepage_iconsrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
    // Image dropdown
    $(".remove_dropdown_icon").unbind('click').click(function () {
        if (confirm('Delete Drop Down Image?')) {
            var params=new Array();
            params.push({name: 'session_id', value: $("#category_session").val()});
            params.push({name: 'field', value: 'icon_dropdown'});
            params.push({name: 'newval', value: ''});
            var url="/database/change_category_content";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#dropdownicon_area").empty().html('<div class="dropdown_iconempty"><div class="dropdownupload" id="newdropdownimg"></div></div>');
                    init_categorycontent_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Add dropdown image
    if ($("#newdropdownimg").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('newdropdownimg'),
            action: '/utils/save_itemimg',
            uploadButtonText: '',
            multiple: false,
            debug: false,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success==true) {
                    $("li.qq-upload-success").hide();
                    var params=new Array();
                    params.push({name: 'session_id', value: $("#category_session").val()});
                    params.push({name: 'field', value: 'icon_dropdown'});
                    params.push({name: 'newval', value: responseJSON.filename});
                    var url="/database/change_category_content";
                    $.post(url, params, function (response) {
                        if (response.errors=='') {
                            $("#dropdownicon_area").empty().html(response.data.content);
                            init_categorycontent_edit();
                        } else {
                            show_error(response);
                        }
                    },'json');
                }
            }
        });
    }
    $(".dropdown_iconsrc").unbind('click').click(function(){
        var imgsrc = $(this).find('img').prop('src');
        $.fancybox.open({
            src  : imgsrc,
            type : 'image',
            autoSize : false
        });
    });
}

function display_metacategory() {
    if ($(".displaymeta").hasClass('shown')) {
        $(".category_metadata_area").hide();
        $(".displaymeta").removeClass('shown').addClass('hiden').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".category_metadata_area").show();
        $(".displaymeta").removeClass('hiden').addClass('shown').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}

function display_category_content() {
    if ($(".displaycontent").hasClass('shown')) {
        $(".categorycontent-area").hide();
        $(".displaycontent").removeClass('shown').addClass('hiden').empty().html('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
    } else {
        $(".categorycontent-area").show();
        $(".displaycontent").removeClass('hiden').addClass('shown').empty().html('<i class="fa fa-chevron-down" aria-hidden="true"></i>');
    }
}