/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Init Inventory
var invsalslidermargin;
var invsalsliderwidth=300;

$(document).ready(function(){
    invsalslidermargin=parseInt($("div.inventsalesreport").find("div.after_head").css('margin-left'));
});

function init_invsalesreport_content() {
    init_invsalesreport();
    // Change Brand
    $("#inventsalesreportbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#inventsalesreportbrand").val(brand);
        $("#inventsalesreportbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#inventsalesreportbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#inventsalesreportbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        search_invsalesreport_brand();
    });
}

function search_invsalesreport_brand() {

}

function init_invsalesreport() {
    var params = new Array();
    // {'salesreport': 1}
    params.push({name: 'salesreport', value: 1});
    params.push({name: 'brand', value: $("#inventsalesreportbrand").val()});
    var url = "/fulfillment/inventory_data";
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("#inventsalesrep").find("div.inventorytableleft").empty().html(response.data.totalinvcontent);
            $("#inventsalesrep").find("div.inventoryonboatarea").empty().html(response.data.onboatcontent);
            $("#inventsalesrep").find("div.inventorytableright").empty().html(response.data.speccontent);
            if (parseInt(response.data.margin) >= 0) {
                $("#inventsalesrep").find("div.left_arrow").removeClass('active');
            } else {
                $("#inventsalesrep").find("div.left_arrow").addClass('active');
            }
            init_salesrepslider_move();
            init_salesreport_view();
            $("#loader").hide();
        } else {
            show_error(response);
        }
    },'json');
}

function init_salesrepslider_move() {
    $("#inventsalesrep").find("div.right_arrow").unbind('click').click(function() {
        if($(this).hasClass('active')) {
            var offset = -60;
            salesrepslider_move(offset);
        }
    });
    $("#inventsalesrep").find("div.left_arrow").unbind('click').click(function() {
        if($(this).hasClass('active')) {
            var offset = 60;
            salesrepslider_move(offset);
        }
    });
}

function salesrepslider_move(offset) {
    var margin=parseInt($("#inventsalesrep").find("div.after_head").css('margin-left'));
    var slwidth=parseInt($("#inventsalesrep").find("div.after_head").css('width'));
    var newmargin=(margin+offset);
    if (newmargin>=0) {
        newmargin=0;
        $("#inventsalesrep").find("div.left_arrow").removeClass('active');
    } else {
        $("#inventsalesrep").find("div.left_arrow").addClass('active');
    }

    if ((slwidth+newmargin)>invsalsliderwidth) {
        $("#inventsalesrep").find("div.right_arrow").addClass('active');
    } else {
        $("#inventsalesrep").find("div.right_arrow").removeClass('active');
    }
    $("#inventsalesrep").find("div.after_head").animate({marginLeft:newmargin+'px'},'slow',function(){
        var margin=parseInt($("#inventsalesrep").find("div.after_head").css('margin-left'));
        var slwidth=parseInt($("#inventsalesrep").find("div.after_head").css('width'));
        if ((slwidth+margin)>invsalsliderwidth) {
            $("#inventsalesrep").find("div.right_arrow").addClass('active');
        } else {
            $("#inventsalesrep").find("div.right_arrow").removeClass('active');
        }

    });
    init_salesrepslider_move();
}

function init_salesreport_view() {
    // Show active Item / Color
    $("#inventsalesrep").find("div.coloritemname").hover(
        function() {
            $( this ).addClass('active');
        }, function() {
            $( this ).removeClass('active');
        }
    );

    // Download Plate
    $("#inventsalesrep").find("div.platetempdata.full").find('i').unbind('click').click(function(){
        var color = $(this).parent('div.platetempdata').data('color');
        var type='plate';
        init_salesrepdownload(color, type);
    });
    // Download Proof
    $("#inventsalesrep").find("div.prooftempdata.full").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.prooftempdata').data('color');
        var type='proof';
        init_salesrepdownload(color, type);
    });
    // Download Proof
    $("#inventsalesrep").find("div.itemlabel.full").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.itemlabel').data('color');
        var type='itemlabel';
        init_salesrepdownload(color, type);
    });
    // Download Pics
    $("#inventsalesrep").find("div.picsdata").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.picsdata').data('color');
        init_salesrepdownload_pics(color);
    });
    // Show Pantone Color
    /* $("#inventsalesrep").find("div.specsdata.full").each(function(){
        $(this).bt({
            fill: '#ffffff',
            trigger: 'hover',
            width: '200px',
            ajaxCache: false,
            positions: ['left'],
            ajaxPath: ["$(this).attr('href')"]
        });
    }); */
    // Download Excell file of OnBoat container
    $("#inventsalesrep").find("div.download_link").unbind('click').click(function(){
        var date = $(this).data('download');
        var params=new Array();
        params.push({name: 'onboat_container', value: date});
        var url="/fulfillment/inventory_boat_download";
        $.post(url, params, function(response){
            if (response.errors=='') {
                var link = response.data.url;
                window.open(link, 'win', 'width=500,height=500,toolbar=0');
            }
        },'json');
    });
}

function init_salesrepdownload(printshop_item_id, type) {
    var url="/fulfillment/inventory_plate_download";
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

function init_salesrepdownload_pics(printshop_color_id) {
    var url="/fulfillment/inventory_pics_download";
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