$(document).ready(function (){
    init_onlineleadorder_edit();
    init_rushpast();
    // Show error
    if ($("#duplerroritemmsg").length > 0) {
        var errmsg = $("#duplerroritemmsg").val();
        alert(errmsg);
        var errfld = $("#duplerroritem").val();
        $("span.addnewcolor[data-item='"+errfld+"']").trigger('click');
    }
});