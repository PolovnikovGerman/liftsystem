$(document).ready(function () {
    var heigth = window.innerHeight;
    $(".maincontent").css('height', heigth);
    $("#openincome").unbind('click').click(function () {
        $.post('/welcome/show_signin',{}, function (response) {
            if (response.errors=='') {
                $("#form").empty().html(response.data.content);
                init_signin();
            } else {
                show_error(response);
            }
        },'json');
    });
})

function init_signin() {

}