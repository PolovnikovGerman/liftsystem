$(document).ready(function(){
    show_results();
});

function init_export_view() {
    $("#db_exportsearch").unbind('click').click(function () {
        show_results();
    });
    $("#db_exportclean").unbind('click').click(function(){
        $("#item_number").val('');
        $("#item_name").val('');
        $("#itm_template").val('');
        $("#itm_new").val('');
        $("#itm_active").val('');
        $("#lead_a").val('');
        $("#lead_b").val('');
        $("#lead_c").val('');
        $("#export_vendor").val('');
        show_results();
    });
    $("#db_export").unbind('click').click(function () {
        var url='/database/export_select_fields';
        $.post(url, {}, function(response){
            if (response.errors=='') {
                $("#pageModal").find('div.modal-dialog').css('width','625px');
                $("#pageModalLabel").empty().html(response.data.title);
                $("#pageModal").find('div.modal-body').empty().html(response.data.content);
                $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
                init_selectexportfields()
            } else {
                show_error(response);
            }
        },'json');
    });
}

function show_results() {
    var params = new Array();
    params.push({name: 'item_number', value: $("#item_number").val()});
    params.push({name: 'item_name', value: $("#item_name").val()});
    params.push({name: 'item_template', value: $("#itm_template").val()});
    params.push({name: 'item_new', value: $("#itm_new").val()});
    params.push({name: 'item_active', value: $("#itm_active").val()});
    params.push({name: 'lead_a', value: $("#lead_a").val()});
    params.push({name: 'lead_b', value: $("#lead_b").val()});
    params.push({name: 'lead_c', value: $("#lead_c").val()});
    params.push({name: 'vendor_id', value: $("#export_vendor").val()});

    var url="/database/exportdata";
    // $("#dbitemloader").css('display','block');
    $.post(url, params, function(response){
        // $("#dbitemloader").css('display','none');
        if (response.errors=='') {
            $("div.db_export_results").empty().html(response.data.content);
            $("div#db_searchres").empty().html('Found <b>'+response.data.totals+'</b> records');
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_selectexportfields() {
    $("div.fieldname").unbind('click').click(function(){
        select_row(this);
    });
    $("button#attach_fld").unbind('click').click(function(){
        attach_fields();
    });
    $("button#remove_fld").unbind('click').click(function(){
        remove_fields();
    });
    /* Save Export */
    $("#db_saveexport").live('click',function(){
        save_export();
    });
}

function select_row(obj) {
    var objid=obj.id;
    var fldid=obj.id.substr(3);
    if (fldid!='1') {
        var classes=$("#"+objid).prop('class');
        if (classes.indexOf('selected') + 1) {
            $("#"+objid).removeClass('selected');
        } else {
            $("#"+objid).addClass('selected');
        }
    }
}

/* Add selected fields to array of exported fields */
function attach_fields() {
    $("div.allowed_select div.fields_list div.fieldname").each(function(index, value){
        var objid=value.id;
        var classes=$("#"+objid).prop('class');
        if (classes.indexOf('selected') + 1) {
            $("#"+objid).removeClass('selected');
            $("#"+objid).removeClass("allowed_enable").addClass("allowed_hide");
            /* Selected Field */
            $("#sel"+objid.substr(3)).removeClass("select_hide").addClass("select_allowed");
        }
    });
}
/* remove field */
function remove_fields() {
    $("div.selected_fileds div.fields_list div.fieldname").each(function(index, value){
        var objid=value.id;
        var classes=$("#"+objid).prop('class');
        if (classes.indexOf('selected') + 1) {
            $("#"+objid).removeClass('selected');
            $("#"+objid).removeClass("select_allowed").addClass("select_hide");
            /* Selected Field */
            $("#all"+objid.substr(3)).removeClass("allowed_hide").addClass("allowed_enable");
        }
    });
}

function save_export() {
    var fldstr='';
    $("div.selected_fileds div.fields_list div.fieldname").each(function(index, value){
        var objid=value.id;
        var classes=$("#"+objid).prop('class');
        if (classes.indexOf('select_allowed') + 1) {
            fldstr=fldstr+objid.substr(3)+"|";
        }
    });
    $("#pageModal").modal('hide');
    var params = new Array();
    params.push({name: 'item_number', value: $("#item_number").val()});
    params.push({name: 'item_name', value: $("#item_name").val()});
    params.push({name: 'item_template', value: $("#itm_template").val()});
    params.push({name: 'item_new', value: $("#itm_new").val()});
    params.push({name: 'item_active', value: $("#itm_active").val()});
    params.push({name: 'lead_a', value: $("#lead_a").val()});
    params.push({name: 'lead_b', value: $("#lead_b").val()});
    params.push({name: 'lead_c', value: $("#lead_c").val()});
    params.push({name: 'vendor_id', value: $("#export_vendor").val()});
    params.push({name: 'fldlst', value:fldstr});
    var url="/database/save_export";
    $.post(url, params, function (response) {
        if (response.errors=='') {
            window.open(response.data.export_url, 'DB Export');
        } else {
            show_error(response);
        }
    },'json');
}