function init_opercalc() {
    init_calc();
}

function init_calc() {
    var params = new Array();
    var sortval = $("#expensivesviewsort").val();
    var sortfld = 'percent';
    var sortdir = 'desc';
    if (sortval=='perc_asc') {
        sortdir = 'asc';
    } else if (sortval=='date_desc') {
        sortfld = 'date';
    } else if (sortval=='method_desc') {
        sortfld = 'method';
    } else if (sortval=='date_asc') {
        sortfld = 'date';
        sortdir = 'asc';
    } else if (sortval=='method_asc') {
        sortfld = 'method';
        sortdir = 'asc';
    }
    params.push({name: 'sort', value: sortfld});
    params.push({name: 'direction', value: sortdir});
    params.push({name: 'brand', value: $("#expensivesviewbrand").val()});
    var url='/accounting/opercalcdata';
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#expensivesviewtable").empty().html(response.data.content);
            $("#expanse-month-total").empty().html(response.data.total_month);
            $("#expanse-quoter-total").empty().html(response.data.total_week);
            // $("div.calc-totalquart").empty().html(response.data.total_quart);
            $("#expanse-year-total").empty().html(response.data.total_year);
            $(".expensivesviewtable").scrollpanel({
                'prefix' : 'sp-'
            });
            $("#loader").hide();
            leftmenu_alignment();
            init_calc_management();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function init_calc_management() {
    $("div.expensivesview-addnewbtn").unbind('click').click(function(){
        add_calcrow();
    })
    $("div.calc-edit").unbind('click').click(function(){
        var calc = $(this).parent('div.expensivesviewtablerow').parent('div.datarow').data('calc');
        edit_calc(calc);
    })
    $("i.removeexpensive").unbind('click').click(function(){
        var calc = $(this).data('calc');
        delete_calcrow(calc);
    })
    $("select#expensivesviewsort").unbind('change').change(function(){
        init_calc();
    })
}

function edit_calc(calc) {
    var url='/accounting/calcrow';
    var params = new Array();
    params.push({name: 'calc_id', value: calc});
    params.push({name: 'brand', value: $("#expensesviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.datarow[data-calc='"+calc+"']").find('div.expensivesviewtablerow').empty().html(response.data.content);
            if (response.data.datetype=='year') {
                $("#yearsum_inpt").focus();
            } else if (response.data.datetype=='month') {
                $("#monthsum_inpt").focus();
            } else {
                $("#weeksum_inpt").focus();
            }
            $("div.calc-edit").unbind('click');
            $("div.expensivesview-addnewbtn").unbind('click');
            $(".removeexpensive").unbind('click');
            init_edit_calc();
        } else {
            show_error(response);
        }
    }, 'json');
}

function add_calcrow() {
    var url='/accounting/calcrow';
    var params = new Array();
    params.push({name: 'calc_id', value: 0});
    params.push({name: 'brand', value: $("#expensivesviewbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            // $("div.newcalcrow").empty().removeClass('newcalcrow').html(response.data.content);
            // $(".expensivesviewtable").find('div.datarow:first').prepend
            $("div#newcalcrow").show().empty().html('<div class="expensivesviewtablerow whitedatarow">'+response.data.content+'</div>');
            $("#yearsum_inpt").focus();
            $("div.calc-edit").unbind('click');
            $("div.expensivesview-addnewbtn").unbind('click');
            $(".removeexpensive").unbind('click');
            init_edit_calc();
        } else {
            show_error(response);
        }
    }, 'json');
}

