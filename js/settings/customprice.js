function init_customprice_page() {
    show_customprice_content();
}
function show_customprice_content() {
    var url = "/admin/customprices";
    $.post(url, {}, function(response) {
        if (response.errors=='') {
            $(".custompricetablebody").empty().html(response.data.content);
            init_customprice_manage();
        } else {
            show_error(response);
        }
    },'json')
}

function init_customprice_manage() {
    $(".custompriceaddbtn").unbind("click").click(function () {
        customprice_edit(0);
    });
    $(".custompriceedit").unbind("click").click(function () {
        var priceid = $(this).data("price");
        customprice_edit(priceid);
    });
    $(".custompricedelete").unbind("click").click(function () {
        if (confirm("Are you sure you want to delete?")==true) {
            var priceid = $(this).data("price");
            var url = "/admin/customprice_delete";
            $.post(url, {'price_id': priceid}, function(response) {
                if (response.errors=='') {
                    $(".custompricetablebody").empty().html(response.data.content);
                    init_customprice_manage();
                } else {
                    show_error(response);
                }
            },'json')
        }
    })

}

function customprice_edit(priceid) {
    var url = "/admin/customprice_edit"
    $.post(url, {'price_id': priceid}, function(response) {
        if (response.errors=='') {
            if (parseInt(priceid) ==0) {
                $(".custompricetablebody").prepend(response.data.content);
            } else {
                $(".pricedatarow[data-price='"+priceid+"']").empty().html(response.data.content);
            }
            $(".custompriceedit").unbind("click");
            $(".custompricedelete").unbind("click");
            $(".custompriceaddbtn").unbind("click");
            init_customprice_edit(priceid);
        } else {
            show_error(response);
        }
    },'json')
}

function init_customprice_edit(priceid) {
    $(".custompricesave").unbind("click").click(function () {
        var params = new Array();
        params.push({name: 'price_id', value: priceid});
        params.push({name: 'qty', value: $("input#quantity").val()});
        params.push({name: 'price', value: $("input#customprice").val()});
        var url = '/admin/customprice_save';
        $.post(url, params, function(response) {
            if (response.errors=='') {
                $(".custompricetablebody").empty().html(response.data.content);
                init_customprice_manage();
            } else {
                show_error(response);
            }
        }, 'json')
    });
    $(".custompricecancel").unbind("click").click(function () {
        show_customprice_content();
    })
}