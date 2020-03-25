function init_taskalertsys() {
    /* checkbox */
    if ($("input#noart_alert").prop('checked')==false) {
        $("div#noartcommontimes").hide();
        $("div#noartrushtimes").hide();
    }
    $("input#noart_alert").change(function(){
        var val=0;
        if ($("input#noart_alert").prop('checked')==true) {
            val=1;
            $("div#noartcommontimes").show();
            $("div#noartrushtimes").show();
        } else {
            $("div#noartcommontimes").hide();
            $("div#noartrushtimes").hide();
        }
        change_alert('noart_alert',val);
    })
    $("#noart_common_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#noart_common_days").val())
    });
    $("#noart_common_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#noart_common_hours").val())
    });
    $("#noart_rush_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#noart_rush_days").val())
    });
    $("#noart_rush_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#noart_rush_hours").val())
    });
    $("input#noart_common_days").on('change',function(){
        change_alerttime('noart_common_days',$("#noart_common_days").val());
    });
    $("input#noart_common_hours").on('change',function(){
        change_alerttime('noart_common_hours',$("#noart_common_hours").val());
    });
    $("input#noart_rush_days").on('change',function(){
        change_alerttime('noart_rush_days',$("#noart_rush_days").val());
    });
    $("input#noart_rush_hours").on('change',function(){
        change_alerttime('noart_rush_hours',$("#noart_rush_hours").val());
    });

    /* Redraw Section */
    if ($("input#redraw_alert").prop('checked')==false) {
        $("div#redrawcommontimes").hide();
        $("div#redrawrushtimes").hide();
    }
    $("input#redraw_alert").change(function(){
        var val=0;
        if ($("input#redraw_alert").prop('checked')==true) {
            val=1;
            $("div#redrawcommontimes").show();
            $("div#redrawrushtimes").show();
        } else {
            $("div#redrawcommontimes").hide();
            $("div#redrawrushtimes").hide();
        }
        change_alert('redraw_alert',val);
    })
    $("#redraw_common_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#redraw_common_days").val())
    });
    $("#redraw_common_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#redraw_common_hours").val())
    });
    $("#redraw_rush_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#redraw_rush_days").val())
    });
    $("#redraw_rush_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#redraw_rush_hours").val())
    });
    $("input#redraw_common_days").on('change',function(){
        change_alerttime('redraw_common_days',$("#redraw_common_days").val());
    });
    $("input#redraw_common_hours").on('change',function(){
        change_alerttime('redraw_common_hours',$("#redraw_common_hours").val());
    });
    $("input#redraw_rush_days").on('change',function(){
        change_alerttime('redraw_rush_days',$("#redraw_rush_days").val());
    });
    $("input#redraw_rush_hours").on('change',function(){
        change_alerttime('redraw_rush_hours',$("#redraw_rush_hours").val());
    });
    /* TO PROOF Section */
    if ($("input#toproof_alert").prop('checked')==false) {
        $("div#toproofcommontimes").hide();
        $("div#toproofrushtimes").hide();
    }
    $("input#toproof_alert").change(function(){
        var val=0;
        if ($("input#toproof_alert").prop('checked')==true) {
            val=1;
            $("div#toproofcommontimes").show();
            $("div#toproofrushtimes").show();
        } else {
            $("div#toproofcommontimes").hide();
            $("div#toproofrushtimes").hide();
        }
        change_alert('toproof_alert',val);
    })
    $("#toproof_common_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#toproof_common_days").val())
    });
    $("#toproof_common_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#toproof_common_hours").val())
    });
    $("#toproof_rush_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#toproof_rush_days").val())
    });
    $("#toproof_rush_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#toproof_rush_hours").val())
    });
    $("input#toproof_common_days").on('change',function(){
        change_alerttime('toproof_common_days',$("#toproof_common_days").val());
    });
    $("input#toproof_common_hours").on('change',function(){
        change_alerttime('toproof_common_hours',$("#toproof_common_hours").val());
    });
    $("input#toproof_rush_days").on('change',function(){
        change_alerttime('toproof_rush_days',$("#toproof_rush_days").val());
    });
    $("input#toproof_rush_hours").on('change',function(){
        change_alerttime('toproof_rush_hours',$("#toproof_rush_hours").val());
    });
    /* Need Approval Section */
    if ($("input#needapproval_alert").prop('checked')==false) {
        $("div#needapprovalcommontimes").hide();
        $("div#needapprovalrushtimes").hide();
    }
    $("input#needapproval_alert").change(function(){
        var val=0;
        if ($("input#needapproval_alert").prop('checked')==true) {
            val=1;
            $("div#needapprovalcommontimes").show();
            $("div#needapprovalrushtimes").show();
        } else {
            $("div#needapprovalcommontimes").hide();
            $("div#needapprovalrushtimes").hide();
        }
        change_alert('needapproval_alert',val);
    })
    $("#needapproval_common_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#needapproval_common_days").val())
    });
    $("#needapproval_common_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#needapproval_common_hours").val())
    });
    $("#needapproval_rush_days").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 30,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#needapproval_rush_days").val())
    });
    $("#needapproval_rush_hours").SpinnerControl({
        type: 'range',
        typedata: {
            min: 0,
            max: 23,
            interval: 1,
            decimalplaces:0
        },
        width: '35px',
        looping:true,
        defaultVal:parseInt($("#needapproval_rush_hours").val())
    });
    $("input#needapproval_common_days").on('change',function(){
        change_alerttime('needapproval_common_days',$("#needapproval_common_days").val());
    });
    $("input#needapproval_common_hours").on('change',function(){
        change_alerttime('needapproval_common_hours',$("#needapproval_common_hours").val());
    });
    $("input#needapproval_rush_days").on('change',function(){
        change_alerttime('needapproval_rush_days',$("#needapproval_rush_days").val());
    });
    $("input#needapproval_rush_hours").on('change',function(){
        change_alerttime('needapproval_rush_hours',$("#needapproval_rush_hours").val());
    });
}

/* change alert status */
function change_alert(alrtid,val) {
    var url="/admin/taskalert_save";
    $.post(url, {'alert_id':alrtid,'value':val}, function(response){
        if (response.errors=='') {

        } else {
            show_error(response);
        }
    }, 'json');
}

function change_alerttime(alrttime, value) {
    var url="/admin/taskalerttime_save";
    $.post(url, {'alert_time':alrttime,'value':value}, function(response){
        if (response.errors=='') {
        } else {
            show_error(response);
        }
    }, 'json');

}