function navigation_init() {
    // close
    $(".neworder_close").unbind("click").click(function () {
        $("#modalLeadOrder").modal("hide");
    });
}