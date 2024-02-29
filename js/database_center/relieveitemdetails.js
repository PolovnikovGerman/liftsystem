var temp= '<div class="qq-uploader"><div class="popupimageedit_upload qq-upload-button"><span style="clear: both; float: left; width: 100%; text-align: center;">'+
    '<em>upload image</em></span></div>' +
    '<ul class="qq-upload-list"></ul>' +
    '<ul class="qq-upload-drop-area"></ul>'+
    '<div class="clear"></div></div>';
var replacetemp= '<div class="qq-uploader"><div class="popupimageedit_upload qq-upload-button"><span style="clear: both; float: right; width: 100%;">'+
    '<em>[Replace]</em></span></div>' +
    '<ul class="qq-upload-list"></ul>' +
    '<ul class="qq-upload-drop-area"></ul>'+
    '<div class="clear"></div></div>';
var addtemp= '<div class="qq-uploader"><div class="popupimageedit_upload qq-upload-button"><span style="clear: both; float: left;">'+
    '<em>upload image</em></span></div>' +
    '<ul class="qq-upload-list"></ul>' +
    '<ul class="qq-upload-drop-area"></ul>'+
    '<div class="clear"></div></div>';

function init_relievitemdetails_view(item) {
    $(".itemdetails-tab").unbind('click').click(function (){
        var tabview=$(this).data('tabview');
        $(".itemdetails-tab").removeClass('active');
        $(this).addClass('active');
        if (tabview=='infoarea') {
            $(".itemdetails-history").hide();
            $(".itemdetails-infoarea").show();
        } else if (tabview=='history') {
            $(".itemdetails-infoarea").hide();
            $(".itemdetails-history").show();
        }
    })
    $(".itemimagepreview").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url = '/sritemdetails/sritem_images_view';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#imageoptionsbackground").show();
                $("#itemImagesModalLabel").empty().html(response.data.header);
                $("#itemImagesModal").find('div.modal-body').empty().html(response.data.content);
                $("#itemImagesModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#itemImagesModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                    $("#imageoptionsbackground").hide();
                    // show new images
                })
                $(".addimages-slide-list").cycle({
                    fx: 'carousel',
                    allowWrap: false,
                    manualSpeed: 600,
                    timeout : 0,
                    slides: '> div',
                    next : '#nextaddimageslider',
                    prev : '#prevaddimageslider',
                });
                $(".optimages-slide-list").cycle({
                    fx: 'carousel',
                    allowWrap: false,
                    manualSpeed: 600,
                    timeout : 0,
                    slides: '> div',
                    next : '#nextcolorimageslider',
                    prev : '#prevcolorimageslider',
                });
            } else {
                show_error(response);
            }
        },'json');
    });
    // Open AI file
    $(".itemvendorfilebtn.vectorfile").unbind('click').click(function(){
        var url = $(this).data('file');
        // window.openai(url, 'AI Template');
        openai(url,'vector_file.ai');
    });
    // Open Print Location View
    $(".printlocexample").unbind('click').click(function () {
        var url = $(this).data('link');
        window.open(url, 'Print Location','left=120,top=120,width=600,height=600');
    });
    $(".edit_itemdetails").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'item_id', value: item});
        params.push({name: 'brand', value: 'SR'});
        params.push({name: 'editmode', value: 1});
        var url = '/dbitems/relieve_item_edit';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModalLabel").empty().html(response.data.header);
                $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                init_relievitemdetails_edit();
            } else {
                show_error(response);

            }
        },'json');
    })
}

