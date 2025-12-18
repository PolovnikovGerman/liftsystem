$(document).ready(function(){
    // Find first item
    var start = '';
    if ($(".contentsubmenu_item.active").length > 0 ) {
        start = $(".contentsubmenu_item.active").data('link');
    } else {
        start = $(".contentsubmenu_item:first").data('link');
    }
    init_page(start);
    $(".contentsubmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    })
});

function init_page(objid) {
    $(".projcontentarea").hide();
    $(".contentsubmenu_item").removeClass('active');
    $(".contentsubmenu_item[data-link='"+objid+"']").addClass('active');
    switch (objid) {
        case 'projectsview':
            $("#projectsview").show();
            init_projects_content();
            break;
    }
}

function init_projects_content() {
    // $(".doupleorders").unbind('click').click(function () {
    //     var params = new Array();
    //     params.push({name: 'blocked', value: 0});
    //     var url = '/projects/vieworders';
    //     $.post(url, params, function (response) {
    //         if (response.errors=='') {
    //             $("#dualOrderspopup").find('div.modal-body').empty().html(response.data.content);
    //             $("#dualOrderspopup").modal({keyboard: false, show: true});
    //             init_doubleorders();
    //         }
    //     },'json');
    // });
    // $(".lockedorders").unbind('click').click(function () {
    //     var params = new Array();
    //     params.push({name: 'blocked', value: 1});
    //     var url = '/projects/vieworders';
    //     $.post(url, params, function (response) {
    //         if (response.errors=='') {
    //             $("#dualOrderspopup").find('div.modal-body').empty().html(response.data.content);
    //             $("#dualOrderspopup").modal({keyboard: false, show: true});
    //             init_doubleorders();
    //         }
    //     },'json');
    // });
    $(".dualorders").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'blocked', value: 0});
        params.push({name: 'content', value: 'dualorders'});
        var url = '/projects/viewcontent';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#modal-dualorders").find('div.modal-dialog').css('width',response.data.modalwidth);
                $("#modal-dualorders").find('div.modal-body').empty().html(response.data.content);
                $("#modal-dualorders").modal({keyboard: false, show: true});
                // Manage content
                init_manageproj_content();
            }
        },'json');
    });
    $(".leadsview").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'blocked', value: 0});
        params.push({name: 'content', value: 'leadsview'});
        var url = '/projects/viewcontent';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#modal-dualorders").find('div.modal-dialog').css('width',response.data.modalwidth);
                $("#modal-dualorders").find('div.modal-body').empty().html(response.data.content);
                $("#modal-dualorders").modal({keyboard: false, show: true});
                // Manage content
                init_manageproj_content();
            }
        },'json');
    })
    $(".orderleadsview").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'blocked', value: 0});
        params.push({name: 'content', value: 'orderleadview'});
        var url = '/projects/viewcontent';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $("#modal-dualorders").find('div.modal-dialog').css('width',response.data.modalwidth);
                $("#modal-dualorders").find('div.modal-body').empty().html(response.data.content);
                $("#modal-dualorders").modal({keyboard: false, show: true});
                // Manage content
                init_manageproj_content();
                init_leadsorders_content();
            }
        },'json');
    })
}