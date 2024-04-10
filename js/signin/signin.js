$(document).ready(function () {
    init_signin_resize();
    init_signin();
});

$(window).resize(function() {
    init_signin_resize();
});

function init_signin_resize() {
    var totalheigth = window.innerHeight;
    var headheigth = parseInt($(".header").css('height'));
    var heigth = parseInt(totalheigth) - headheigth;
    var marginform = parseInt(heigth / 3);

    $(".login-form").css('height', heigth);
    $(".signin_form").css('margin-top',marginform);
    $(".verifycode_form").css('margin-top',marginform);

}
function init_signin() {
    $("#letsgo").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'username', value: $("#email").val()});
        params.push({name: 'passwd', value: $("#passwd").val()});
        var url='/login/signin';
        $.post(url, params, function (resposne) {
            if (resposne.errors=='') {
                if (parseInt(resposne.data.chkcode)==1) {
                    $("#signformarea").empty().html(resposne.data.content);
                    init_signin_resize();
                    init_codeverify();
                } else {
                    window.location.href=resposne.data.url;
                }
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
                    if (parseInt(resposne.data.chkcode)==1) {
                        $("#signformarea").empty().html(resposne.data.content);
                        init_codeverify();
                    } else {
                        window.location.href=resposne.data.url;
                    }
                } else {
                    show_errors(resposne);
                }
            },'json');
        }
    });
}

function init_codeverify() {
    $("input.verifyformelement").keypress(function(event) {
        if (event.which == 13) {
            var params = new Array();
            params.push({name: 'code', value: $("#code").val()});
            var url = '/login/codeverify';
            $.post(url, params, function (resposne) {
                if (resposne.errors=='') {
                    window.location.href=resposne.data.url;
                } else {
                    show_errors(resposne);
                }
            },'json');
        }
    });
    $("#letsverify").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'code', value: $("#code").val()});
        var url = '/login/codeverify';
        $.post(url, params, function (resposne) {
            if (resposne.errors=='') {
                window.location.href=resposne.data.url;
            } else {
                show_errors(resposne);
            }
        },'json');
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