function init_relievitemdetails_edit() {
    $(".save_itemdetails").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url='/sritemdetails/item_relive_savedata';
        $("#loader").show();
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModal").modal('hide');
                init_relievers_items();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');

    })
    $("select.categoryitemselect").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_itemcategory';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".itemdetails-keydatvalue[data-item='item_number']").empty().html(response.data.item_number);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemdetailsstatus").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'item_active'});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(newval)==1) {
                    $(".itemdetailsstatus-value").empty().html('ACTIVE');
                } else {
                    $(".itemdetailsstatus-value").empty().html('INACTIVE');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemdetailstemplate").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'item_template'});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $("select.itemdetailstemplate").addClass('missing_info');
                } else {
                    $("select.itemdetailstemplate").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemsubcategory").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'subcategory_id'});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $("select.itemsubcategory").addClass('missing_info');
                } else {
                    $("select.itemsubcategory").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".tags-checkbox").unbind('click').click(function () {
        var fldname = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        var url='/sritemdetails/change_relive_checkbox';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".tags-checkbox[data-item='"+fldname+"']").empty().html('<i class="fa fa-check-square" aria-hidden="true"></i>');
                    $(".tags-checkbox-label[data-item='"+fldname+"']").addClass('active');
                } else {
                    $(".tags-checkbox[data-item='"+fldname+"']").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
                    $(".tags-checkbox-label[data-item='"+fldname+"']").removeClass('active');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.itemkeyinfoinput").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (fldname=='bullet1' || fldname=='bullet2' || fldname=='bullet3' || fldname=='bullet4') {
                } else {
                    if (newval=='') {
                        $("input.itemkeyinfoinput[data-item='"+fldname+"']").addClass('missing_info');
                    } else {
                        $("input.itemkeyinfoinput[data-item='"+fldname+"']").removeClass('missing_info');
                    }
                }
            } else {
                show_error(response);
            }
        },'json');
    })
    $(".itemdescription").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'item_description1';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $(".itemdescription").addClass('missing_info');
                } else {
                    $(".itemdescription").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".similaritems").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_similar';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.vendornameinp").unbind('change').change(function () {
        var newval=$(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: 'vendor_id'});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_vendor';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $("select.vendornameinp").addClass('missing_info');
                } else {
                    $("select.vendornameinp").removeClass('missing_info');
                }
                $("#profitdataarea").empty().html(response.data.profit);
                $(".relievers_vendorprices").empty().html(response.data.vendor_price);
                $(".itemoptionsarea").empty().html(response.data.colors);
                $("#vendoritemdetailsarea").empty().html(response.data.vendoritemview);
                init_relievitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendoritemcountyinp").unbind('change').change(function () {
        var newval = $(this).val();
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url = '/sritemdetails/change_relive_vendoritemprice';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".vendorshipstate").empty().html(response.data.shipstate);
                if (newval=='') {
                    $(".vendoritemcountyinp").addClass('missing_info');
                } else {
                    $(".vendoritemcountyinp").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        }, 'json');
    });

    // Printshop item
    $("select.printshopitemselect").unbind('change').change(function (){
        var newval=$(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_printshopitem';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $(".printshopitemselect").addClass('missing_info');
                } else {
                    $(".printshopitemselect").removeClass('missing_info');
                }
                $(".printshop_item_name").empty().html(response.data.printshop_name);
                if (response.data.printshop_name=='') {
                    $(".printshop_item_name").addClass('missing_info');
                } else {
                    $(".printshop_item_name").removeClass('missing_info');
                }
                $("#profitdataarea").empty().html(response.data.profit);
                $(".relievers_vendorprices").empty().html(response.data.vendorprice);
                $(".itemoptionsarea").empty().html(response.data.colorsview);
                $(".itemoptioncheck").empty().html(response.data.imgoptions);
                init_relievitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });

    $(".itemimagepreview").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url = '/sritemdetails/relive_images_edit';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#imageoptionsbackground").show();
                $("#itemImagesModalLabel").empty().html(response.data.header);
                $("#itemImagesModal").find('div.modal-body').empty().html(response.data.content);
                $("#itemImagesModal").modal({backdrop: 'static', keyboard: false, show: true});
                $("#itemImagesModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                    $("#imageoptionsbackground").hide();
                    // show new images
                })
                $(".addimages-slide-list").cycle({
                    fx: 'carousel',
                    allowWrap: false,
                    manualSpeed: 600,
                    timeout : 0,
                    slides: '> div',
                    next : '#nextaddimageslider',
                    prev : '#prevaddimageslider',
                });
                $(".optimages-slide-list").cycle({
                    fx: 'carousel',
                    allowWrap: false,
                    manualSpeed: 600,
                    timeout : 0,
                    slides: '> div',
                    next : '#nextcolorimageslider',
                    prev : '#prevcolorimageslider',
                });
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Vendor prices
    $(".vendorpriceinpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var priceidx = $(this).data('price');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'priceidx', value: priceidx});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_vendorprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#netpricesarea").empty().html(response.data.netprices);
                $("#profitdataarea").empty().html(response.data.profit);
                $(".itemprice_extrasale[data-item='item_sale_print']").empty().html(response.data.saleprint);
                $(".itemprice_extrasale[data-item='item_sale_setup']").empty().html(response.data.salesetup);
                $(".itemprice_extrasale[data-item='item_sale_repeat']").empty().html(response.data.salerepeat);
                $(".itemprice_rushsale[data-item='item_sale_rush1']").empty().html(response.data.salerush1);
                $(".itemprice_rushsale[data-item='item_sale_rush2']").empty().html(response.data.salerush2);
                $(".itemprice_pantonesale[data-item='item_sale_pantone']").empty().html(response.data.salepantone);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendordatapriceinpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_vendoritemprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#netpricesarea").empty().html(response.data.netprices);
                $("#profitdataarea").empty().html(response.data.profit);
                $(".itemprice_extrasale[data-item='item_sale_print']").empty().html(response.data.saleprint);
                $(".itemprice_extrasale[data-item='item_sale_setup']").empty().html(response.data.salesetup);
                $(".itemprice_extrasale[data-item='item_sale_repeat']").empty().html(response.data.salerepeat);
                $(".itemprice_rushsale[data-item='item_sale_rush1']").empty().html(response.data.salerush1);
                $(".itemprice_rushsale[data-item='item_sale_rush2']").empty().html(response.data.salerush2);
                $(".itemprice_pantonesale[data-item='item_sale_pantone']").empty().html(response.data.salepantone);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".vendordatainpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_vendoritemprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (fld=='vendor_item_zipcode') {
                    $(".vendorshipstate").empty().html(response.data.shipstate);
                }
                if (newval=='') {
                    $(".vendordatainpt[data-item='"+fld+"']").addClass('missing_info');
                } else {
                    $(".vendordatainpt[data-item='"+fld+"']").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".discountselect").unbind('change').change(function(){
        var newval = $(this).val();
        var fldname = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_itempricediscount';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#netpricesarea").empty().html(response.data.netprices);
                $("#profitdataarea").empty().html(response.data.profit);
                $(".itemprice_extrasale[data-item='item_sale_print']").empty().html(response.data.saleprint);
                $(".itemprice_extrasale[data-item='item_sale_setup']").empty().html(response.data.salesetup);
                $(".itemprice_extrasale[data-item='item_sale_repeat']").empty().html(response.data.salerepeat);
                $(".itemprice_rushsale[data-item='item_sale_rush1']").empty().html(response.data.salerush1);
                $(".itemprice_rushsale[data-item='item_sale_rush2']").empty().html(response.data.salerush2);
                $(".itemprice_pantonesale[data-item='item_sale_pantone']").empty().html(response.data.salepantone);
            } else {
                show_error(response);
            }
        },'json');
    });

    $(".priceinpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var priceidx = $(this).data('price');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'priceidx', value: priceidx});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_itemprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#netpricesarea").empty().html(response.data.netprices);
                $("#profitdataarea").empty().html(response.data.profit);
                $(".itemprice_extrasale[data-item='item_sale_print']").empty().html(response.data.saleprint);
                $(".itemprice_extrasale[data-item='item_sale_setup']").empty().html(response.data.salesetup);
                $(".itemprice_extrasale[data-item='item_sale_repeat']").empty().html(response.data.salerepeat);
                $(".itemprice_rushsale[data-item='item_sale_rush1']").empty().html(response.data.salerush1)
                $(".itemprice_rushsale[data-item='item_sale_rush2']").empty().html(response.data.salerush2)
                $(".itemprice_pantonesale[data-item='item_sale_pantone']").empty().html(response.data.salepantone);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itempriceinpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_itempriceval';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#netpricesarea").empty().html(response.data.netprices);
                $("#profitdataarea").empty().html(response.data.profit);
                $(".itemprice_extrasale[data-item='item_sale_print']").empty().html(response.data.saleprint);
                $(".itemprice_extrasale[data-item='item_sale_setup']").empty().html(response.data.salesetup);
                $(".itemprice_extrasale[data-item='item_sale_repeat']").empty().html(response.data.salerepeat);
                $(".itemprice_rushsale[data-item='item_sale_rush1']").empty().html(response.data.salerush1)
                $(".itemprice_rushsale[data-item='item_sale_rush2']").empty().html(response.data.salerush2)
                $(".itemprice_pantonesale[data-item='item_sale_pantone']").empty().html(response.data.salepantone);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea.metadescription").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'item_metadescription';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $("textarea.metadescription").addClass('missing_info');
                } else {
                    $("textarea.metadescription").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea.metakeywords").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'item_metakeywords';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea.itemkeywords").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'item_keywords';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.itemshipbox").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = $(this).data('item');
        var shipidx = $(this).data('shipbox');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        params.push({name: 'shipidx', value: shipidx});
        var url='/sritemdetails/change_relive_shipbox';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.custommethodselect").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'imprint_method';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });

    $("select.customprintcolors").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'imprint_color';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });

    $(".addprintlocation").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url='/sritemdetails/relive_itemprintloc_add';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".printlocationsdata").empty().html(response.data.content);
                // Init upload
                init_relieve_printlocation();
                init_relievitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".printimageview").unbind('click').click(function () {
        var link = $(this).data('link');
        window.open(link, 'printlocwin', 'width=600, height=800,toolbar=1')
    });
    $(".locationdeleterow").unbind('click').click(function () {
        if (confirm('Remove Print Location?')==true) {
            var params = new Array();
            params.push({name: 'session', value: $("#dbdetailsession").val()});
            params.push({name: 'fldidx', value: $(this).data('idx')});
            var url="/sritemdetails/remove_relive_printlocat";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".printlocationsdata").empty().html(response.data.content);
                    // Init upload
                    init_relieve_printlocation();
                    init_relievitemdetails_edit();
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    $(".printimagedel").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: $(this).data('idx')});
        params.push({name: 'operation', value: 'del'});
        var url="/sritemdetails/save_relive_printlocatview";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".printlocationsdata").empty().html(response.data.content);
                // Init upload
                init_relieve_printlocation();
                init_relievitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.printlocationinpt").unbind('change').change(function () {
        var newval = $(this).val();
        var fldidx = $(this).data('idx');
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: fldidx});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/relive_itemprintloc_edit';
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Upload vector file
    if ($("#addvectorfile").length > 0) {
        init_vector_upload();
    }
    $(".vendorfile_view").unbind('click').click(function () {
        var link = $(this).data('link');
        window.open(link, 'printlocwin', 'width=600, height=800,toolbar=1')
    });
    $(".vendorfile_delete").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'operation', value: 'del'});
        var url="/sritemdetails/save_relive_vectorfile";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".vectorfilemanage").empty().html(response.data.content);
                // Init upload
                init_relievitemdetails_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    })
}

