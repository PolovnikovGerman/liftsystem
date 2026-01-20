function init_leadsview() {
    init_customform_interest();
    init_webquest_interest();
    init_webquotes_interest();
    init_proofrequest_interest();
    init_repeatreminder();
}

function init_customform_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/customform_interest';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#sbcustomformstable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='sbcustomform']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='sbcustomform']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='sbcustomform']").addClass('emptycontent');
                $("#sbcustomformstable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='sbcustomform']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='sbcustomform']").removeClass('emptycontent');
                $("#sbcustomformstable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('sbcustomformstable'), { autoHide: false })
            }
        } else {
            show_error(response);
        }
    },'json');
}

function init_webquest_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/webquest_interest';
    $.post(url,params, function (response){
        if (response.errors=='') {
            $("#webquestiontable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='webquestions']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='webquestions']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='webquestions']").addClass('emptycontent');
                $("#webquestiontable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='webquestions']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='webquestions']").removeClass('emptycontent');
                $("#webquestiontable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('webquestiontable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_webquotes_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/webquotes_interest';
    $.post(url,params, function (response){
        if (response.errors=='') {
            $("#onlinequotetable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='onlinequotes']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='onlinequotes']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='onlinequotes']").addClass('emptycontent');
                $("#onlinequotetable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='onlinequotes']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='onlinequotes']").removeClass('emptycontent');
                $("#onlinequotetable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('onlinequotetable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_proofrequest_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/proofrequest_interest';
    $.post(url,params, function (response){
        if (response.errors=='') {
            $("#proofrequesttable").empty().html(response.data.content);
            $(".newunassign_tasks_total[data-task='proofrequests']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='proofrequests']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='proofrequests']").addClass('emptycontent');
                $("#proofrequesttable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='proofrequests']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='proofrequests']").removeClass('emptycontent');
                $("#proofrequesttable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('proofrequesttable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}

function init_repeatreminder() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    params.push({name: 'customorders', value: $("#leadviewremindcustom").val()});
    params.push({name: 'orderrich', value: $("#leadviewremindrichy").val()});
    params.push({name: 'date', value: $("#leadviewremindmonth").val()});
    var url = '/leads/reminder_interest';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#repeatremandtable").empty().html(response.data.content);
            if (parseInt(response.data.cntrec)==0) {
                $(".repeatremandheader").addClass('emptycontent');
                $(".repeatremand_subheader").addClass('emptycontent');
                $("#repeatremandtable").addClass('emptycontent');
            } else {
                $(".repeatremandheader").removeClass('emptycontent');
                $(".repeatremand_subheader").removeClass('emptycontent');
                $("#repeatremandtable").removeClass('emptycontent');
                new SimpleBar(document.getElementById('repeatremandtable'), { autoHide: false })
            }
        } else {
            show_error(response)
        }
    },'json');
}