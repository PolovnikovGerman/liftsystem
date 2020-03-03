// Init Inventory
var needslidermargin;
var needsliderwidth=180;
$(document).ready(function(){
    needslidermargin=parseInt($("div.needinventlist").find("div.after_head").css('margin-left'));
});

function init_needinvlist_content(mainurl) {
    init_needinvlist(mainurl);
    $("#inventoryneedlistbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#inventoryneedlistbrand").val(brand);
        $("#inventoryneedlistbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#inventoryneedlistbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#inventoryneedlistbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_inventoryneed_brand();
    });
}

function search_inventoryneed_brand() {
    var params = new Array();
    params.push({name: 'brand', value: $("#inventoryneedlistbrand").val()});
    var url = '/fulfillment/invneedlist_brand';
    $.post(url, params, function (response) {
        if (response.errors=='') {
            $("#invneedlistonboathead").empty().html(response.data.onboat_content);
            $("#invneedlistonboatfoot").empty().html(response.data.download_view).css('width',response.data.width+'px').css('margin-left',response.data.margin+'px');
            init_needinvlist('fulfillment');
        } else {
            show_error(response);
        }
    },'json');
}

function init_needinvlist(mainurl) {
    var params = new Array();
    params.push({name: 'brand', value: $("#inventoryneedlistbrand").val()});
    var url = "/"+mainurl+"/datalist";
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("div.needinventlist").find("div.inventorytableleft").empty().html(response.data.totalinvcontent);
            $("div.needinventlist").find("div.inventoryonboatarea").empty().html(response.data.onboatcontent);
            $("div.needinventlist").find("div.inventorytableright").empty().html(response.data.speccontent);
            if (parseInt(response.data.margin) >= 0) {
                $("div.needinventlist").find("div.left_arrow").removeClass('active');
            } else {
                $("div.needinventlist").find("div.left_arrow").addClass('active');
            }
            init_needslider_move();
            init_needinventory_view(mainurl);
            $("#loader").hide();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_needslider_move() {
    $("div.needinventlist").find("div.right_arrow").unbind('click').click(function() {
        if($(this).hasClass('active')) {
            var offset = -60;
            needslider_move(offset);
        }
    });
    $("div.needinventlist").find("div.left_arrow").unbind('click').click(function() {
        if($(this).hasClass('active')) {
            var offset = 60;
            needslider_move(offset);
        }
    });
}

function needslider_move(offset) {
    var margin=parseInt($("div.needinventlist").find("div.after_head").css('margin-left'));
    var slwidth=parseInt($("div.needinventlist").find("div.after_head").css('width'));
    var newmargin=(margin+offset);
    if (newmargin>=0) {
        newmargin=0;
        $("div.needinventlist").find("div.left_arrow").removeClass('active');
    } else {
        $("div.needinventlist").find("div.left_arrow").addClass('active');
    }
    if ((slwidth+newmargin)>needsliderwidth) {
        $("div.needinventlist").find("div.right_arrow").addClass('active');
    } else {
        $("div.needinventlist").find("div.right_arrow").removeClass('active');
    }
    $("div.needinventlist").find("div.after_head").animate({marginLeft:newmargin+'px'},'slow',function(){
        var margin=parseInt($("div.needinventlist").find("div.after_head").css('margin-left'));
        var slwidth=parseInt($("div.needinventlist").find("div.after_head").css('width'));
        if ((slwidth+margin)>needsliderwidth) {
            $("div.needinventlist").find("div.right_arrow").addClass('active');
        } else {
            $("div.needinventlist").find("div.right_arrow").removeClass('active');
        }
    });
    init_needslider_move();
}

function init_needinventory_view(mainurl) {
    // Download Excell file of OnBoat container
    $("div.needinventlist").find("div.download_link").unbind('click').click(function(){
        var date = $(this).data('download');
        var params=new Array();
        params.push({name: 'onboat_container', value: date});
        var url="/"+mainurl+"/inventory_boat_download";
        $.post(url, params, function(response){
            if (response.errors=='') {
                var link = response.data.url;
                window.open(link, 'win', 'width=500,height=500,toolbar=0');
            }
        },'json');
    });
    // Show Pantone Color
    $("div.needinventlist").find("div.specsdata.full").qtip({
        content: {
            attr: 'data-content'
        },
        position: {
            my: 'right center',
            at: 'center left',
        },
        style: {
            classes: 'colordata_tooltip'
        }
    });
    // Download Plate
    $("div.needinventlist").find("div.platetempdata.full").find('i').unbind('click').click(function(){
        var color = $(this).parent('div.platetempdata').data('color');
        var type='plate';
        init_needlistdownload(color, type, mainurl);
    });
    // Download Proof
    $("div.needinventlist").find("div.prooftempdata.full").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.prooftempdata').data('color');
        var type='proof';
        init_needlistdownload(color, type, mainurl);
    });
    // Download Proof
    $("div.needinventlist").find("div.itemlabel.full").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.itemlabel').data('color');
        var type='itemlabel';
        init_needlistdownload(color, type, mainurl);
    });
    // Download Pics
    $("div.needinventlist").find("div.picsdata").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.picsdata').data('color');
        init_needlistdownload_pics(color, mainurl);
    });
}

function init_needlistdownload_pics(printshop_color_id, mainurl) {
    var url="/"+mainurl+"/pics_download";
    var params=[];
    var fileur =[];
    params.push({name:'printshop_color_id', value:printshop_color_id});
    $.post(url, params, function(response) {
        if(response.errors=='') {
            fileur = response.data.fileurl;
            var link;
            var win=0;
            fileur.forEach(function(fileurl, fileur) {
                win++;
                link = fileurl;
                window.open(link, win, 'width=500,height=500,toolbar=0');
            });

        } else {
            show_error(response);
        }
    }, 'json');
}

function init_needlistdownload(printshop_item_id, type, mainurl) {
    var url="/"+mainurl+"/plate_download";
    var params=[];
    params.push({name:'type', value: type});
    params.push({name:'printshop_item_id', value:printshop_item_id});
    $.post(url, params, function(response) {
        if(response.errors=='') {
            window.open(response.data.fileurl, response.data.filename, 'width=500,height=500,toolbar=0');
        } else {
            show_error(response);
        }
    }, 'json');
}
