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

function init_btitemdetails_view(item) {
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
        // Show popup with images and colors
    });
    $(".itemvendorfilebtn.vectorfile").unbind('click').click(function(){
        var url = $(this).data('file');
        window.openai(url, 'AI Template');
    });
    $(".printlocexample").unbind('click').click(function () {
        var url = $(this).data('link');
        window.open(url, 'Print Location','left=120,top=120,width=600,height=600');
    });
    $(".edit_itemdetails").unbind('click').click(function () {
        var params=new Array();
        params.push({name: 'item_id', value: item});
        params.push({name: 'brand', value: 'BT'});
        params.push({name: 'editmode', value: 1});
        var url = '/dbitems/itemlistdetails';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModalLabel").empty().html(response.data.header);
                $("#itemDetailsModal").find('div.modal-body').empty().html(response.data.content);
                init_btitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    })
}

function init_btitemdetails_edit() {
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
    $(".save_itemdetails").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url='/btitemdetails/save_itemdetails';
        $("#loader").show();
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#itemDetailsModal").modal('hide');
                // init_btitemslist_view();
                search_itemlists();
            } else {
                $("#loader").hide();
                show_error(response);
            }
        },'json');
    });
    $("select.categoryitemselect").unbind('change').change(function () {
        var newval = $(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: newval});
        var url='/btitemdetails/change_item_category';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".itemkeyinfoinput.itemnumberone").val(response.data.item_numberone);
                $(".itemkeyinfoinput.itemnumber").val(response.data.item_numbersec);
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".actionbtn").unbind('click').click(function () {
        var params = new Array()
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url='/btitemdetails/change_item_status';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(response.data.item_active)==1) {
                    $(".itemdetailsstatus-value").empty().html('ACTIVE');
                    $(".actionbtn").empty().html('Make Inactive');
                } else {
                    $(".itemdetailsstatus-value").empty().html('INACTIVE');
                    $(".actionbtn").empty().html('Make Active');
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
        var url='/btitemdetails/change_btitem';
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
        params.push({name: 'fld', value: $(this).data('category')});
        params.push({name: 'newval', value: newval});
        var url='/btitemdetails/change_item_subcategory';
        $.post(url, params, function (response) {
            if (response.errors=='') {
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
        var url='/btitemdetails/change_btitem_checkbox';
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
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/change_btsimilar';
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
        var url='/btitemdetails/change_btvendor';
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
                init_btitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".itemimagepreview").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url = '/btitemdetails/btitem_images_edit';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#imageoptionsbackground").show();
                $("#itemImagesModalLabel").empty().html(response.data.header);
                $("#itemImagesModal").find('div.modal-body').empty().html(response.data.content);
                $("#itemImagesModal").modal({backdrop: 'static', keyboard: false, show: true});
                // $("#itemImagesModal").modal({show: true});
                $("#itemImagesModal").on('hidden.bs.modal', function (e) {
                    $(document.body).addClass('modal-open');
                    // show new images
                })
                init_btitemimages_edit();
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
        var url='/btitemdetails/change_btvendorprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#profitdataarea").empty().html(response.data.profit);
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
        var url='/btitemdetails/change_btvendoritemprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#profitdataarea").empty().html(response.data.profit);
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
        var url='/btitemdetails/change_btvendoritemprice';
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
    // Printshop item
    $("select.printshopitemselect").unbind('change').change(function (){
        var newval=$(this).val();
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: newval});
        var url='/btitemdetails/change_printshopitem';
        $.post(url, params, function (response) {
            if (response.errors=='') {
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
                init_btitemdetails_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Printshop color
    $(".printshopcolor").unbind('change').change(function (){
        var newval=$(this).val();
        var color = $(this).data('color');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: newval});
        params.push({name: 'color', value: color});
        var url='/btitemdetails/change_printshopcolor';
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');

    })
    $(".vendoritemcountyinp").unbind('change').change(function () {
        var newval = $(this).val();
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url = '/btitemdetails/change_btvendoritemprice';
        $.post(url, params, function (response) {
            if (response.errors == '') {
                $(".vendorshipstate").empty().html(response.data.shipstate);
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Change item price
    $(".priceinpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var priceidx = $(this).data('price');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'priceidx', value: priceidx});
        params.push({name: 'newval', value: newval});
        var url='/btitemdetails/change_btitemprice';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#profitdataarea").empty().html(response.data.profit);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Change Item - price section
    $(".itempriceinpt").unbind('change').change(function () {
        var newval=$(this).val();
        var fld = $(this).data('item');
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fld});
        params.push({name: 'newval', value: newval});
        var url='/btitemdetails/change_btitempriceval';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#profitdataarea").empty().html(response.data.profit);
            } else {
                show_error(response);
            }
        },'json');
    });
    // Meta
    $("textarea.metadescription").unbind('change').change(function () {
        var newval = $(this).val();
        var fldname = 'item_metadescription';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        params.push({name: 'newval', value: newval});
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/change_btshipbox';
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
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/change_btitem';
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
        var url='/btitemdetails/itemprintloc_add';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".printlocationsdata").empty().html(response.data.content);
                // Init upload
                init_btprintlocation();
                init_btitemdetails_edit();
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
            var url="/btitemdetails/remove_printlocat";
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $(".printlocationsdata").empty().html(response.data.content);
                    // Init upload
                    init_btprintlocation();
                    init_btitemdetails_edit();
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
        var url="/btitemdetails/save_printlocatview";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $(".printlocationsdata").empty().html(response.data.content);
                // Init upload
                init_btprintlocation();
                init_btitemdetails_edit();
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
        var url='/btitemdetails/itemprintloc_edit';
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
        var url="/btitemdetails/save_vectorfile";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".vectorfilemanage").empty().html(response.data.content);
                // Init upload
                init_btitemdetails_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    })
}

function init_btitemimages_edit() {
    $("#itemImagesModal").find("button.close").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        var url="/btitemdetails/item_images_rebuild";
        $.post(url, params, function(response) {
            if (response.errors=='') {
                $("#imageoptionsbackground").hide();
                $(".relievers_itemimages").empty().html(response.data.content);
                init_btitemdetails_edit();
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
                    var url="/btitemdetails/save_btimage";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_btitemimages_edit();
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
                    var url="/btitemdetails/save_btimage";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_btitemimages_edit();
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
        var url="/btitemdetails/save_btimage";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                init_btitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    // Category Image
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
                    var url="/btitemdetails/save_btimage";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_btitemimages_edit();
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
                    var url="/btitemdetails/save_btimage";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_btitemimages_edit();
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
        var url="/btitemdetails/save_btimage";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                init_btitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });

    // Top Banner
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
                    var url="/btitemdetails/save_btimage";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_btitemimages_edit();
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
                    var url="/btitemdetails/save_btimage";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                            init_btitemimages_edit();
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
        var url="/btitemdetails/save_btimage";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".popupimages_section.itemimagesection").empty().html(response.data.content);
                init_btitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    });
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
                        var url="/btitemdetails/save_btupdaddimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".addimages-slider").empty().html(response.data.content);
                                init_btitemimages_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
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
                        var url="/btitemdetails/save_btupdaddimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".addimages-slider").empty().html(response.data.content);
                                init_btitemimages_edit();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    $(".removeimage.addimage").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/btitemdetails/save_btaddimagedel";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".addimages-slider").empty().html(response.data.content);
                init_btitemimages_edit();
            } else {
                show_error(response);
            }
        }, 'json');
    })
    $("select.imageorderinpt").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/btitemdetails/save_btaddimagesort";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".addimages-slider").empty().html(response.data.content);
                init_btitemimages_edit();
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
        var url="/btitemdetails/save_btaddimagetitle";
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
        var url='/btitemdetails/change_btitem';
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
    $(".itemoptioncheck").unbind('click').click(function () {
        var fldname = 'option_images';
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fld', value: fldname});
        var url='/btitemdetails/change_options_checkbox';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(response.data.newval)==1) {
                    $(".itemoptioncheck").empty().html('<i class="fa fa-check-square" aria-hidden="true"></i>');
                    // Show add item and slider
                    $("#addoptionimage").show();
                    $("#addoptiontxt").hide();
                    $(".colorimages-slider").empty().html(response.data.slideroptions);
                    init_optionslider();
                } else {
                    $(".itemoptioncheck").empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
                    $("#addoptionimage").hide();
                    $("#addoptiontxt").show();
                    $(".colorimages-slider").empty().html(response.data.slideroptions);
                    init_optionslider();
                }
                // init_btitemimages_edit();
            } else {
                show_error(response);
            }
        },'json');
    });
    init_optionslider();
}

