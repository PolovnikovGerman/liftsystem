var mainurl = '/leadmanagement';
function show_new_lead(lead_id,type, brand) {
    var url=mainurl+"/edit_lead";
    params = new Array();
    params.push({name: 'lead_id', value :lead_id});
    params.push({name: 'brand', value: brand});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#leadformModalLabel").empty().html(response.data.title);
            $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
            $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#leadformModal").modal({backdrop: 'static', keyboard: false, show: true});
            init_leadpopupcontent();
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    },'json');
}

function edit_lead(lead_id) {
    // var lead_id=obj.id.substr(7);
    var url=mainurl+"/edit_lead";
    $.post(url, {'lead_id':lead_id}, function(response){
        if (response.errors=='') {
            $("#leadformModalLabel").empty().html(response.data.title);
            $("#leadformModal").find('div.modal-body').empty().html(response.data.content);
            $("#leadformModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#leadformModal").modal({backdrop: 'static', keyboard: false, show: true});
            // init_lead_cloneemail();
            init_leadpopupcontent();
            init_leadpopupedit();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_leadpopupcontent(){
    $("select#lead_item").select2({
        dropdownParent: $('#leadformModal'),
        matcher: matchStart,
    });
    $("input#lead_needby").datepicker({
        autoclose: true,
        todayHighlight: true
    // }).on('changeDate', function (e) {
    //     $("#lead_needby").val(e.format(0,"mm/dd/yyyy"));
    });
}
function init_leadpopupedit() {
    // Input change
    $("input.leadmainedit").unbind('change').change(function (){
        var field_name = $(this).data('fld');
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: field_name});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (field_name=='lead_needby') {
                    $("input#lead_needby").val(response.data.newdate);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Select Changes
    $("select.leadmainedit").unbind('change').change(function (){
        var params = new Array();
        var field_name = $(this).data('fld');
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: field_name});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                if (field_name=='country_id') {
                    $("#lead_address_states").empty().html(response.data.states_view);
                }
                init_leadpopupedit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // TextArea Changes
    $("textarea.leadmainedit").unbind('change').change(function(){
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    // Item Change
    $(".leadpopupitem").unbind('change').change(function (){
        var field_name = 'lead_item_id';
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'field_name', value: field_name});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_data_change";
        $.post(url, params, function (response){
            if (response.errors=='') {
                var newtxt = $("#lead_item option:selected").text();
                $("#select2-lead_item-container").empty().html(newtxt);
                if (parseInt(response.data.show_custom)==1) {
                    $("div.lead-descr").addClass('active');
                } else {
                    $("div.lead-descr").removeClass('active');
                }
                $("textarea[data-fld='other_item_name']").val('');
                init_leadpopupedit();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Contacts change
    $("input.leadcontactedit").unbind('change').change(function (){
        var params = new Array();
        params.push({name: 'lead', value: $("#leadeditid").val()});
        params.push({name: 'contact', value: $(this).data('contact')});
        params.push({name: 'field_name', value: $(this).data('fld')});
        params.push({name: 'newval', value: $(this).val()});
        var url = mainurl+"/lead_contact_change";
    });
    // Save button
}