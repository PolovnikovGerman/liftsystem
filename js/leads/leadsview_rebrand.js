function init_leadsview() {
    init_customform_interest();
    init_webquest_interest();
}

function init_customform_interest() {
    var params = new Array();
    params.push({name: 'brand', value: $("#leadviewbrand").val()});
    var url = '/leads/customform_interest';
    $.post(url, params, function (response){
        if (response.errors=='') {
            $("#sbcustomformstable").empty().html(response.data.content);
            new SimpleBar(document.getElementById('sbcustomformstable'), { autoHide: false })
            $(".newunassign_tasks_total[data-task='sbcustomform']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='sbcustomform']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='sbcustomform']").addClass('emptycontent');
                $("#sbcustomformstable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='sbcustomform']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='sbcustomform']").removeClass('emptycontent');
                $("#sbcustomformstable").removeClass('emptycontent');
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
            new SimpleBar(document.getElementById('webquestiontable'), { autoHide: false })
            $(".newunassign_tasks_total[data-task='webquestions']").empty().html(response.data.total);
            if (parseInt(response.data.cntrec)==0) {
                $(".newunassign_taskheader[data-task='webquestions']").addClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='webquestions']").addClass('emptycontent');
                $("#webquestiontable").addClass('emptycontent');
            } else {
                $(".newunassign_taskheader[data-task='webquestions']").removeClass('emptycontent');
                $(".newunassign_tasksubheader[data-task='webquestions']").removeClass('emptycontent');
                $("#webquestiontable").removeClass('emptycontent');
            }
        } else {
            show_error(response)
        }
    },'json');
}