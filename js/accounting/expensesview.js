function init_opercalc() {
    init_calc();
    // Change Brand
    $("#expensesviewbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#expensesviewbrand").val(brand);
        $("#expensesviewbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#expensesviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#expensesviewbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_calc();
    });
}

function init_calc() {
    var url='/accounting/opercalcdata';
    var params = new Array();
    params.push({name: 'sort', value: $("input#calcsort").val()});
    params.push({name: 'direction', value: $("input#calcdirec").val()});
    params.push({name: 'brand', value: $("#expensesviewbrand").val()});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.calc-content").empty().html(response.data.content);
            $("div.calc-totalmonthly").empty().html(response.data.total_month);
            $("div.calc-totalweekly").empty().html(response.data.total_week);
            $("div.calc-totalquart").empty().html(response.data.total_quart);
            $("div.calc-totalyear").empty().html(response.data.total_year)
            $("#loader").hide();
            init_calc_management();
        } else {
            $("#loader").hide();
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function init_calc_management() {
    $("div.newcalcrow").unbind('click').click(function(){
        add_calcrow();
    })
    $("div.calc-edit").unbind('click').click(function(){
        var calc = $(this).data('calcid');
        edit_calc(calc);
    })
    /* Events focus-blur */
    $("input#monthsum").unbind('focus').focus(function() {
        var weeksum=$("input#weeksum").val();
        if(weeksum=='') {
            $("input#monthsum").prop('readonly',false);
        } else {
            $("input#monthsum").prop('readonly','readonly');
        }
    });
    $("input#weeksum").unbind('focus').focus(function() {
        var monthsum=$("input#monthsum").val();
        if(monthsum=='') {
            $("input#weeksum").prop('readonly',false);
        } else {
            $("input#weeksum").prop('readonly','readonly');
        }
    });
    $("div.calc-actions.calc-delete").unbind('click').click(function(){
        var calc = $(this).data('calcid');
        delete_calcrow(calc);
    })
    $("div.sortcalc").unbind('click').click(function(){
        change_sort(this);
    })
}

function edit_calc(calc) {
    var url='/accounting/calcrow';
    var params = new Array();
    params.push({name: 'calc_id', value: calc});
    params.push({name: 'brand', value: $("#expensesviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div#calcrow"+calc).empty().removeClass('newcalcrow').html(response.data.content);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function add_calcrow() {
    var url='/accounting/calcrow';
    var params = new Array();
    params.push({name: 'calc_id', value: 0});
    params.push({name: 'brand', value: $("#expensesviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.newcalcrow").empty().removeClass('newcalcrow').html(response.data.content);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function savecalcrow() {
    var url="/accounting/calcsave";
    var params = new Array();
    params.push({name: 'calc_id', value : $("input#calc_id").val()});
    params.push({name: 'descr', value: $("input#description").val()});
    params.push({name: 'month', value: $("input#monthsum").val()});
    params.push({name: 'week', value: $("input#weeksum").val()});
    params.push({name: 'brand', value: $("#expensesviewbrand").val()});
    $.post(url, params, function (response) {
        if (response.errors == '') {
            init_calc();
        } else {
            alert(response.errors);
            if (response.data.url !== undefined) {
                window.location.href = response.data.url;
            }
        }
    }, 'json');

}

function delete_calcrow(calc_id) {
    var descr=$("#calcrow"+calc_id+" div.calc-descrdata").text();
    if (confirm('Do you realy want to delete '+descr+'?')) {
        var url="/accounting/calcdelete";
        $.post(url,{'calc_id':calc_id},function(response){
            if (response.errors=='') {
                init_calc();
            } else {
                alert(response.errors);
                if(response.data.url !== undefined) {
                    window.location.href=response.data.url;
                }
            }
        },'json');
    }
}

function change_sort(obj) {
    var objid=obj.id;
    /* Del img */
    var newsort=objid.substr(4);
    var oldsort=$("input#calcsort").val();
    $("div#sort"+oldsort+" div.sortcalclnk").empty();
    var olddirect=$("input#calcdirec").val();
    var newdirec='';
    if (newsort==oldsort) {
        if (olddirect=='asc') {
            newdirec='desc';
        } else {
            newdirec='asc';
        }
    } else {
        newdirec='desc';
    }
    $("input#calcsort").val(newsort);
    $("input#calcdirec").val(newdirec);
    if (newdirec=='asc') {
        $("div#sort"+newsort+" div.sortcalclnk").html('<img src="/img/icons/sort_up.png" alt="Sort"/>');
    } else {
        $("div#sort"+newsort+" div.sortcalclnk").html('<img src="/img/icons/sort_down.png" alt="Sort"/>');
    }
    init_calc();
}