$(document).ready(function () {
    $(".menubutton").unbind('click').click(function () {
        var url=$(this).data('menulink');
        window.location.href=url;
    });
    $("#signout").unbind('click').click(function () {
        if (confirm('You want to sign out?')==true) {
            window.location.href='/login/logout';
        }
    })
})

function show_error(response) {
    alert(response.errors);
    if(response.data.url !== undefined) {
        window.location.href=response.data.url;
    }
}
