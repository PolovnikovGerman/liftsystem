var boxid;
function navigation_init() {
    // close
    $(".neworder_close").unbind("click").click(function () {
        $("#modalLeadOrder").modal("hide");
    });
    // Art history - change scroll
    new SimpleBar(document.getElementById('orddtls_historybox'), {autoHide: false});
    new SimpleBar(document.getElementById('ordercontacts_table'), {autoHide: false});
    new SimpleBar(document.getElementById('orderitemsarea'), {autoHide: false});
    new SimpleBar(document.getElementById('order_art_boxes'), {autoHide: false});
    new SimpleBar(document.getElementById('trackcodesarea'), {autoHide: false});
    $(".claymodels_body").find('div.claymodels_optn_box').each(function () {
        boxid = $(this).attr('id');
        new SimpleBar(document.getElementById(boxid), {autoHide: false});
    });
    $(".previewpict_body").find('div.previewpict_optn_box').each(function () {
        boxid = $(this).attr('id');
        new SimpleBar(document.getElementById(boxid), {autoHide: false});
    });
    $(".artproofs_optn").find('div.optn_box').each(function () {
        boxid = $(this).attr('id');
        new SimpleBar(document.getElementById(boxid), {autoHide: false});
    })
}