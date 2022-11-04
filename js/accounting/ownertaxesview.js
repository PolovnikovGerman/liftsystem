function init_ownertax_content() {
    init_ownertax();
    // Change Brand
    $("#ownertaxesbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#ownertaxesbrand").val(brand);
        $("#ownertaxesbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#ownertaxesbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#ownertaxesbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_ownertax();
    });
}

function init_ownertax() {
    var url="/accounting/ownnertaxes_data";
    var params = new Array();
    params.push({name: 'brand', value: $("#ownertaxesbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div#ownertax").empty().html(response.data.content);
            leftmenu_alignment();
            $("div.ownertaxessave").hide();
            init_ownertax_manage();
        } else {
            show_error(response);
        }
    },'json');
}

function init_ownertax_manage() {
    $("input.ownertaxdatainput").unbind('change').change(function(){
        var calc_type=$(this).data('calc');
        var fldname=$(this).data('fld');
        var params=new Array();
        params.push({name: 'calcsession', value: $("input#calcsessionid").val()});
        params.push({name: 'calc_type', value: calc_type});
        params.push({name: 'fldname', value: fldname});
        params.push({name: 'newval', value: $(this).val()});
        var url="/accounting/ownnertaxes_change";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.ownertaxessave").show();
                $("input.ownertaxdatainput[data-calc='"+calc_type+"'][data-fld='"+fldname+"']").val(response.data.newval);
                // Update View
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='total_income']").empty().html(response.data.total_income);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='taxable_income']").empty().html(response.data.taxable_income);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='fed_taxes']").empty().html(response.data.fed_taxes);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='fed_taxes_due']").empty().html(response.data.fed_taxes_due);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='fed_pay']").empty().html(response.data.fed_pay);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='state_taxes']").empty().html(response.data.state_taxes);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='state_taxes_due']").empty().html(response.data.state_taxes_due);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='state_pay']").empty().html(response.data.state_pay);
                $("div.taxvalue[data-calc='"+calc_type+"'][data-fld='take_home']").empty().html(response.data.take_home);
            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.turnoffsign").unbind('click').click(function(){
        var newval=1;
        if ($(this).hasClass('switchon')) {
            newval=0;
        }
        var params=new Array();
        params.push({name: 'calcsession', value: $("input#calcsessionid").val()});
        params.push({name: 'od_incl', value: newval});
        var url="/accounting/ownnertaxes_odincl";
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div.ownertaxessave").show();
                if (newval==1) {
                    $("div.turnoffsign").removeClass('switchoff').addClass('switchon').empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>');
                } else {
                    $("div.turnoffsign").removeClass('switchon').addClass('switchoff').empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>');
                }
                // Update View
                $("div.taxvalue[data-calc='single'][data-fld='total_income']").empty().html(response.data.single_total_income);
                $("div.taxvalue[data-calc='single'][data-fld='taxable_income']").empty().html(response.data.single_taxable_income);
                $("div.taxvalue[data-calc='single'][data-fld='fed_taxes']").empty().html(response.data.single_fed_taxes);
                $("div.taxvalue[data-calc='single'][data-fld='fed_taxes_due']").empty().html(response.data.single_fed_taxes_due);
                $("div.taxvalue[data-calc='single'][data-fld='fed_pay']").empty().html(response.data.single_fed_pay);
                $("div.taxvalue[data-calc='single'][data-fld='state_taxes']").empty().html(response.data.single_state_taxes);
                $("div.taxvalue[data-calc='single'][data-fld='state_taxes_due']").empty().html(response.data.single_state_taxes_due);
                $("div.taxvalue[data-calc='single'][data-fld='state_pay']").empty().html(response.data.single_state_pay);
                $("div.taxvalue[data-calc='single'][data-fld='take_home']").empty().html(response.data.single_take_home);

                $("div.taxvalue[data-calc='joint'][data-fld='total_income']").empty().html(response.data.joint_total_income);
                $("div.taxvalue[data-calc='joint'][data-fld='taxable_income']").empty().html(response.data.joint_taxable_income);
                $("div.taxvalue[data-calc='joint'][data-fld='fed_taxes']").empty().html(response.data.joint_fed_taxes);
                $("div.taxvalue[data-calc='joint'][data-fld='fed_taxes_due']").empty().html(response.data.joint_fed_taxes_due);
                $("div.taxvalue[data-calc='joint'][data-fld='fed_pay']").empty().html(response.data.joint_fed_pay);
                $("div.taxvalue[data-calc='joint'][data-fld='state_taxes']").empty().html(response.data.joint_state_taxes);
                $("div.taxvalue[data-calc='joint'][data-fld='state_taxes_due']").empty().html(response.data.joint_state_taxes_due);
                $("div.taxvalue[data-calc='joint'][data-fld='state_pay']").empty().html(response.data.joint_state_pay);
                $("div.taxvalue[data-calc='joint'][data-fld='take_home']").empty().html(response.data.joint_take_home);

            } else {
                show_error(response);
            }
        },'json');
    });
    $("div.ownertaxessave").unbind('click').click(function(){
        var params=new Array();
        params.push({name: 'calcsession', value: $("input#calcsessionid").val()});
        params.push({name: 'brand', value: $("#ownertaxesbrand").val()});
        var url="/accounting/ownnertaxes_save";
        $.post(url, params, function(response){
            if (response.errors=='') {
                init_ownertax();
            } else {
                show_error(response);
            }
        },'json');
    });
    // Change Project Base
    $("div.ownertaxcontent").find("div.inputplace").unbind('click').click(function(){
        if ($(this).hasClass('switchon')){
        } else {
            var pace=$(this).data('pace');
            var baseval=$(this).data('proj');
            if (pace=='income') {
                $("input#taxownincome").val(baseval);
            } else {
                $("input#taxownexpence").val(baseval);
            }

            var newval=0;
            if ($("div.ownertaxcontent").find("div.turnoffsign").hasClass('switchon')) {
                newval=1;
            }
            $("div.ownertaxcontent").find("div.inputplace[data-pace='"+pace+"']").removeClass('switchon').empty().html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
            $(this).addClass('switchon').empty().html('<i class="fa fa-check-circle-o" aria-hidden="true"></i>');
            var params=new Array();
            params.push({name: 'calcsession', value: $("input#calcsessionid").val()});
            params.push({name: 'paceincome', value: $("input#taxownincome").val()});
            params.push({name: 'paceexpense', value: $("input#taxownexpence").val()});
            params.push({name: 'od_incl', value: newval});
            var url="/finance/ownnertaxes_netprofit";
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $("div.ownertaxtotalsarea").find('div.ownertxprofit').empty().html(response.data.companyprofit);
                    $("div.ownertaxtotalsarea").find('div.ownertxvalue').empty().html(response.data.ownership);
                    $("div.taxvalue[data-calc='single'][data-fld='total_income']").empty().html(response.data.single_total_income);
                    $("div.taxvalue[data-calc='single'][data-fld='taxable_income']").empty().html(response.data.single_taxable_income);
                    $("div.taxvalue[data-calc='single'][data-fld='fed_taxes']").empty().html(response.data.single_fed_taxes);
                    $("div.taxvalue[data-calc='single'][data-fld='fed_taxes_due']").empty().html(response.data.single_fed_taxes_due);
                    $("div.taxvalue[data-calc='single'][data-fld='fed_pay']").empty().html(response.data.single_fed_pay);
                    $("div.taxvalue[data-calc='single'][data-fld='state_taxes']").empty().html(response.data.single_state_taxes);
                    $("div.taxvalue[data-calc='single'][data-fld='state_taxes_due']").empty().html(response.data.single_state_taxes_due);
                    $("div.taxvalue[data-calc='single'][data-fld='state_pay']").empty().html(response.data.single_state_pay);
                    $("div.taxvalue[data-calc='single'][data-fld='take_home']").empty().html(response.data.single_take_home);
                    $("div.taxvalue[data-calc='single'][data-fld='owner_drawer']").empty().html(response.data.ownership);

                    $("div.taxvalue[data-calc='joint'][data-fld='total_income']").empty().html(response.data.joint_total_income);
                    $("div.taxvalue[data-calc='joint'][data-fld='taxable_income']").empty().html(response.data.joint_taxable_income);
                    $("div.taxvalue[data-calc='joint'][data-fld='fed_taxes']").empty().html(response.data.joint_fed_taxes);
                    $("div.taxvalue[data-calc='joint'][data-fld='fed_taxes_due']").empty().html(response.data.joint_fed_taxes_due);
                    $("div.taxvalue[data-calc='joint'][data-fld='fed_pay']").empty().html(response.data.joint_fed_pay);
                    $("div.taxvalue[data-calc='joint'][data-fld='state_taxes']").empty().html(response.data.joint_state_taxes);
                    $("div.taxvalue[data-calc='joint'][data-fld='state_taxes_due']").empty().html(response.data.joint_state_taxes_due);
                    $("div.taxvalue[data-calc='joint'][data-fld='state_pay']").empty().html(response.data.joint_state_pay);
                    $("div.taxvalue[data-calc='joint'][data-fld='take_home']").empty().html(response.data.joint_take_home);
                    $("div.taxvalue[data-calc='joint'][data-fld='owner_drawer']").empty().html(response.data.ownership);
                } else {
                    show_error(response);
                }
            },'json');
        }
    });

}
