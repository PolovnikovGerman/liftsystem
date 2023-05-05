function init_countries() {
    // Build data
    get_country_data();
    // Init search
    $("div.search_template").click(function(){
        var search=$(this).data('searchid');
        $("div.search_template").removeClass('active');
        $(this).addClass('active');
        $("input#search_template").val(search);
        get_country_data();
        $("div.search_clean").show();
    })
    $("div.search_clean").click(function(){
        $("div.search_template").removeClass('active');
        $("input#search_template").val('');
        get_country_data();
        $("div.search_clean").hide();
    })
}

function get_country_data() {
    var params=new Array();
    params.push({name:'search_template', value:$("input#search_template").val()});
    params.push({name:'sort',value:'sort'});
    params.push({name:'direc', value:'asc'});
    var url="/settings/countries_data";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.countries_data").empty().html(response.data.content);
            init_countrydat();
            leftmenu_alignment();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_countrydat() {
    $("select.shzonevalue").change(function(){

    })
    $("input.cntshipallow").change(function(){
        var cnt_id=$(this).data('coutryid');
        var allow=0;
        if ($(this).prop('checked')==true) {
            allow=1;
        }
        edit_shipallow(cnt_id, allow);
    })
}

function edit_shipallow(cnt_id, allow) {
    var url="/settings/countries_shipallow";
    $.post(url, {'country_id':cnt_id, 'shipallow':allow}, function(response){
        if (response.errors=='') {
            if (allow==0) {
                $("div.countries_data_row[data-countryid="+cnt_id+"]").addClass('notallowed');
            } else {
                $("div.countries_data_row[data-countryid="+cnt_id+"]").removeClass('notallowed');
            }
        } else {
            show_error(response);
        }
    }, 'json');
}