function init_optionslider() {
    if ($("#addoptionimage").length > 0) {
        var uploader = new qq.FileUploader({
            element: document.getElementById('addoptionimage'),
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
                    var url="/btitemdetails/save_addoptionis";
                    $.post(url, params, function(response){
                        if (response.errors=='') {
                            $(".colorimages-slider").empty().html(response.data.content);
                            init_optionslider();
                        } else {
                            show_error(response);
                        }
                    }, 'json');
                }
            }
        });
    }
    $(".replaseoptionitems").each(function () {
        var replid = $(this).prop('id');
        if (replid != '') {
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
                        var url="/btitemdetails/save_updoptimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".colorimages-slider").empty().html(response.data.content);
                                init_optionslider();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    $(".addoptionimageslider").each(function (){
        var replid = $(this).prop('id');
        if (replid != '') {
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
                        var url="/btitemdetails/save_updoptimage";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".colorimages-slider").empty().html(response.data.content);
                                init_optionslider();
                            } else {
                                show_error(response);
                            }
                        }, 'json');
                    }
                }
            });
        }
    });
    $(".optimageorderinpt").unbind('change').change(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'newval', value: $(this).val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/btitemdetails/save_optionsort";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".colorimages-slider").empty().html(response.data.content);
                init_optionslider();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".removeimage.optimage").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/btitemdetails/save_optiondel";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".colorimages-slider").empty().html(response.data.content);
                init_optionslider();
            } else {
                show_error(response);
            }
        }, 'json');
    });
    $(".removeimagefull").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'session', value: $("#dbdetailsession").val()});
        params.push({name: 'fldidx', value: $(this).data('image')});
        var url="/btitemdetails/save_optiondel";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $(".colorimages-slider").empty().html(response.data.content);
                init_optionslider();
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
        var url="/btitemdetails/save_optiontitle";
        $.post(url, params, function(response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        }, 'json');
    })
    // Add text
    if ($("#addoptiontxt").length > 0) {
        $("#addoptiontxt").unbind('click').click(function () {
            var params = new Array();
            params.push({name: 'session', value: $("#dbdetailsession").val()});
            params.push({name: 'newval', value: ''});
            var url="/btitemdetails/save_addoptionis";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $(".colorimages-slider").empty().html(response.data.content);
                    init_optionslider();
                } else {
                    show_error(response);
                }
            }, 'json');
        })
    }
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

function init_btprintlocation() {
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
                        var url="/btitemdetails/save_printlocatview";
                        $.post(url, params, function(response){
                            if (response.errors=='') {
                                $(".printlocationsdata").empty().html(response.data.content);
                                // Init upload
                                init_btprintlocation();
                                init_btitemdetails_edit();
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
                var url="/btitemdetails/save_vectorfile";
                $.post(url, params, function(response){
                    if (response.errors=='') {
                        $(".vectorfilemanage").empty().html(response.data.content);
                        // Init upload
                        init_btitemdetails_edit();
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