function init_relievitemimages_edit() {
    $("#itemImagesModal").find("button.close").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url="/sritemdetails/item_images_rebuild";
        $.post(url, params, function(response) {
            if (response.errors=='') {
                $("#imageoptionsbackground").hide();
                $(".relievers_itemimages").empty().html(response.data.content);
                init_relievitemdetails_edit();
                $(document.body).addClass('modal-open');
            } else {
                show_error(response);
            }
        },'json');
    })
    // Main Image
    if ($("#uploadmainimage").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('uploadmainimage'),
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            action: '/utils/save_itemimg',
            template: temp,
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success) {
                    $("ul.qq-upload-list").css('display','none');
                    var params = new Array();
                    params.push({name: 'session', value: $("#dbdetailsession").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'main_image'})
                    var url="/sritemdetails/save_relive_image";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_relievitemimages_edit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    if ($("#replaceimagemain").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('replaceimagemain'),
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            action: '/utils/save_itemimg',
            template: replacetemp,
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success) {
                    $("ul.qq-upload-list").css('display','none');
                    var params = new Array();
                    params.push({name: 'session', value: $("#dbdetailsession").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'main_image'})
                    var url="/sritemdetails/save_relive_image";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_relievitemimages_edit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    $(".removeimage.mainimage").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: ''});
        params.push({name: 'fld', value: 'main_image'})
        var url="/sritemdetails/save_relive_image";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Category page
    if ($("#uploadcategoryimage").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('uploadcategoryimage'),
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            action: '/utils/save_itemimg',
            template: temp,
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success) {
                    $("ul.qq-upload-list").css('display','none');
                    var params = new Array();
                    params.push({name: 'session', value: $("#dbdetailsession").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'category_image'})
                    var url="/sritemdetails/save_relive_image";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_relievitemimages_edit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    if ($("#replaceimagecategory").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('replaceimagecategory'),
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            action: '/utils/save_itemimg',
            template: replacetemp,
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success) {
                    $("ul.qq-upload-list").css('display','none');
                    var params = new Array();
                    params.push({name: 'session', value: $("#dbdetailsession").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'category_image'})
                    var url="/sritemdetails/save_relive_image";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_relievitemimages_edit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    $(".removeimage.categoryimage").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: ''});
        params.push({name: 'fld', value: 'category_image'})
        var url="/sritemdetails/save_relive_image";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    if ($("#uploadtopbannerimage").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('uploadtopbannerimage'),
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            action: '/utils/save_itemimg',
            template: temp,
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success) {
                    $("ul.qq-upload-list").css('display','none');
                    var params = new Array();
                    params.push({name: 'session', value: $("#dbdetailsession").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'top_banner'})
                    var url="/sritemdetails/save_relive_image";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_relievitemimages_edit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    if ($("#replaceimagetopbanner").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('replaceimagetopbanner'),
            allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
            action: '/utils/save_itemimg',
            template: replacetemp,
            multiple: false,
            debug: false,
            onComplete: function(id, fileName, responseJSON){
                if (responseJSON.success) {
                    $("ul.qq-upload-list").css('display','none');
                    var params = new Array();
                    params.push({name: 'session', value: $("#dbdetailsession").val()});
                    params.push({name: 'newval', value: responseJSON.filename});
                    params.push({name: 'fld', value: 'top_banner'})
                    var url="/sritemdetails/save_relive_image";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_relievitemimages_edit();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    $(".removeimage.topbannerimage").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: ''});
        params.push({name: 'fld', value: 'top_banner'})
        var url="/sritemdetails/save_relive_image";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Image Slider
    // Replace
    $(".replaseadditems").each(function () {
        var replid = $(this).prop('id');
        if (replid!=='') {
            var uploader = new qq.FileUploader({
                element: document.getElementById(replid),
                allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
                action: '/utils/save_itemimg',
                template: replacetemp,
                multiple: false,
                debug: false,
                onComplete: function(id, fileName, responseJSON){
                    if (responseJSON.success) {
                        $("ul.qq-upload-list").css('display','none');
                        var params = new Array();
                        params.push({name: 'session', value: $("#dbdetailsession").val()});
                        params.push({name: 'newval', value: responseJSON.filename});
                        params.push({name: 'fldidx', value: replid});
                        var url="/sritemdetails/save_relive_updaddimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".addimages-slider").empty().html(response.data.content);
                                init_relievitemimages_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    // Add new
    $(".addimageslider").each(function (){
        var replid = $(this).prop('id');
        if (replid!=='') {
            var uploader = new qq.FileUploader({
                element: document.getElementById(replid),
                allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
                action: '/utils/save_itemimg',
                template: addtemp,
                multiple: false,
                debug: false,
                onComplete: function(id, fileName, responseJSON){
                    if (responseJSON.success) {
                        $("ul.qq-upload-list").css('display','none');
                        var params = new Array();
                        params.push({name: 'session', value: $("#dbdetailsession").val()});
                        params.push({name: 'newval', value: responseJSON.filename});
                        params.push({name: 'fldidx', value: replid});
                        var url="/sritemdetails/save_relive_addimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".addimages-slider").empty().html(response.data.content);
                                init_relievitemimages_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    // Remove image
    $(".removeimage.addimage").unbind('click').click(function (){
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/sritemdetails/save_relive_addimagedel";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".addimages-slider").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $("select.imageorderinpt").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/sritemdetails/save_relive_addimagesort";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".addimages-slider").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".itemimagecaption.addimage").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/sritemdetails/save_relive_addimagetitle";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        }, 'json');
    })
    // Init Add Images slider
    $(".addimages-slide-list").cycle({
        fx: 'carousel',
        allowWrap: false,
        manualSpeed: 600,
        timeout : 0,
        slides: '> div',
        next : '#nextaddimageslider',
        prev : '#prevaddimageslider',
    });
    // Options section
    $(".itemdetailsoptions").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'options';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/sritemdetails/change_relive_item';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newval=='') {
                    $(".itemdetailsoptions").addClass('missing_info');
                } else {
                    $(".itemdetailsoptions").removeClass('missing_info');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Check options
    $(".itemoptioncheck").unbind('click').click(function () {
        var fldname = 'option_images';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        var url='/sritemdetails/change_relive_checkbox';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".itemoptioncheck").empty().html('<i class="fa fa-check-square" aria-hidden="true"></i>');
                    // Show add item and slider
                } else {
                    $(".itemoptioncheck").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Add Image
    $(".addoptionimageslider").each(function (){
        var replid = $(this).prop('id');
        if (replid!=='') {
            var uploader = new qq.FileUploader({
                element: document.getElementById(replid),
                allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
                action: '/utils/save_itemimg',
                template: addtemp,
                multiple: false,
                debug: false,
                onComplete: function(id, fileName, responseJSON){
                    if (responseJSON.success) {
                        $("ul.qq-upload-list").css('display','none');
                        var params = new Array();
                        params.push({name: 'session', value: $("#dbdetailsession").val()});
                        params.push({name: 'newval', value: responseJSON.filename});
                        params.push({name: 'fldidx', value: replid});
                        var url="/sritemdetails/save_relive_addoptionimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".colorimages-slider").empty().html(response.data.content);
                                init_relievitemimages_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    // Replace Image
    $(".replaseoptionitems").each(function () {
        var replid = $(this).prop('id');
        if (replid!=='') {
            var uploader = new qq.FileUploader({
                element: document.getElementById(replid),
                allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
                action: '/utils/save_itemimg',
                template: replacetemp,
                multiple: false,
                debug: false,
                onComplete: function(id, fileName, responseJSON){
                    if (responseJSON.success) {
                        $("ul.qq-upload-list").css('display','none');
                        var params = new Array();
                        params.push({name: 'session', value: $("#dbdetailsession").val()});
                        params.push({name: 'newval', value: responseJSON.filename});
                        params.push({name: 'fldidx', value: replid});
                        var url="/sritemdetails/save_relive_updoptimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".colorimages-slider").empty().html(response.data.content);
                                init_relievitemimages_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    // Sorting
    $(".optimageorderinpt").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/sritemdetails/save_relive_optimagesort";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".colorimages-slider").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Remove image
    $(".removeimagefull.optimage").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/sritemdetails/save_relive_optimagedel";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".colorimages-slider").empty().html(response.data.content);
                init_relievitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".itemimagecaption.optimage").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/sritemdetails/save_relive_optimagetitle";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        }, 'json');
    })
    // Init Options Images slider
    $(".optimages-slide-list").cycle({
        fx: 'carousel',
        allowWrap: false,
        manualSpeed: 600,
        timeout : 0,
        slides: '> div',
        next : '#nextcolorimageslider',
        prev : '#prevcolorimageslider',
    });

}

function init_relieve_printlocation() {
    var replacetemp = '<div class="qq-uploader"><div class="customprint_upload qq-upload-button"><span style="clear: both; float: left; width: 100%; text-align: center;">'+
        '<em>browse</em></span></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';
    if ($(".printimageadd").length > 0) {
        $(".printimageadd").each(function () {
            var replid = $(this).prop('id');
            var locatidx = $(this).data('idx');
            var uploader = new qq.FileUploader({
                element: document.getElementById(replid),
                allowedExtensions: ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'],
                action: '/utils/save_itemimg',
                template: replacetemp,
                multiple: false,
                debug: false,
                onComplete: function(id, fileName, responseJSON){
                    if (responseJSON.success) {
                        $("ul.qq-upload-list").css('display','none');
                        var params = new Array();
                        params.push({name: 'session', value: $("#dbdetailsession").val()});
                        params.push({name: 'newval', value: responseJSON.filename});
                        params.push({name: 'fldidx', value: locatidx});
                        params.push({name: 'operation', value: 'add'});
                        var url="/sritemdetails/save_relive_printlocatview";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".printlocationsdata").empty().html(response.data.content);
                                // Init upload
                                init_relieve_printlocation();
                                init_relievitemdetails_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        });
    }
}

function init_vector_upload() {
    var temp= '<div class="qq-uploader"><div class="custom_upload qq-upload-button"><span style="clear: both; float: left; width: 100%; text-align: center;">'+
        '<i class="fa fa-plus"></i> Add</span></div>' +
        '<ul class="qq-upload-list"></ul>' +
        '<ul class="qq-upload-drop-area"></ul>'+
        '<div class="clear"></div></div>';
    var uploader = new qq.FileUploader({
        element: document.getElementById('addvectorfile'),
        allowedExtensions: ['ai','AI'],
        action: '/utils/save_itemplatetemplate',
        template: temp,
        multiple: false,
        debug: false,
        onComplete: function(id, fileName, responseJSON){
            if (responseJSON.success) {
                $("ul.qq-upload-list").css('display','none');
                var params = new Array();
                params.push({name: 'session', value: $("#dbdetailsession").val()});
                params.push({name: 'newval', value: responseJSON.filename});
                params.push({name: 'operation', value: 'add'});
                var url="/sritemdetails/save_relive_vectorfile";
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $(".vectorfilemanage").empty().html(response.data.content);
                        // Init upload
                        init_relievitemdetails_edit();
                    } else {
                        show_error(response);
                    }
                }, 'json');
            } else {
                $("ul.qq-upload-list").css('display','none');
            }
        }
    });

}