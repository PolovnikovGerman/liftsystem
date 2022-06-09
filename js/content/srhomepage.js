function init_srhomepage_editcontent() {
    // Cancel Edit
    $(".cancel_button[data-page='home']").unbind('click').click(function () {
        init_contentpage('custom', 'SR');
    });
    // Save
    $(".save_button[data-page='home']").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'session', value: $("#custom_session").val()});
        params.push({name:'brand', value: 'SR'});
        var url="/content/save_customcontent";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                init_contentpage('home', 'SR');
            } else {
                show_error(response);
            }
        },'json');
    });
}