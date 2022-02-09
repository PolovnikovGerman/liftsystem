function init_accounts_receivable() {
    init_accreceive_totals();
}

function init_accreceive_totals() {
    var params = new Array();
    params.push({name: 'brand', value: $("#accreceivebrand").val()});
    params.push({name: 'period', value: $(".accreceiv-period-select").val()});
    var url = '/accounting/accountreceiv_totals';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $(".accreceiv-totals").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    },'json');
}