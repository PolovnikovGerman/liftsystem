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
}