function init_redrawlist() {
    var url="/redraw/get_redrawlogos";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $("div.redrawdata").empty().html(response.data.content);
            // $("input#toredraw_total").val(response.data.total);
            redrawcontent_manage();
        } else {
            show_error(response);
        }
    }, 'json');
}

function redrawcontent_manage() {
    // $("div.usertextlogo").bt({
    //     fill : '#FFFFFF',
    //     cornerRadius: 10,
    //     width: 220,
    //     padding: 10,
    //     strokeWidth: '2',
    //     positions: "most",
    //     strokeStyle : '#000000',
    //     strokeHeight: '18',
    //     cssClass: 'white_tooltip',
    //     cssStyles: {color: '#000000'}
    // })
    $("input.markasvector").change(function(){
        mark_asvector(this);
    })
    $("div.filesourcedata").click(function(){
        // var filename=$(this).text();
        var srcid=$(this).data('redrawid');
        var url="/redraw/src_file";
        $.post(url, {'art_id': srcid, 'type':'source'}, function(response){
            if (response.errors=='') {
                $.fileDownload('/redraw/openimg', {httpMethod : "POST", data: {url : response.data.url, file: response.data.filename}});
                return false; //this is critical to stop the click event which will trigger a normal file download!
            } else {
                show_error(response);
            }
        }, 'json');
    })
    $("div.submitdata").click(function(){
        var logo=$(this).data('logo');
        upload_logo(logo);
    });
    // $("div.imagesourceview").each(function(){
    //     $(this).bt({
    //         ajaxCache: false,
    //         width: 200,
    //         fill: 'white',
    //         cornerRadius: 20,
    //         padding: 20,
    //         strokeWidth: 1,
    //         ajaxPath: ["$(this).data('redrawsource')"]
    //     });
    // });
    // $(".longredrawmessage").each(function(){
    //     $(this).bt({
    //         ajaxCache: false,
    //         /* trigger: 'click', */
    //         fill : '#FFFFFF',
    //         cornerRadius: 10,
    //         width: 250,
    //         padding: 10,
    //         strokeWidth: '2',
    //         positions: "most",
    //         strokeStyle : '#000000',
    //         strokeHeight: '18',
    //         cssClass: 'white_tooltip',
    //         ajaxPath: ["$(this).data('redraw')"]
    //     });
    // })
}

function mark_asvector(obj) {
    if (confirm('Mark this Logo as Vector file?')) {
        var vectlogo=$("#"+obj.id).val();
        var url="/redraw/markvector";
        $.post(url, {'logo_id':vectlogo}, function(response){
            if (response.errors=='') {
                init_redrawlist();
            } else {
                show_error(response);
                $("#"+obj.id).prop('checked',false);
            }
        }, 'json');
    } else {
        $("#"+obj.id).prop('checked',false);
    }
}

function upload_logo(logo) {
    var url="/redraw/prepare_upload";
    $.post(url, {'logo_id':logo}, function(response){
        if (response.errors=='') {
            $("#modalRedrawUpload").find('div.modal-body').empty().html(response.data.content);
            $("#modalRedrawUpload").modal({backdrop: 'static', keyboard: false, show: true});
            $("div.vectorsave_data").unbind('click').click(function(){
                save_upload();
            })
            init_upload();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_upload() {
    var temp= '<div class="qq-uploader"><div class="custom_upload qq-upload-button"><span style="clear: both; float: left; padding-left: 10px; padding-top: 8px;">'+
        '<em>Upload</em></span></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: ['ai','eps'],
        action: '/utils/vectoredattach',
        template: temp,
        multiple: false,
        debug: false,
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success) {
                var url="/redraw/vector_upload";
                $("ul.qq-upload-list").css('display','none');
                $.post(url, {'filename':responseJSON.filename,'doc_name':fileName}, function(response){
                    if (response.errors=='') {
                        $("#orderattachlists").empty().html(response.data.content);
                        $(".qq-uploader").hide();
                        $("div.vectorsave_data").show();
                        $("div.delvectofile").click(function(){
                            $("#orderattachlists").empty();
                            $(".qq-uploader").show();
                            $("div.vectorsave_data").hide();
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

function save_upload() {
    var logo=$("input#logo").val();
    var file=$("input#filename").val();
    var url="/redraw/save_upload";
    $.post(url, {'logo':logo,'file':file}, function(response){
        if (response.errors=='') {
            $("#modalRedrawUpload").modal('hide');
            init_redrawlist();
            $.flash('Task Completed');
        } else {
            show_error(response);
        }
    }, 'json');
}
