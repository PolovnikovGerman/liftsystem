$(document).ready(function () {
    $(".brandmenuitem").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'url', value: $(this).data('url')});
        params.push({name: 'brand', value: $(this).data('brand')});
        var url='/welcome/brandnavigate';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                window.location.href=response.data.url;
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".tab-brandmenu").unbind('click').click(function () {
        if ($(this).hasClass('active')) {
        } else {
            var params = new Array();
            params.push({name: 'brand', value: $(this).data('brand')});
            var url='/welcome/brandshow';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    window.location.href=response.data.url;
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    $("#signout").unbind('click').click(function () {
        if (confirm('You want to sign out?')==true) {
            window.location.href='/login/logout';
        }
    });
    $("div.menuitem").unbind('click').click(function () {
        var url = $(this).data('menulink');
        window.location.href=url;
    })
});

function init_totalinfo_content() {
    $(".totalsalesprev.active").unbind('click').click(function (){
        var params=new Array();
        params.push({name: 'weekdate', value: $(this).data('week')});
        var url = '/welcome/salestotals';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".period_analitic_info").empty().html(response.data.content);
                init_totalinfo_content();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".totalsalesnext.active").unbind('click').click(function (){
        var params=new Array();
        params.push({name: 'weekdate', value: $(this).data('week')});
        var url = '/welcome/salestotals';
        $.post(url, params, function (response){
            if (response.errors=='') {
                $(".period_analitic_info").empty().html(response.data.content);
                init_totalinfo_content();
            } else {
                show_error(response);
            }
        },'json');
    });
}
