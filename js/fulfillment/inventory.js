// Init Inventory
var slidermargin;
var color_list = {};
var sliderwidth=240;

$(document).ready(function(){
    slidermargin=parseInt($("div.after_head").css('margin-left'));
});

function init_inventory_data() {
    var viewtype=$("input#invpageview").val();
    var url = "/fulfillment/inventory_data";
    $("#loader").show();
    var params = new Array();
    params.push({name: 'brand', value: $("#printshopinventbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            $("#printshopinventor").find("div.inventorytableleft").empty().html(response.data.totalinvcontent);
            $("#printshopinventor").find("div.inventoryonboatarea").empty().html(response.data.onboatcontent);
            $("#printshopinventor").find("div.inventorytableright").empty().html(response.data.speccontent);
            $("div#curinvtotal").empty().html(response.data.total_inventory);
            if (parseInt(response.data.margin) >= 0) {
                $("div.left_arrow").removeClass('active');
            } else {
                $("div.left_arrow").addClass('active');
            }
            if ($("input#showonlinemaxvalue").val()==1) {
                showmaxevent_data();
            }
            init_slider_move();
            init_inventory_view();
            $("#loader").hide();
        } else {
            show_error(response);
        }
    }, 'json');
}

function showmaxevent_data() {
    $("#printshopinventor").find("div.maxinvent").show();
    $("#printshopinventor").find("div.inventorydatarow").css('width','506px');
}
var specfile;
function init_inventory_view() {
    // Show active Item / Color
    $("#printshopinventor").find("div.coloritemname").hover(
        function() {
            $( this ).addClass('active');
        }, function() {
            $( this ).removeClass('active');
        });
    // Change Add'l cost
    $("#printshopinventor").find("input#invaddvcost").unbind('change').change(function(){
        var url="/fulfillment/inventory_addcost";
        var cost=$(this).val();
        $.post(url, {'cost': cost}, function(response){
            if (response.errors=='') {
                // $("div.inventorytablebody").empty().html(response.data.content);
                $("#printshopinventor").find("div.inventorytableright").empty().html(response.data.speccontent);
                init_inventory_view();
            } else {
                show_error(response);
            }
        },'json');
    })
    // Add Color
    $("#printshopinventor").find("div.inventorydatarow").find('div.additemcolor').unbind('click').click(function(){
        var item=$(this).data('item');
        var params=new Array();
        params.push({name: 'printshop_item_id', value: item});
        params.push({name: 'printshop_color_id', value: 0});
        params.push({name: 'showmax', value: $("input#showonlinemaxvalue").val()});
        var url="/fulfillment/inventory_color";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // ??????
                // $("div.inventorydatarow.itemdata[data-item='"+item+"']").after('<div class="inventorydatarow">'+response.data.content+'</div>');
                $("#printshopinventor").find("div.inventorytableleft").find("div.inventorydatarow[data-color='0'][data-item='"+item+"']").after('<div class="inventorydatarow">'+response.data.commoncontent+'</div>');
                //         .empty().html(response.data.commoncontent);
                $("#printshopinventor").find("div.inventorytableright").find("div.inventorydatarow[data-color='0'][data-item='"+item+"']").after('<div class="inventorydatarow">'+response.data.addcontent+'</div>');
                // .empty().html(response.data.addcontent);
                $("#printshopinventor").find("div.inventorydatarow").find('div.additemcolor').unbind('click');
                $("#printshopinventor").find("div.additem").unbind('click');
                $("#printshopinventor").find("div.coloritemname").unbind('click');
                $("#printshopinventor").find("input.colorname").focus();
                $("#printshopinventor").find("div.picsdata").find('i').unbind('click').click(function(){
                    add_pics();
                });
                $("#printshopinventor").find("div.specsdata").find('i').unbind('click').click(function(){
                    /*var color=$(this).parent('div.specsdata').data('color');*/
                    var params = {
                        uploadsession :  $("input#uploadsession").val(),
                        specfile: $("textarea.specfile").val(),
                    };
                    var url="/fulfillment/inventory_specedit";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            show_popup('stockdataarea');
                            $("div#pop_content").empty().html(response.data.content);
                            //init_inventspec(color);
                            $('div.savedata').unbind('click').click(function(){
                                specfile = $("textarea.specfile").val();
                                disablePopup();
                            });
                        } else {
                            show_error(response);
                        }
                    },'json');
                });
                save_inventitem_color()
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add Item
    $("#printshopinventor").find("div.additem").unbind('click').click(function(){
        var url="/fulfillment/inventory_item";
        var params=new Array();
        var item=0;
        params.push({name: 'printshop_item_id', value: 0});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#printshopinventor").find(".inventorytablebody").scrollTop();
                $("#printshopinventor").find("div.inventorytableleft div.inventorydatarow:first-child").before('<div class="inventorydatarow itemdata">'+response.data.content+'</div>');
                $("#printshopinventor").find("div.inventorytableright div.inventorydatarow:first-child").before('<div class="inventorydatarow itemdata">'+response.data.addcontent+'</div>');

                $("#printshopinventor").find("div.inventorydatarow").find('div.additemcolor').unbind('click');
                $("#printshopinventor").find("div.additem").unbind('click');
                $("#printshopinventor").find("div.coloritemname").unbind('click');
                $("#printshopinventor").find("div.platetempdata").find('i.active').click(function(){
                    var format = ['ai','AI'];
                    add_platetemp(item, "","", format);
                });
                $("#printshopinventor").find("div.prooftempdata").find('i.active').click(function(){
                    var format = ['ai', 'pdf', 'AI', 'PDF'];
                    add_platetemp("", item,"", format);
                });
                $("#printshopinventor").find("div.itemlabel").find('i.active').click(function(){
                    var format = ['jpg', 'JPG', 'jpeg', 'JPEG'];
                    add_platetemp("","", item, format);
                });
                $("#printshopinventor").find("input.itemnum").focus();
                save_inventitem();
            } else {
                show_error(response);
            }
        },'json');
    });
    //  Edit Item
    $("#printshopinventor").find("div.edititem").unbind('click').click(function(){
        var url="/fulfillment/inventory_item";
        var item=$(this).data('item');
        var params=new Array();
        params.push({name: 'printshop_item_id', value: item});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#printshopinventor").find("div.inventorytableleft").find("div.inventorydatarow.itemdata[data-item='"+item+"']").empty().html(response.data.content);
                $("#printshopinventor").find("div.inventorytableright").find("div.inventorydatarow.itemdata[data-item='"+item+"']").empty().html(response.data.addcontent);
                $("#printshopinventor").find("div.inventorydatarow").find('div.additemcolor').unbind('click');
                $("#printshopinventor").find("div.additem").unbind('click');
                $("#printshopinventor").find("div.coloritemname").unbind('click');
                $("#printshopinventor").find("div.platetempdata").find('i.active').click(function(){
                    var format = ['ai','AI'];
                    add_platetemp(item, "","", format);
                });
                $("#printshopinventor").find("div.prooftempdata").find('i.active').click(function(){
                    var format = ['ai', 'pdf', 'AI', 'PDF'];
                    add_platetemp("", item,"", format);
                });
                $("#printshopinventor").find("div.itemlabel").find('i.active').click(function(){
                    var format = ['jpg', 'JPG', 'jpeg', 'JPEG'];
                    add_platetemp("","", item, format);
                });
                $("#printshopinventor").find("input.itemnum").focus();
                save_inventitem();
            } else {
                show_error(response);
            }
        },'json');
    })
    // Edit Color
    $("#printshopinventor").find("div.editcolor").unbind('click').click(function(){
        var color=$(this).data('color');
        var item=$(this).data('item');
        var params=new Array();
        params.push({name: 'printshop_item_id', value: item});
        params.push({name: 'printshop_color_id', value: color});
        // params.push({name: 'uplsess', value: $(this).parents('.inventorydatarow').find('input#uploadsession').val()});
        params.push({name: 'showmax', value: $("input#showonlinemaxvalue").val()});
        var url="/fulfillment/inventory_color";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // place
                $("#printshopinventor").find("div.inventorytableleft").find("div.inventorydatarow[data-color='"+color+"']").empty().html(response.data.commoncontent);
                $("#printshopinventor").find("div.inventorytableright").find("div.inventorydatarow[data-color='"+color+"']").empty().html(response.data.addcontent);
                // $("div.inventorydatarow[data-color='"+color+"']").empty().html(response.data.content);
                $("#printshopinventor").find("div.inventorydatarow").find('div.additemcolor').unbind('click');
                $("#printshopinventor").find("div.additem").unbind('click');
                $("#printshopinventor").find("div.coloritemname").unbind('click');
                $("#printshopinventor").find("input.colorname").focus();
                $("#printshopinventor").find("div.picsdata").find('i').unbind('click').click(function(){
                    /*var color=$(this).parent('div.picsdata').data('color');*/
                    add_pics();
                });
                $("#printshopinventor").find("div.specsdata").find('i').unbind('click').click(function(){
                    /*var color=$(this).parent('div.specsdata').data('color');*/
                    var params=new Array();
                    params.push({name: 'uploadsession', value: $('input#uploadsession').val()});
                    var url="/fulfillment/inventory_specedit";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            show_popup('stockdataarea');
                            $("div#pop_content").empty().html(response.data.content);
                            //init_inventspec(color);
                            $('div.savedata').unbind('click').click(function(){
                                var params = {
                                    'fldname' : 'specfile',
                                    'newval' : $("textarea.specfile").val(),
                                    // 'uploadsession' : $(this).parents('.inventorydatarow').find('input#uploadsession').val()
                                    'uploadsession' : $('input#uploadsession').val()
                                };
                                var url="/fulfillment/inventory_color_change";
                                $.post(url, params, function(response){
                                    if (response.errors=='') {
                                        $("div.specsdata[data-color='"+color+"']").find('i').removeClass('empty').addClass(response.data.specclass);
                                        if(specfile == '') {
                                            $("#printshopinventor").find("div.specsdata[data-color='"+color+"']").find('i').addClass(response.data.specclass);
                                        }
                                    } else {
                                        show_error(response);
                                    }
                                },'json');
                                disablePopup();
                            });
                        } else {
                            show_error(response);
                        }
                    },'json');
                });
                save_inventitem_color()
            } else {
                show_error(response);
            }
        },'json');
    });
    // Stock Edit
    $("#printshopinventor").find("div.coloritemname").unbind('click').click(function(){
        var color=$(this).data('color');
        var params=new Array();
        params.push({name: 'printshop_color_id', value: color});
        var url="/fulfillment/inventory_colorstock";
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('stockdataarea');
                $("div#pop_content").empty().html(response.data.content);
                init_inventdatastock(color);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Download Plate
    $("#printshopinventor").find("div.platetempdata.full").find('i').unbind('click').click(function(){
        var color = $(this).parent('div.platetempdata').data('color');
        var type='plate';
        init_download(color, type);
    });
    // Download Proof
    $("#printshopinventor").find("div.prooftempdata.full").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.prooftempdata').data('color');
        var type='proof';
        init_download(color, type);
    });
    // Download Proof
    $("#printshopinventor").find("div.itemlabel.full").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.itemlabel').data('color');
        var type='itemlabel';
        init_download(color, type);
    });
    // Download Pics
    $("#printshopinventor").find("div.picsdata").find('i').unbind('click').click(function(){
        var color=$(this).parent('div.picsdata').data('color');

        init_download_pics(color);
    });
    // Show Pantone Color
    $("#printshopinventor").find("div.specsdata.full").each(function(){
        $(this).bt({
            fill: '#ffffff',
            trigger: 'hover',
            width: '200px',
            ajaxCache: false,
            positions: ['left'],
            ajaxPath: ["$(this).attr('href')"]
        });
    });
    // Show Max parameter per item
