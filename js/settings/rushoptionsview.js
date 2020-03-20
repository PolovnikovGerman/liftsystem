function init_rushoptions_page() {
    init_settings();
    // Change Brand
    $("#rushoptionsviewbrandmenu").find("div.left_tab").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#rushoptionsviewbrand").val(brand);
        $("#rushoptionsviewbrandmenu").find("div.left_tab").removeClass('active');
        $("#rushoptionsviewbrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
        init_settings();
    });
}

function init_settings() {
    var datqry=new Date().getTime();
    $.post('/settings/settingsdata', {'datq':datqry}, function(response){
        if (response.errors=='') {
            $("div#configview").empty().html(response.data.content);
            $("div.config_datarow div.config_acltions").click(function(){
                var config = $(this).data('config');
                edit_config(config);
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function edit_config(config_id) {
    var datqry=new Date().getTime();
    var url='/settings/edit_config';
    $.post(url, {'config_id':config_id,'datq':datqry}, function(response){
        if (response.errors=='') {
            $("div#cfgrow"+config_id).empty().html(response.data.content);
            $("div.config_datarow div.config_acltions").unbind('click');
            $("a#saveconfig").click(function(){
                save_config();
            });
            $("a#closeconfig").click(function(){
                cancel_configedit();
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function save_config() {
    var config_alias=$("#config_alias").val();
    var config_value=$("#config_value").val();
    var config_id=$("#config_id").val();
    var url="/settings/saveconfig";
    var datqry=new Date().getTime();
    $.post(url, {'config_alias':config_alias,'config_value':config_value,'config_id':config_id,'datq':datqry}, function(response){
        if (response.errors=='') {
            $("div#configview").empty().html(response.data.content);
            $("div.config_datarow div.config_acltions").click(function(){
                var config=$(this).data('config');
                edit_config(config);
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}
function cancel_configedit() {
    var datqry=new Date().getTime();
    $.post('/settings/settingsdata', {'datq':datqry}, function(response){
        if (response.errors=='') {
            $("div#configview").empty().html(response.data.content);
            $("div.config_datarow div.config_acltions").click(function(){
                var config=$(this).data('config');
                edit_config(config);
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');

}

function edit_specitem(obj) {
    var item_id=obj.id.substr(6);
    var url="/otherpages/edit_specitem";
    var datqry=new Date().getTime();
    $.post(url, {'item_id':item_id,'datq':datqry}, function(response){
        if (response.errors=='') {
            $("#itmrow"+item_id).empty().html(response.data.content);
            $("div.itmdata-action").unbind('click');
            $("a#savespecitm").click(function(){
                save_specitem();
            })
            $("a#closespecitm").click(function(){
                close_itemedt();
            });
            /* Change picture */
            $("#selectitem").change(function(){
                change_itmimg();
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json')
}

function save_specitem() {
    var newitem=$("#selectitem").val();
    var olditem=$("#old_item").val();
    var datqry=new Date().getTime();
    var url="/otherpages/save_specitem";
    $.post(url, {'newitem':newitem, 'olditem':olditem,'datq':datqry}, function(response){
        if (response.errors=='') {
            $("div.items_homepage").empty().html(response.data.content);
            $("div.itmdata-action").click(function(){
                edit_specitem(this);
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function close_itemedt() {
    var url="/otherpages/cancel_specedit";
    var datqry=new Date().getTime();
    $.post(url, {'datq':datqry}, function(response){
        if (response.errors=='') {
            $("div.items_homepage").empty().html(response.data.content);
            $("div.itmdata-action").click(function(){
                edit_specitem(this);
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');

}

function change_itmimg() {
    var newitem=$("#selectitem").val();
    var olditem=$("#old_item").val();
    var datqry=new Date().getTime();
    var url="/otherpages/change_itemimg";
    $.post(url, {'item_id':newitem,'datq':datqry}, function(response){
        if (response.errors=='') {
            $("#itmimg"+olditem+" img").prop('src',response.data.newimg);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}