$(document).ready(function () {
    var heigth = window.innerHeight;
    $(".maincontent").css('height', heigth);
    init_signin();
    // var heigth = window.innerHeight;
    // $(".maincontent").css('height', heigth);
    // $("#openincome").unbind('click').click(function () {
    //     $.post('/login/show_signinform',{}, function (response) {
    //         if (response.errors=='') {
    //             $("#form").empty().html(response.data.content);
    //             init_signin();
    //         } else {
    //             show_errors(response);
    //         }
    //     },'json');
    // });
})

function init_signin() {
    $("#letsgo").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'username', value: $("#email").val()});
        params.push({name: 'passwd', value: $("#passwd").val()});
        var url='/login/signin';
        $.post(url, params, function (resposne) {
            if (resposne.errors=='') {
                window.location.href=resposne.data.url;
            } else {
                show_errors(resposne);
            }
        },'json');
    });
    $("input.signinformelement").keypress(function(event){
        if (event.which == 13) {
            var params=new Array();
            params.push({name: 'username', value: $("#email").val()});
            params.push({name: 'passwd', value: $("#passwd").val()});
            var url='/login/signin';
            $.post(url, params, function (resposne) {
                if (resposne.errors=='') {
                    window.location.href=resposne.data.url;
                } else {
                    show_errors(resposne);
                }
            },'json');
        }
    });
}

// function show_error(response) {
//     $('#modal_alert .modal-body p').html(response.errors);
//     var redirect_location = '';
//     var refresh = false;
//     if (response.data.url !== undefined) {
//         redirect_location = response.data.url;
//         refresh = true;
//     }
//     $('#modal_alert').modal('show');
//     $('#modal_alert #confirm').click(function() {
//         $('#modal_alert').modal('hide');
//     });
//     if (refresh) {
//         if (redirect_location) {
//             $('#modal_alert').on('hidden.bs.modal', function () {
//                 window.location = redirect_location;
//             });
//         } else {
//             $('#modal_alert').on('hidden.bs.modal', function () {
//                 location.reload();
//             });
//         }
//     }
// };

function show_errors(response) {
    alert(response.errors);
    // $('.alert').alert('show');
    if (response.data.url !== undefined) {
        clearTimeout(timerId)
        window.location.href = response.data.url;
    }
}
