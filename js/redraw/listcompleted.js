function init_redrawcomplet_content() {
    search_readyredrawn();
}

function search_readyredrawn() {
    var url="/redraw/complited_count";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $('#completetotal').val(response.data.total);
            $("#completejobs").empty().html(response.data.total);
            $("#completeavgtime").empty().html(response.data.avg_time);
            $("#completeavgrushtime").empty().html(response.data.avg_rush);
            initArtCompletePagination();
        } else {
            show_error(response);
        }
    }, 'json');
}

function initArtCompletePagination() {
    // count entries inside the hidden content
    var num_entries = $('#completetotal').val();
    var perpage = $("#completeperpage").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $("div.completed_pagesviews div.Pagination").empty();
        pageCompleteArtCallback(0);
    } else {
        var curpage = $("#completecurpage").val();
        // Create content inside pagination element
        $("div.completed_pagesviews div.Pagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageCompleteArtCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }
}

function pageCompleteArtCallback(page_index) {
    var params=new Array();
    params.push({name:'limit',value:$("#completeperpage").val()});
    params.push({name:'order_by',value:$("#completeorderby").val()});
    params.push({name:'direction',value:$("#completedirection").val()});
    params.push({name:'maxval',value:$('#completetotal').val()});
    params.push({name:'offset',value: page_index});

    $("#loader").show();
    var url='/redraw/completed_datalist';
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div.content-art-table").empty().html(response.data.content);
            $("#loader").hide();
            init_completed_content();
            jQuery.balloon.init();
            leftmenu_alignment();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_completed_content() {
    $("div.completed_srcfile_data.sourcedat").click(function() {
        var srcid = $(this).data('logoid');
        var url = "/redraw/src_file";
        $.post(url, {'art_id': srcid,'type':'source'}, function(response) {
            if (response.errors == '') {
                $.fileDownload('/redraw/openimg', {httpMethod: "POST", data: {url: response.data.url, file: response.data.filename}});
                return false; //this is critical to stop the click event which will trigger a normal file download!
            } else {
                show_error(response);
            }
        }, 'json');
    })
    $("div.completed_srcfile_data.vectordat").click(function() {
        var srcid = $(this).data('logoid');
        var url = "/redraw/src_file";
        $.post(url, {'art_id': srcid, 'type':'vectorfile'}, function(response) {
            if (response.errors == '') {
                $.fileDownload('/redraw/openimg', {httpMethod: "POST", data: {url: response.data.url, file: response.data.filename}});
                return false; //this is critical to stop the click event which will trigger a normal file download!
            } else {
                show_error(response);
            }
        }, 'json');
    });
}