function init_edit_calc() {
    $(".expensive-savedata").find('i').unbind('click').click(function(){
        var params = new Array();
        params.push({name: 'calc_id', value: $(this).data('calc')});
        params.push({name: 'date_type', value: $("#date_type").val()});
        params.push({name: 'descr', value: $("input#description_inpt").val()});
        params.push({name: 'yearsum', value: $("#yearsum_inpt").val()});
        params.push({name: 'monthsum', value: $("input#monthsum_inpt").val()});
        params.push({name: 'weeksum', value: $("input#weeksum_inpt").val()});
        params.push({name: 'method', value: $("#method_inpt").val()});
        params.push({name: 'date_day', value: $("#dateday_inpt").val()});
        params.push({name: 'brand', value: $("#expensivesviewbrand").val()});
        var url="/accounting/calcsave";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#newcalcrow").empty().hide();
                init_calc();
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".expensive-cancel").find('i').unbind('click').click(function(){
        $("div#newcalcrow").empty().hide();
        init_calc();
    });
    $(".expensive-annually").find('i').unbind('click').click(function () {
        $("#date_type").val('year');
        var calc_id = $("#expensive").val();
        var url='/accounting/calc_edit_type';
        var params = new Array();
        params.push({name: 'date_type', value: $("#date_type").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                update_editform_expense(response, calc_id, 'year');
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".expensive-monthly").find('i').unbind('click').click(function(){
        $("#date_type").val('month');
        var calc_id = $("#expensive").val();
        var url='/accounting/calc_edit_type';
        var params = new Array();
        params.push({name: 'date_type', value: $("#date_type").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                update_editform_expense(response, calc_id, 'month');
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".expensive-weekly").find('i').unbind('click').click(function(){
        $("#date_type").val('week');
        var calc_id = $("#expensive").val();
        var url='/accounting/calc_edit_type';
        var params = new Array();
        params.push({name: 'date_type', value: $("#date_type").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                update_editform_expense(response, calc_id, 'week');
            } else {
                show_error(response);
            }
        },'json');
    });
    if ($("input#dateday_inpt").length > 0) {
        $("input#dateday_inpt").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'M d'
        });
    }
    // Update year / month / week sums
    $("#yearsum_inpt").unbind('change').change(function(){
        var calc_id = $("#expensive").val();
        var params = new Array();
        params.push({name: 'date_type', value: $("#date_type").val()});
        params.push({name: 'calc_id', value: $("#expensive").val()});
        params.push({name: 'brand', value: $("#expensivesviewbrand").val()});
        params.push({name: 'amount', value: $(this).val()});
        var url="/accounting/calc_edit_amount";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                update_editform_expensetotals(response, calc_id);
            } else {
                show_error(response);
            }
        },'json');
    })
    $("#monthsum_inpt").unbind('change').change(function(){
        var calc_id = $("#expensive").val();
        var params = new Array();
        params.push({name: 'date_type', value: $("#date_type").val()});
        params.push({name: 'calc_id', value: $("#expensive").val()});
        params.push({name: 'brand', value: $("#expensivesviewbrand").val()});
        params.push({name: 'amount', value: $(this).val()});
        var url="/accounting/calc_edit_amount";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                update_editform_expensetotals(response, calc_id);
            } else {
                show_error(response);
            }
        },'json');
    })
    $("#weeksum_inpt").unbind('change').change(function(){
        var calc_id = $("#expensive").val();
        var params = new Array();
        params.push({name: 'date_type', value: $("#date_type").val()});
        params.push({name: 'calc_id', value: $("#expensive").val()});
        params.push({name: 'brand', value: $("#expensivesviewbrand").val()});
        params.push({name: 'amount', value: $(this).val()});
        var url="/accounting/calc_edit_amount";
        $.post(url, params, function (response) {
            if (response.errors=='') {
                update_editform_expensetotals(response, calc_id);
            } else {
                show_error(response);
            }
        },'json');
    })
}

function update_editform_expense(response, calc_id, init_type) {
    if (calc_id=='0') {
        $("#newcalcrow").find(".expensive-annually").empty().html(response.data.yearcontent);
        $("#newcalcrow").find(".expensive-monthly").empty().html(response.data.monthcontent);
        $("#newcalcrow").find(".expensive-weekly").empty().html(response.data.weekcontent);
        $("#newcalcrow").find(".expensive-date").empty().html(response.data.daydatecontent);
        $("#newcalcrow").find(".expensive-quoter").empty().html(response.data.weektotal);
        $("#newcalcrow").find(".expensive-yearly").empty().html(response.data.yeartotal);
        $("#newcalcrow").find(".expensive-percent").empty().html(response.data.percentval);
    } else {
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-annually").empty().html(response.data.yearcontent);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-monthly").empty().html(response.data.monthcontent);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-weekly").empty().html(response.data.weekcontent);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-date").empty().html(response.data.daydatecontent);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-quoter").empty().html(response.data.weektotal);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-yearly").empty().html(response.data.yeartotal);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-percent").empty().html(response.data.percentval);
    }
    init_edit_calc();
    if (init_type=='year') {
        $("#yearsum_inpt").focus();
    } else if (init_type=='month') {
        $("#monthsum_inpt").focus();
    } else {
        $("#weeksum_inpt").focus();
    }
}

function update_editform_expensetotals(response, calc_id) {
    if (calc_id=='0') {
        $("#newcalcrow").find(".expensive-quoter").empty().html(response.data.weektotal);
        $("#newcalcrow").find(".expensive-yearly").empty().html(response.data.yeartotal);
        $("#newcalcrow").find(".expensive-percent").empty().html(response.data.percentval);
    } else {
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-quoter").empty().html(response.data.weektotal);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-yearly").empty().html(response.data.yeartotal);
        $(".datarow[data-calc='"+calc_id+"']").find(".expensive-percent").empty().html(response.data.percentval);
    }
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
            show_error(response);
        }
    }, 'json');

}


function delete_calcrow(calc_id) {
    var descr=$(".datarow[data-calc='"+calc_id+"']").find('div.expensive-description').text();
    if (confirm('Do you realy want to delete '+descr+'?')) {
        var url="/accounting/calcdelete";
        $.post(url,{'calc_id':calc_id},function(response){
            if (response.errors=='') {
                init_calc();
            } else {
                show_error(response);
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