//    $("div.itempercent").each(function(){
//        $(this).bt({
//            fill: '#ffffff',
//            trigger: 'hover',
//            width: '300px',
//            ajaxCache: false,
//            positions: ['top'],
//            ajaxPath: ["$(this).attr('href')"]
//        });
//    });

    // Download Excell file of OnBoat container
    $("#printshopinventor").find("div.download_link").unbind('click').click(function(){
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
    // Edit Container
    $("#printshopinventor").find("i.edit_onboat").unbind('click').click(function(){
        var container=$(this).parent('div.onboatmanage').data('container');
        var url="/fulfillment/inventory_changecontainer";
        $.post(url, {'container': container}, function(response){
            if (response.errors=='') {
                // Lets go
                $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").find('div.containrermanage').empty().html(response.data.managecontent);
                $("#printshopinventor").find("div.onboacontainerarea[data-container='"+container+"']").empty().html(response.data.containercontent);
                $("#printshopinventor").find("input.boatcontainerdate[data-container='"+container+"']").datepicker();
                init_change_onboatcontainer(container);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add container
    $("#printshopinventor").find("span.add_onboat").unbind('click').click(function(){
        var container=0;
        var params=new Array();
        params.push({name: 'container', value: container});
        params.push({name: 'showmax', value: $("input#showonlinemaxvalue").val()});
        var url="/fulfillment/inventory_changecontainer";
        $.post(url, params, function(response){
            container=response.data.onboat_container;
            $("#printshopinventor").find("div.inventorytablehead").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.containerhead);
            $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").find('div.containrermanage').empty().html(response.data.managecontent);
            $("#printshopinventor").find("div.inventoryonboatarea").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width).append(response.data.containercontent);
            $("#printshopinventor").find("div.boat_download").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width);
            $("#printshopinventor").find("input.boatcontainerdate[data-container='"+container+"']").datepicker();
            init_change_onboatcontainer(container);
        },'json');
    });
    // Waiting button
    $("#printshopinventor").find("div.waitarrive").unbind('click').click(function(){
        var container=$(this).data('container');
        var msg='Are You Sure You Want to Mark This As Arrived?';
        if (confirm(msg)==true) {
            var url="/fulfillment/inventory_arrivecontainer";
            var params=new Array();
            params.push({name: 'onboat_container', value: container});
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").empty().html(response.data.containerhead);
                    $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").addClass('arrived');
                    $("#printshopinventor").find("div.onboacontainerdata[data-container='"+container+"']").addClass('arrived');
                    $("#printshopinventor").find("div.inventorytableleft").empty().html(response.data.totalinvcontent);
                    $("div#inventtotal").empty().html(response.data.totalinvview);
                    $("div#curinvtotal").empty().html(response.data.total_inventory);
                    init_inventory_view();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    // Show hide Max value
    $("#printshopinventor").find("div.showonlinemaxvalue").unbind('click').click(function(){
        // current state
        $("#loader").show();
        var curstate=$("input#showonlinemaxvalue").val();
        var newmsg='[show max]';
        var curmarge=parseInt($("div.inventorytablehead").find('div.after_head').css('margin-left'));
        var totalinvwidth=437;
        var totalinvsumwidth=450;
        var invdatawidth=448;
        var onboatwidth=240;
        var onboatheadwidth=280;
        var totcostmargin=282;
        var inventoryonboatwidth=283;
        // var availbodywidth=58;
        var boatdownloadwidth=475;
        if (curstate==0) {
            newmsg='[hide max]';
            $("input#showonlinemaxvalue").val(1);
            curmarge=curmarge-60;
            totalinvwidth=totalinvwidth+60;
            totalinvsumwidth=totalinvsumwidth+60;
            invdatawidth=invdatawidth+60;
            onboatwidth=onboatwidth-60;
            onboatheadwidth=onboatheadwidth-60;
            totcostmargin=totcostmargin-60;
            inventoryonboatwidth=inventoryonboatwidth-60;
            // availbodywidth=66;
            boatdownloadwidth=boatdownloadwidth+60;
            sliderwidth=sliderwidth-60;
            $("#printshopinventor").find("div.totalinventory").css('width','507px');
        } else {
            $("input#showonlinemaxvalue").val(0);
            curmarge=curmarge+60;
            if (curmarge>0) {
                curmarge=0;
            }
            sliderwidth=sliderwidth+60;
        }

        if (parseInt(curmarge) >= 0) {
            $("#printshopinventor").find("div.left_arrow").removeClass('active');
        } else {
            $("#printshopinventor").find("div.left_arrow").addClass('active');
        }
        var realsliderwidth=parseInt($("#printshopinventor").find("div.inventoryonboatarea").find('div.after_head').css('width'));
        if (realsliderwidth<=sliderwidth) {
            $("#printshopinventor").find("div.right_arrow").removeClass('active');
        }

        $("#printshopinventor").find("div.showonlinemaxvalue").empty().html(newmsg);
        $("#printshopinventor").find("div.inventorytablehead").find('div.after_head').css('margin-left',curmarge);
        $("#printshopinventor").find("div.inventoryonboatarea").find('div.after_head').css('margin-left',curmarge);
        $("#printshopinventor").find("div.boat_download").find('div.after_head').css('margin-left', curmarge);
        $("#printshopinventor").find("div.totalinventory").css('width',totalinvwidth);
        $("#printshopinventor").find("div.inventorytableleft").css('width',totalinvwidth);
        $("#printshopinventor").find("div.totalinventorysum").css('width', totalinvsumwidth)
        $("#printshopinventor").find("div.inventorydatarow").css('width',invdatawidth);
        $("#printshopinventor").find("div.onboatareas").css('width', onboatwidth);
        $("#printshopinventor").find("div.onboatdataareas").css('width', onboatwidth);
        $("#printshopinventor").find("div.headonboat").css('width',onboatheadwidth);
        $("#printshopinventor").find("div.inventoryonboatarea").css('width',inventoryonboatwidth);
        $("#printshopinventor").find("div.totcoseasum").css('margin-left',totcostmargin);
        $("#printshopinventor").find("div.boat_download").find("div.left_block").css('width', boatdownloadwidth);
        // $("div.inventorytablebody").find("div.available").css('width',availbodywidth);
        $("#printshopinventor").find("div.maxinvent").toggle();
        init_inventory_view();
        init_slider_move();
        $("#loader").hide();
    });
    // Export to file
    $("div.printshopexporttoexcel").unbind('click').click(function(){
        var url="/fulfillment/inventory_export";
        var params=new Array();
        $("#loader").show();
        $.post(url, params, function(response){
            if (response.errors=='') {
                window.open(response.data.url);
                $("#loader").hide();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function init_change_onboatcontainer(container) {
    // Switch off other edit and add
    $("#printshopinventor").find("span.add_onboat").unbind('click');
    $("#printshopinventor").find("i.edit_onboat").unbind('click');
    $("#printshopinventor").find("div.download_link").unbind('click').css('opacity','0.4').css('cursor','default');
    // Change Color Value
    $("#printshopinventor").find("input.onboatelementinput").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("input#onboatsession").val()});
        params.push({name: 'color', value: $(this).data('color')});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'entity', value: 'color'});
        var url="/fulfillment/inventory_editcontainer";
        $.post(url, params, function(response){
            if (response.errors=='') {
                // New total
                $("#printshopinventor").find("div.containertotal[data-container='"+container+"']").empty().html(response.data.total);
                $("#printshopinventor").find("div.totalitem[data-item='"+response.data.item+"']").empty().html(response.data.totalitem);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Change date of Arrive
    $("#printshopinventor").find("input.boatcontainerdate[data-container='"+container+"']").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'session', value: $("input#onboatsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'entity', value: 'boatcontainerdate'});
        var url="/fulfillment/inventory_editcontainer";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Save
    $("#printshopinventor").find("i.saveboatcontainer").unbind('click').click(function(){
        saveonboat_container(container);
    });
    $("#printshopinventor").find("i.cancelboatcontainer").unbind('click').click(function(){
        cancelonboat_container(container);
    });

}

function saveonboat_container(container) {
    var params=new Array();
    params.push({name: 'session', value: $("input#onboatsession").val()});
    params.push({name: 'container', value: container});
    params.push({name: 'action', value: 'save'});
    params.push({name: 'showmax', value: $("input#showonlinemaxvalue").val()});
    var url="/fulfillment/inventory_savecontainer";
    $.post(url, params, function(response){
        if (response.errors=='') {
            if (parseInt(response.data.delete)==1) {
                $("#printshopinventor").find("div.headonboat").find('div.after_head').empty().html(response.data.headview).css('margin-left',response.data.margin).css('width',response.data.width);
                $("#printshopinventor").find("div.boat_download").find('div.after_head').empty().html(response.data.download_view).css('margin-left',response.data.margin).css('width',response.data.width);
                $("#printshopinventor").find("div.inventoryonboatarea").empty().html(response.data.onboatcontent);
                $("#printshopinventor").find('div.after_head').css('margin-left',response.data.margin).css('width',response.data.width);
                if (parseInt(response.data.margin) >= 0) {
                    $("#printshopinventor").find("div.left_arrow").removeClass('active');
                } else {
                    $("#printshopinventor").find("div.left_arrow").addClass('active');
                }
                init_slider_move();
            } else {
                if (container<0) {
                    $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").remove();
                    $("#printshopinventor").find("div.onboacontainerarea[data-container='"+container+"']").remove();
                    // Add new
                    $("#printshopinventor").find('div.after_head').css('margin-left',response.data.marginleft).css('width',response.data.width);
                    $("#printshopinventor").find("div.inventorytablehead").find('div.after_head').append(response.data.containerhead);
                    $("#printshopinventor").find("div.inventoryonboatarea").find('div.after_head').append(response.data.containercontent);
                    $("#printshopinventor").find("div.boat_download").find('div.after_head').empty().html(response.data.downloadview);
                    if (parseInt(response.data.marginleft)==0) {
                        $("#printshopinventor").find("div.left_arrow").removeClass('active');
                    } else {
                        $("#printshopinventor").find("div.left_arrow").addClass('active');
                    }
                } else {
                    $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").empty().html(response.data.containerhead);
                    $("#printshopinventor").find("div.onboacontainerarea[data-container='"+container+"']").empty().html(response.data.containercontent);
                    $("#printshopinventor").find("input.boatcontainerdate[data-container='"+container+"']").datepicker("destroy");
                }
            }
            $("#printshopinventor").find("div.download_link").css('opacity','1').css('cursor','pointer');
            init_inventory_view();
        } else {
            show_error(response);
        }
    },'json');
}

function cancelonboat_container(container) {
    $("#printshopinventor").find("div.download_link").css('opacity','1').css('cursor','pointer');
    if (container<0) {
        // New COntainer
        $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").remove();
        $("#printshopinventor").find("div.onboacontainerarea[data-container='"+container+"']").remove();
        // Rebuild slider

        var newsliderwidth=parseInt($("#printshopinventor").find("div.inventorytablehead").find('div.after_head').css('width'))-60;
        var slidermargin=sliderwidth-newsliderwidth;
        $("#printshopinventor").find("div.left_arrow").removeClass('active');
        if (slidermargin>=0) {
            slidermargin=0;
        } else {
            $("#printshopinventor").find("div.left_arrow").addClass('active');
        }
        $("#printshopinventor").find(".after_head").css('width', newsliderwidth).css('margin-left', slidermargin);
        init_inventory_view();
    } else {
        var params=new Array();
        params.push({name: 'session', value: $("input#onboatsession").val()});
        params.push({name: 'container', value: container});
        params.push({name: 'action', value: 'cancel'});
        var url="/fulfillment/inventory_savecontainer";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("#printshopinventor").find("div.inventorytablehead").find("div.onboacontainer[data-container='"+container+"']").empty().html(response.data.containerhead);
                $("#printshopinventor").find("div.onboacontainerarea[data-container='"+container+"']").empty().html(response.data.containercontent);
                $("#printshopinventor").find("input.boatcontainerdate[data-container='"+container+"']").datepicker("destroy");
                init_inventory_view();
            } else {
                show_error(response);
            }
        },'json');

    }
}

/*function save_pics() {
    var dat=$("form#picsaddform").serializeArray();
    var url="/fulfillment/savepicsattach";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            disablePopup();
        } else {
            show_error(response);
        }
    }, 'json');
}*/

// Manage stock

/*function specs_color_discription() {
    var url = "/fulfillment/specs_color_description";
    $.post(url, {}, function(response) {

    });
}*/

function init_inventdatastock(color) {
    $("a#popupContactClose").unbind('click').click(function(){
        init_inventory_data();
    })
    $("div.addstock").unbind('click').click(function(){
        var url="/fulfillment/invcolor_stock_edit";
        var params=new Array();
        params.push({name: 'printshop_instock_id', value: 0});
        params.push({name: 'printshop_color_id', value: color});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".stockcontentdata").scrollTop();
                $("div.stockcontentdata div.stokdatarow:first-child").before('<div class="stokdatarow">'+response.data.content+'<div>');
                save_stockedit(color);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.editstock").unbind('click').click(function(){
        var url="/fulfillment/invcolor_stock_edit";
        var stock=$(this).data('stock');
        var params=new Array();
        params.push({name: 'printshop_instock_id', value: stock});
        params.push({name: 'printshop_color_id', value: color});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.stockcontentdata div.stokdatarow[data-stock='"+stock+"']").empty().html(response.data.content);
                save_stockedit(color);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.leadorderlink").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'order', value:$(this).data('order')});
        params.push({name: 'printcolor', value: color});
        params.push({name: 'page', value: 'inventory'});
        var url="/fulfillment/order_change";
        $.post(url,params,function(response){
            if (response.errors=='') {
                show_popup('leadorderdetailspopup');
                $("div#pop_content").empty().html(response.data.content);
                $("#popupContactClose").unbind('click').click(function(){
                    clearTimeout(timerId);
                    // Check - may be we close edit content
                    if ($("input#locrecid").length>0) {
                        // Clean locked record
                        var locrecid=$("input#locrecid").val();
                        var url="/leadorder/cleanlockedorder";
                        var params=new Array();
                        params.push({name: 'locrecid', value: locrecid});
                        $.post(url, params, function(response){
                        },'json');
                    }
                    $("#pop_content").empty();
                    disablePopup();
                    invetory_exitorder(color);
                });
                navigation_init();
            } else {
                show_error(response);
            }
        },'json');
    });
}

function invetory_exitorder(color) {
    $("#loader").hide();
    if (parseInt(color)===0) {
        // init_inventory_data();
    } else {
        var params=new Array();
        params.push({name: 'printshop_color_id', value: color});
        var url="/fulfillment/inventory_colorstock";
        $.post(url, params, function(response){
            if (response.errors=='') {
                show_popup('stockdataarea');
                $("div#pop_content").empty().html(response.data.content);
                init_inventdatastock(color);
            } else {
                show_error(response);
            }
        },'json');
    }
}

function save_stockedit(color) {
    // init_inventdatastock(color);
    $("input.instockdata.stockdateinpt").datepicker();
    $("input.instockdata").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('fldname')});
        params.push({name: 'newval', value: $(this).val()});
        var url="/fulfillment/invcolor_stock_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.inventorymanage.cancel").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'printshop_color_id', value: color});
        var url="/fulfillment/inventory_colorstock_data";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.stockcontentdata").empty().html(response.data.content);
                init_inventdatastock(color);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.inventorymanage.save").unbind('click').click(function(){
        var url="/fulfillment/inventory_colorstock_save";
        $.post(url, {}, function(response){
            if (response.errors=='') {
                $("#printshopinventor").find("div.stockcontentdata").empty().html(response.data.content);
                init_inventdatastock(color);
            } else {
                show_error(response);
            }
        },'json');
    });
}

function save_inventitem() {
    $("div.inventorymanage.cancel").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'uploadsession', value: $("input#uploadsession").val()});
        var url="/fulfillment/inventory_data";
        $("#loader").show();
        $.post(url, {}, function(response){
            if (response.errors=='') {
                $("#printshopinventor").find("div.inventorytableleft").empty().html(response.data.totalinvcontent);
                $("#printshopinventor").find("div.inventorytableright").empty().html(response.data.speccontent);
                if ($("input#showonlinemaxvalue").val()==1) {
                    showmaxevent_data();
                }
                $("#loader").hide();
                init_inventory_view();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.inventorymanage.save").unbind('click').click(function(){
        var params=new Array();
        var url="/fulfillment/inventory_item_save";
        $("#loader").show();
        $.post(url, {}, function(response){
            if (response.errors=='') {
                if (response.data.newitem=='1') {
                    $("#loader").hide();
                    init_inventory_data();
                } else {
                    $("#printshopinventor").find("div.inventorytableleft").empty().html(response.data.totalinvcontent);
                    $("#printshopinventor").find("div.inventorytableright").empty().html(response.data.speccontent);
                    $("#loader").hide();
                    init_inventory_view();
                }
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $("input.invitemdataval").unbind('change').change(function(){
        var params=new Array();
        params.push({name: 'fldname', value: $(this).data('item')});
        params.push({name: 'newval', value:$(this).val()});
        var url="/fulfillment/inventory_item_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
}

function save_inventitem_color() {
    $("div.inventorymanage.cancel").unbind('click').click(function(){
        var url="/fulfillment/inventory_data";
        var params = {
            'uploadsession' : $(this).parents('.inventorydatarow').find('input#uploadsession').val()
        };
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.inventorytableleft").empty().html(response.data.totalinvcontent);
                $("div.inventorytableright").empty().html(response.data.speccontent);
                if ($("input#showonlinemaxvalue").val()==1) {
                    showmaxevent_data();
                }
                init_inventory_view();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.inventorymanage.save").unbind('click').click(function(){
        var url="/fulfillment/inventory_color_save";
        // var color=$(this).parents('.inventorydatarow').attr('data-color');
        var params = {
            'uploadsession' : $(this).parents('.inventorydatarow').find('input#uploadsession').val(),
            /*'printshop_color_id': color,*/
            /*'specfile': specfile*/
        };
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (response.data.new=='1') {
                    init_inventory_data();
                } else {
                    $("div.inventorytableleft").empty().html(response.data.totalinvcontent);
                    $("div.inventorytableright").empty().html(response.data.speccontent);
                    if ($("input#showonlinemaxvalue").val()==1) {
                        showmaxevent_data();
                    }
                    init_inventory_view();
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.invitemcolordata").unbind('change').change(function(){
        // var params=new Array();
        // params.push({name: 'fldname', value: $(this).data('item')});
        // params.push({name: 'newval', value:$(this).val()});

        var params = {
            'fldname' : $(this).data('item'),
            'newval' : $(this).val(),
            'uploadsession' : $('input#uploadsession').val()
        };

        var url="/fulfillment/inventory_color_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("input#invstockavail").val(response.data.availabled);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.donotreorderedit").unbind('click').click(function(){
        var params = {
            'fldname' : 'notreorder',
            'newval' : 0,
            'uploadsession' : $('input#uploadsession').val()
        };

        var url="/fulfillment/inventory_color_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.donotreorderedit").removeClass('filled');
                if (parseInt(response.data.notreorder)===1) {
                    $("div.donotreorderedit").addClass('filled');
                }
            } else {
                show_error(response);
            }
        },'json');
    })
    $("select.colororderselect").unbind('change').change(function(){
        // var params=new Array();
        // params.push({name: 'fldname', value: 'color_order'});
        // params.push({name: 'newval', value: $(this).val()});
        var params = {
            'fldname' : 'color_order',
            'newval' : $(this).val(),
            'uploadsession' : $('input#uploadsession').val()
        };
        var url="/fulfillment/inventory_color_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    })
}

// Spec manage
/*function init_inventspec(color) {
    $("div.texteditarea").find('div.savedata').unbind('click').click(function(){
        var url='/fulfillment/inventory_specsave';
        var params=new Array();
        params.push({name: 'printshop_color_id', value: color});
        params.push({name: 'specfile', value: $("textarea.specfile").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                disablePopup();
                $("div.specsdata[data-color='"+color+"']").find('i').removeClass('empty').addClass(response.data.specclass);
            } else {
                show_error(response);
            }
        },'json');
    })
}*/

//Pics

function add_pics() {
    var url="/fulfillment/inventory_pics";
    // var params=[];
    // /*params.push({name: 'artsession', value: $("input#artsession").val()});*/
    // params.push({name:'printshop_color_id', value :printshop_color_id, uplsess : $("input#uploadsession").val()});
    var params = {
        uplsess :  $("input#uploadsession").val()
    };
    $.post(url, params, function(response) {
        if (response.errors=='') {
            show_popup('stockdataarea');
            $("a#popupContactClose").unbind('click').click(function(){
                var url = "/fulfillment/inventory_close_popup";
                $.post(url, params, function(response) {
                    if (response.errors=='') {
                        disablePopup();
                        $("a#popupContactClose").live('click',function(){
                            disablePopup();
                        })
                        // $("a#popupContactClose").live('click');
                    }
                });
            });
            $("div#pop_content").empty().html(response.data.content);

            init_inventpics();
            init_uploadpics_manage();

            $("div.picssave_data").unbind('click').click(function(){
                disablePopup();

            });

            if (response.data.numrec < 8) {
                $("div.picsupload").show();
            } else {
                $("div.picsupload").hide();
            }
        } else {
            show_error(response);
        }
    } , 'json');
}

function init_inventpics() {
    var upload_templ= '<div class="qq-uploader picsupload"><div class="custom_upload qq-upload-button"><span style="clear: both; float: left; padding-left: 10px; padding-top: 8px;">'+
        '<em>Upload</em></span></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: ['jpg', 'jpeg', 'JPG', 'JPEG'],
        action: '/fulfillment/inventory_picsattach',
        template: upload_templ,
        params: {
            'uploadsession': $("#uploadsession").val()
        },
        // multiple: true,
        multiple: false,
        debug: false,
        onSubmit: function(id, fileName){
            $("div.qq-upload-button").css('visibility','hidden');
            $("div.picssave_data").show();
            $("div#loader").show();
        },
        onComplete: function(id, fileName, responseJSON){
            $("div#loader").hide();
            $("ul.qq-upload-list").css('display','none');
            if (responseJSON.success==true) {
                $("div.qq-upload-button").css('visibility','visible');
                $("#orderattachlists").empty().html(responseJSON.content);
                init_uploadpics_manage();
                if (responseJSON.numrec>0) {
                    $("div.picssave_data").show();
                } else {
                    $("div.picssave_data").hide();
                }
                if (responseJSON.numrec < 8) {
                    $("div.picsupload").show();
                } else {
                    $("div.picsupload").hide();
                }

            } else {
                alert(responseJSON.error);
                $("div#loader").hide();
                $("div.qq-upload-button").css('visibility','visible');
            }
        }
    });
}

function init_uploadpics_manage() {
    $("div.delpicsfile").unbind('click').click(function(){
        var uplididx=$(this).data('updloadredraw');
        remove_uploadpics(uplididx);
    });
}

function remove_uploadpics(uplididx) {
    if (confirm('Remove this Proof Docmument?')) {
        var url='/fulfillment/inventory_deluplpics';
        var params=new Array();
        params.push({name:'uploadsession', value: $("input#uploadsession").val()});
        params.push({name:'id', value: uplididx});
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("#orderattachlists").empty().html(response.data.content);
                init_uploadpics_manage();
                if (response.data.numrec>=0) {
                    $("div.picssave_data").show();
                } else {
                    $("div.picssave_data").hide();
                }
                if (response.data.numrec <= 8) {
                    $("div.picsupload").show();
                } else {
                    $("div.picsupload").hide();
                }
            } else {
                show_error(response);
            }
        },'json');
    }
}

function init_download_pics(printshop_color_id) {
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

/*function save_picsupload() {
    var printshop_color_id=$("input#printshop_color_id").val();
    /!* Add New file *!/
    var params=new Array();
    params.push({name: 'uploadsession', value: $("input#uploadsession").val()});
    params.push({name: 'printshop_color_id', value: printshop_color_id});
    var url="/fulfillment/inventory_savepicsload";
    $.post(url, params, function(response){
        if (response.errors=='') {
            disable_popup1();
            $("div#proofarea"+artwork_id).empty().html(response.data.content);
            init_proofs();
        } else {
            show_error(response);
        }
    }, 'json');
}*/

function init_download(printshop_item_id, type) {
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

function add_platetemp(plate_temp, proof_temp, item_label, format) {
    var url="/fulfillment/inventory_loaddata";
    var params=[];
    /*params.push({name: 'artsession', value: $("input#artsession").val()});*/
    params.push({name:'plate_temp', value :plate_temp});
    params.push({name:'proof_temp', value :proof_temp});
    params.push({name:'item_label', value :item_label});
    $.post(url, params, function(response) {
        if (response.errors=='') {
            show_popup('stockdataarea');
            $("div#pop_content").empty().html(response.data.content);
            if (response.data.filename==null) {
                $("div.delplatefile").css('display', 'none');
                $("div#file-uploader").css('visibility','visible');
            } else {
                $("div.delplatefile").css('display', 'block');
                $("div#file-uploader").css('visibility','hidden');
            }
            init_inventplatetemp(format);
            init_uploadplatetemp_manage();
            $("div.platetempsave_data").unbind('click').click(function(){
                save_platetemp();
            });
        } else {
            show_error(response);
        }
    } , 'json');
}

function init_inventplatetemp(format) {
    var upload_templ= '<div class="qq-uploader tempupload"><div class="custom_upload qq-upload-button"><span style="clear: both; float: left; padding-left: 10px; padding-top: 8px;">'+
        '<em>Upload</em></span></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        allowedExtensions: format,
        action: '/fulfillment/inventory_platetempattach',
        template: upload_templ,
        params: {
            'uploadsession': $("#uploadsession").val(),
            'uploadtype': $("#uploadprintitemtype").val()
        },
        // multiple: true,
        multiple: false,
        debug: false,

        onSubmit: function(id, fileName){
            $("div.qq-upload-button").css('visibility','visible');
            $("div.platetempsave_data").show();
            $("div#loader").show();
            $("div.delplatefile").css('display', 'block');
            $("div.qq-uploader").show();
        },
        onComplete: function(id, fileName, responseJSON){
            $("div#loader").hide();
            $("ul.qq-upload-list").css('display','none');
            if (responseJSON.success==true) {
                $("div.qq-upload-button").css('visibility','visible');
                $("div.delplatefile").css('display', 'block');
                $("#orderattachlists").empty().html(fileName);
                init_uploadplatetemp_manage();
                if (responseJSON.numrecs>0) {
                    //$("div.qq-upload-button").css('visibility','visible');
                    $("div.platetempsave_data").hide();
                    $("div.delplatefile").css('display', 'none');
                    $("div.qq-uploader").show();
                } else {
                    // $("div.qq-upload-button").css('visibility','none');
                    $("div.platetempsave_data").show();
                    $("div.delplatefile").css('display', 'block');
                    $("div.qq-uploader").hide();
                }
            } else {
                alert(responseJSON.error);
                $("div#loader").hide();
                $("div.qq-uploader").show();
                $("div.delplatefile").css('display', 'block');
                $("div.qq-upload-button").css('visibility','visible');
            }
        }
    });
}

function init_uploadplatetemp_manage() {
    $("div.delplatefile").unbind('click').click(function(){
        var uplididx=$(this).data('updloadredraw');
        remove_uploadplatetemp(uplididx);
    });
}

function remove_uploadplatetemp(uplididx) {
    if (confirm('Remove this Plate Temp Docmument?')) {
        var url='/fulfillment/inventory_deluplplatetempdocs';
        var params=new Array();
        params.push({name:'uploadsession', value: $("input#uploadsession").val()});
        params.push({name:'id', value: uplididx});
        $.post(url,params, function(response){
            if (response.errors=='') {
                $("#orderattachlists").empty().html(response.data.content);
                init_uploadplatetemp_manage();
                if (response.data.filename == null) {
                    $("div.qq-upload-button").css('visibility','visible');
                    $("div.platetempsave_data").show();
                    $("div.delplatefile").hide();
                    //$("div.qq-uploader").show();
                } else {
                    $("div.qq-upload-button").css('visibility','none');
                    $("div.platetempsave_data").hide();
                    $("div.delplatefile").hide();
                    //$("div.qq-uploader").hide();
                }
            } else {
                show_error(response);
            }
        },'json');
    }
}

function save_platetemp() {
    /* Add New file */
    var url="/fulfillment/save_platetempload";
    var params=new Array();
    /*params.push({name: 'invsession', value: $("input#invsession").val()});*/
    params.push({name: 'uploadsession', value: $("input#uploadsession").val()});
    params.push({name: 'uploadtype', value: $("#uploadprintitemtype").val()})
    $.post(url, params, function(response){
        if (response.errors=='') {
            disablePopup();
            /*$("div.platetempdata[data-color='"+color+"']").find('i').removeClass('empty').addClass(response.data.platetemp);*/
            //init_inventory_view();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_transferview() {
    $("div.inventoryviewswitcher").find('label').unbind('click').click(function(){
        var viewnew=$(this).data('viewtype');
        var curviewtype=$("input#invpageview").val();
        if (viewnew!=curviewtype) {
            show_newinvetory(viewnew);
        }
    });
    $("input#shotogetonly").unbind('change').change(function() {
        init_inventory_data();
    });
    $("div.edittrasferval").unbind('click').click(function(){
        var url="/fulfillment/inventory_transferedit";
        $.post(url,{},function(response){
            if (response.errors=='') {
                $("input.transferval").removeClass('empty').val('');
                $("div.transferdirect").removeClass('empty');
                $("div.transferdirect.direct").addClass('active');
                $("div.transferdirect.inverse").addClass('nonactive');
                $("div.transferheadrow").find('div.transfered').empty().html('<div class="savetransferval">&nbsp;</div>');
                init_tranferchange();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_tranferchange() {
    $("div.savetransferval").unbind('click').click(function(){
        save_tranferdata();
    });
    $("input.transferval").unbind('change').change(function(){
        var params=new Array();
        var color=$(this).data('color');
        params.push({name: 'printshop_color_id', value: color});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldname', value: 'move_amnt'});
        var url="/fulfillment/inventory_transferchange";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.inventorydatarow[data-color='"+color+"']").find('div.have').removeClass('changed').addClass('changed').empty().html(response.data.newhave);
                $("div.inventorydatarow[data-color='"+color+"']").find('div.back_up').removeClass('changed').addClass('changed').empty().html(response.data.newbackup);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.transferdirect.direct").unbind('click').click(function(){
        var direc='direct';
        if ($(this).hasClass('active')==true) {

        } else {
            var color=$(this).data('color');
            var params=new Array();
            params.push({name: 'printshop_color_id', value: color});
            params.push({name: 'newval', value: direc});
            params.push({name: 'fldname', value: 'direct'});
            var url="/fulfillment/inventory_transferchange";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    if (direc=='inverse') {
                        $(".transferdirect.direct[data-color='"+color+"']").removeClass('active').addClass('nonactive');
                        $(".transferdirect.inverse[data-color='"+color+"']").removeClass('nonactive').addClass('active');
                        // $(this).removeClass('active').addClass('nonactive');
                    } else {
                        $(".transferdirect.inverse[data-color='"+color+"']").removeClass('active').addClass('nonactive');
                        $(".transferdirect.direct[data-color='"+color+"']").removeClass('nonactive').addClass('active');
                        // $(this).removeClass('nonactive').addClass('active');
                    }
                    $("div.inventorydatarow[data-color='"+color+"']").find('div.have').removeClass('changed').addClass('changed').empty().html(response.data.newhave);
                    $("div.inventorydatarow[data-color='"+color+"']").find('div.back_up').removeClass('changed').addClass('changed').empty().html(response.data.newbackup);
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $("div.transferdirect.inverse").unbind('click').click(function(){
        var direc='inverse';
        if ($(this).hasClass('active')==true) {
            direc='direct';
        } else {
            var color=$(this).data('color');
            var params=new Array();
            params.push({name: 'printshop_color_id', value: color});
            params.push({name: 'newval', value: direc});
            params.push({name: 'fldname', value: 'direct'});
            var url="/fulfillment/inventory_transferchange";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    if (direc=='direct') {
                        $(".transferdirect.inverse[data-color='"+color+"']").removeClass('active').addClass('nonactive');
                        $(".transferdirect.direct[data-color='"+color+"']").removeClass('nonactive').addClass('active');
                        // $(this).removeClass('active').addClass('nonactive');
                    } else {
                        $(".transferdirect.direct[data-color='"+color+"']").removeClass('active').addClass('nonactive');
                        $(".transferdirect.inverse[data-color='"+color+"']").removeClass('nonactive').addClass('active');
                        // $(this).removeClass('nonactive').addClass('active');
                    }
                    $("div.inventorydatarow[data-color='"+color+"']").find('div.have').removeClass('changed').addClass('changed').empty().html(response.data.newhave);
                    $("div.inventorydatarow[data-color='"+color+"']").find('div.back_up').removeClass('changed').addClass('changed').empty().html(response.data.newbackup);
                } else {
                    show_error(response);
                }
            },'json');

        }
    });
}

function save_tranferdata() {
    var url="/fulfillment/inventory_transfersave";
    $.post(url, {}, function(response){
        if (response.errors=='') {
            $("div.transferheadrow").find('div.transfered').empty().html('<div class="edittrasferval">&nbsp;</div>');
            init_inventory_data();
        } else {
            show_error(response);
        }
    },'json');
}

// Show new view type
function show_newinvetory(viewnew) {
    $("input#invpageview").val(viewnew);
    var params=new Array();
    params.push({name: 'viewtype', value: viewnew});
    var url="/fulfillment/inventory_pageview";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div#pageviewarea").empty().html(response.data.content);
            $("div.inventoryviewswitcher").find('div.label').removeClass('active');
            $("div.inventoryviewswitcher").find("div.label[data-viewtype='"+viewnew+"']").addClass('active');
            if (viewnew=='full') {
                $("div.inventoryfilterarea").css('visibility','hidden');
            } else {
                $("div.inventoryfilterarea").css('visibility','visible');
            }
            init_inventory_data();
        } else {
            show_error(response);
        }
    },'json');

}

function save_onboat(container) {
    var url = '/fulfillment/save_onboatedit';
    var input = $(".add_boat .edit_color").val();
    // var params = new Array();
    // params.push({name: 'data', value: $("input.add_data").val()});
    // params.push({name: 'container_number', value: container})
    // $("div.add_boat *[id]").each(function() {
    //     if($(this).val() != "") {
    //         /*alert($(this).val());*/
    //         params.push({name: $(this)[0].id, value:$(this).val()});
    //     }
    // });

    var params = {
        date : $("input.add_data").val(),
        container_number :  container,
        color_list : []
    }
    $("div.add_boat *[id]:gt(0)").each(function() {
        if($(this).val() != "") {
            //params.color_list[$(this)[0].id] = $(this).val()
            params.color_list.push({color_id: $(this)[0].id, value:$(this).val()});
        }
    });

    $.post(url, params, function(response) {
        if(response.errors=='') {
            $("div.add_boat").css('display', 'none');
            $("input.arriving_data").val();
            window.location.reload();
            /*init_inventory_data();*/
        } else {

        }
    }, 'json');
}

function save_onboatedit(date) {
    var url = '/fulfillment/save_onboatedit';
    var input = $(".edit_onboatcol .edit_color").val();
    // var params = new Array();
    // params.push({name: 'container_number', value: date});
    // params.push({name: 'date', value: $("input#onboatdate").val()});
    // $("div.[data-editonboat='"+date+"'] *[id]").each(function() {
    //     /*if($(this).val() != "") {*/
    //         /*alert($(this).val());*/
    //         params.color_list.push({name: $(this)[0].id, value:$(this).val()});
    //         //params.push({name: 'color_id', value: $('input.edit_color').data('color')});
    //     /*}*/
    // });


    var params = {
        date : $("input#onboatdate").val(),
        container_number :  date,
        color_list : []
    }
    $("div.[data-editonboat='"+date+"'] *[id]").each(function() {
        if($(this).val() != "") {
            params.color_list.push({color_id: $(this)[0].id, value:$(this).val()});
        }
    });

    // Find color ids those user entered empty values. (Just deleted them).
    var cl = JSON.parse(JSON.stringify(params.color_list));
    $(cl).each(function () {
        var v = this;
        if (v.color_id in color_list) {
            delete color_list[v.color_id];
        }
    });

    for (var key in color_list) {
        if (color_list.hasOwnProperty(key)) {
            params.color_list.push({color_id: key, value: ""});
        }
    }

    $.post(url, params, function(response) {
        if(response.errors=='') {
            $("div.add_boat").css('display', 'none');
            window.location.reload();
            /*init_inventory_data();*/
        } else {

        }
    }, 'json');
}

function init_slider_move() {
    $("div.right_arrow").unbind('click').click(function() {
        if($(this).hasClass('active')) {
            var offset = -60;
            slider_move(offset);
        }
    });
    $("div.left_arrow").unbind('click').click(function() {
        if($(this).hasClass('active')) {
            var offset = 60;
            slider_move(offset);
        }
    });
}

function slider_move(offset) {
    var margin=parseInt($("div.after_head").css('margin-left'));
    var slwidth=parseInt($("div.after_head").css('width'));
    var newmargin=(margin+offset);
    if (newmargin>=0) {
        newmargin=0;
        $("div.left_arrow").removeClass('active');
    } else {
        $("div.left_arrow").addClass('active');
    }
    // var newwidth=(slwidth+offset);

    if ((slwidth+newmargin)>sliderwidth) {
        $("div.right_arrow").addClass('active');
    } else {
        $("div.right_arrow").removeClass('active');
    }
    $("div.after_head").animate({marginLeft:newmargin+'px'},'slow',function(){
        var margin=parseInt($("div.after_head").css('margin-left'));
        var slwidth=parseInt($("div.after_head").css('width'));
        if ((slwidth+margin)>sliderwidth) {
            $("div.right_arrow").addClass('active');
        } else {
            $("div.right_arrow").removeClass('active');
        }

    });

    // $("div.after_head_boat").animate({marginLeft:newmargin+'px'},'slow');
    // if ((slwidth+newmargin)>=slshow) {
    init_slider_move();
}
