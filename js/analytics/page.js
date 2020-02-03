$(document).ready(function(){
    // Find first item
    var start = $(".maincontentmenu_item:first").data('link');
    init_page(start);
    $(".maincontentmenu_item").unbind('click').click(function () {
        var objid = $(this).data('link');
        init_page(objid);
    })
});
function init_page(objid) {
    console.log('Page '+objid);
    // $(".artcontentarea").hide();
    // $(".maincontentmenu_item").removeClass('active');
    // $(".maincontentmenu_item[data-link='" + objid + "']").addClass('active');
    // switch (objid) {
    //     case 'orderlist':
    //         $("#orderlist").show();
    //         init_orders();
    //         break;
    //     case 'requestlist':
    //         $("#requestlist").show();
    //         init_proofdata();
    //         break;
    //     case 'taskview':
    //         $("#taskview").show();
    //         init_tasks_management();
    //         init_tasks_page();
    //         break;
    // }
}