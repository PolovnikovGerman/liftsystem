/* Fuction instead DOCUMENT READY */
function init_batches_management() {
    $("input.batchemail").unbind('click').click(function(){
        change_emailed(this);
    });
    $("input.batchreceiv").unbind('click').click(function(){
        change_received(this);
    })
    $("select#batchfilter").unbind('change').change(function(){
        change_batchview();
    });
    $("select#batchview_year").unbind('change').change(function(){
        change_batchview();
    });
    $("img.delbatchrow").unbind('click').click(function(){
        del_batchrow(this);
    })
    $("img.editbatchrow").unbind('click').click(function(){
        edit_batchrow(this);
    })
    $("div.batchdetailtitle_manual").unbind('click').click(function(){
        var batchdate=$(this).data('batchdate');
        add_manualbatch(batchdate);
    })
    /* edit_batchnote */
    $("div.batchpaytable_note").unbind('click').click(function(){
        edit_batchnote(this);
    })

}
/* Deeds of open (focus) of tab */
function init_batches() {
    var url="/finance/adminbatchesdata";
    var filter=$("#batchfilter").val();
    var current=$("input#batchcurrent").val();
    $("#loader").css('display','block');
    $.post(url, {'filter':filter, 'current':current}, function(response){
        $("#loader").css('display','none');
        if (response.errors=='') {
            $("div.batchcalendar").empty().html(response.data.calendar_view);
            $("div#batchdetailsview").empty().html(response.data.details);
            var destination = $("div.curday").offset().top;
            destination=parseInt(destination)-162;
            $("#calendar_date").animate({scrollTop: destination}, 1100 );

            $("#batchcalmaxdate").val(response.data.max_date);
            $("#batchcalmindate").val(response.data.min_date);
            /* View full data about customer & note */
            $("div.batchnoteview").bt({
                fill : '#FFFFFF',
                cornerRadius: 10,
                width: 160,
                padding: 10,
                strokeWidth: '2',
                positions: "top",
                strokeStyle : '#000000',
                strokeHeight: '18',
                cssClass: 'white_tooltip',
                cssStyles: {
                    color: '#000000'
                }
            });
            $("div.batchpaytable_customer").bt({
                fill : '#FFFFFF',
                cornerRadius: 10,
                width: 160,
                padding: 10,
                strokeWidth: '2',
                positions: "top",
                strokeStyle : '#000000',
                strokeHeight: '18',
                cssClass: 'white_tooltip',
                cssStyles: {
                    color: '#000000'
                }
            });
            $("div.batchcalend_dateinfo").each(function(){
                $("div#"+$(this).prop('id')).bt({
                    trigger: 'click',
                    ajaxCache: false,
                    width: '463px',
                    ajaxPath: ["$(this).attr('href')"]
                });
            });
            // Init manage elements
            init_batches_management();
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_received(obj) {
    var objid=obj.id;
    var batch_id=objid.substr(6);
    var filter=$("#batchfilter").val();
    var receiv=0;
    if ($("#"+objid).prop('checked')==true) {
        receiv=1;
    }
    var url="/finance/batchreceived";
    $.post(url, {'batch_id':batch_id, 'receiv':receiv, 'filter':filter}, function(response){
        if (response.errors=='') {
            /* rebuild calendar & current day view */
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            $("div#batch"+response.data.batch_due).empty().html(response.data.calendar_view);
            $("div.batchcalend_dateinfo").each(function(){
                $("div#"+$(this).prop('id')).bt({
                    trigger: 'click',
                    width: '463px',
                    ajaxPath: ["$(this).attr('href')"]
                });
            });
            $("span.pendcc").empty().html(response.data.pendcc);
            $("span.openterm").empty().html(response.data.terms);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            } else {
                if (receiv==1) {
                    $("#"+objid).prop('checked',false);
                } else {
                    $("#"+objid).prop('checked',true);
                }
            }
        }
    }, 'json')
}

function change_emailed(obj) {
    var objid=obj.id;
    var batch_id=objid.substr(6);
    var mail=0;
    if ($("#"+objid).prop('checked')==true) {
        mail=1;
    }
    var url="/finance/batchmailed";
    $.post(url, {'batch_id':batch_id, 'mail':mail}, function(response){
        if (response.errors=='') {
            if (mail==1) {
                $("#"+objid).parent('div').addClass('emailed');
            } else {
                $("#"+objid).parent('div').removeClass('emailed');
            }
        } else {
            show_error(response);
        }
    }, 'json')

}

function show_batchdetails(obj) {
    var batch_date=obj.id.substr(5);
    $("input#batchcurrent").val(batch_date);
    var filter=$("#batchfilter").val();
    var url="/finance/batchdetails";
    $.post(url, {'batch_date':batch_date,'filter':filter}, function(response){
        if (response.errors=='') {
            $("div#batchdetailsview").empty().html(response.data.content);
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_batchview() {
    var filter=$("#batchfilter").val();
    if (filter=='0') {
        $("select#batchview_year").css('visibility','hidden');
    } else {
        $("select#batchview_year").css('visibility','visible');
    }
    var params=new Array();
    params.push({name: 'filter', value: filter});
    params.push({name: 'current', value: $("input#batchcurrent").val()});
    params.push({name: 'year', value: $("select#batchview_year").val()});
    // var current=$("input#batchcurrent").val();
    var url="/finance/adminbatchesdata";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            //$("div.batchcalendar").empty().html(response.data.calendar_view);
            $("div#batchdetailsview").empty().html(response.data.details);
            //var offset=parseInt(response.data.offset)*76;
            // $('#calendar_date').stop().scrollTo( {top:'+'+response.data.offset+'px',left:'0px'}, 800 );
            // $("#batchcalmaxdate").val(response.data.max_date);
            // $("#batchcalmindate").val(response.data.min_date);
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function del_batchrow(obj) {
    var batch_id=obj.id.substr(11);
    var order_num=$("div#batchrow"+batch_id+" div.batchpaytable_ordernum").text();
    if (confirm('Delete batch for order '+order_num+'?')==true) {
        var url="/finance/delbatchrow";
        var filter=$("#batchfilter").val();
        $.post(url, {'batch_id':batch_id, 'filter':filter}, function(response){
            if (response.errors=='') {
                $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
                $("div#batch"+response.data.batch_due).empty().html(response.data.calendar_view);
                $("span.pendcc").empty().html(response.data.pendcc);
                $("span.openterm").empty().html(response.data.terms);
            } else {
                show_error(response);
            }

        }, 'json');
    }
}

function edit_batchrow(obj) {
    var batch_id=obj.id.substr(12);
    var url="/finance/edit_batch";
    $.post(url, {'batch_id':batch_id}, function(response){
        if (response.errors=='') {
            $("div#batchrow"+batch_id).empty().html(response.data.content);
            /* Init management */
            $("img#cancenbatchrow").click(function(){
                cancel_batchedit();
            })
            $("input#dueedit").datepicker({
                onSelect: function(date) {
                    batchrow_duedate(date);
                }
            });
            $("input.input_batch").change(function(){
                change_batchrowsum(this);
            })
            $("img#acceptbatchrow").click(function(){
                save_batchedit();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function batchrow_duedate(date) {
    var url="/finance/change_datedue";
    $.post(url, {'date':date}, function(response){
        if (response.errors=='') {
            $("input#datedue").val(response.data.datedue);
        } else {
            show_error(response);
        }
    }, 'json');
}

function change_batchrowsum(obj) {
    var inpid=obj.id;
    var newval=$("#"+inpid).val();
    if (newval!='') {
        var paymethod='';
        var batch_date=$("input#batch_date").val();
        switch(inpid) {
            case 'batch_vmd':
                paymethod='v';
                break;
            case 'batch_amex':
                paymethod='a';
                break;
            case 'batch_other':
                paymethod='o';
                break;
            case 'batch_term':
                paymethod='t';
                break;
        }
        var url="/finance/batch_paymethod";
        $.post(url, {'date':batch_date,'paymethod':paymethod}, function(response){
            if (response.errors=='') {
                $("form#editbatchdata div.batchpaytable_due").empty().html(response.data.dateeditinpt);
                $("input#datedue").val(response.data.datedue);
                if (response.data.edit_option=='1') {
                    $("input#dueedit").datepicker({
                        onSelect: function(date) {
                            batchrow_duedate(date);
                        }
                    });
                }
            } else {
                show_error(response);
            }
        }, 'json');


    }
}

function save_batchedit() {
    var dat=$("form#editbatchdata").serializeArray();
    var filter=$("#batchfilter").val();
    dat.push({name: "filter", value: filter});
    var url="/finance/save_batch";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            $("div#batch"+response.data.batch_due).empty().html(response.data.calendar_view);
            $("span.pendcc").empty().html(response.data.pendcc);
            $("span.openterm").empty().html(response.data.terms);
            if (response.data.calend_change=='2') {
                $("div#batch"+response.data.batch_due_second).empty().html(response.data.calendar_view_second);
            }
        } else {
            alert(response.errors);
            show_error(response);
        }
    }, 'json');
}

function cancel_batchedit() {
    var batch_id=$("form#editbatchdata input#batch_id").val();
    var filter=$("#batchfilter").val();
    var url="/finance/batch_canceledit";
    $.post(url, {'batch_id':batch_id,'filter':filter}, function(response){
        if (response.errors=='') {
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json')
}

function edit_batchnote(obj) {
    var batch_id=obj.id.substr(9);
    var url="/finance/batchnote";
    $.post(url, {'batch_id':batch_id}, function(response){
        if (response.errors=='') {
            show_popup('userdata');
            $("div#pop_content").empty().html(response.data.content);
            $("div.savebatchnote").click(function(){
                save_batchnote();
            })
        } else {
            alert(response.errors)
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function save_batchnote() {
    var batch_id=$("form#batchnoteedit input#batch_id").val();
    var batch_note=$("form#batchnoteedit textarea#batch_note").val();
    var filter=$("select#batchfilter").val();
    var url="/finance/save_batchnote";
    $.post(url, {'batch_id':batch_id, 'batch_note':batch_note, 'filter':filter}, function(response){
        if (response.errors=='') {
            disablePopup();
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            $("div.batchnoteview").bt({
                fill : '#FFFFFF',
                cornerRadius: 10,
                width: 160,
                padding: 10,
                strokeWidth: '2',
                positions: "top",
                strokeStyle : '#000000',
                strokeHeight: '18',
                cssClass: 'white_tooltip',
                cssStyles: {
                    color: '#000000'
                }
            });
            $("div.batchpaytable_customer").bt({
                fill : '#FFFFFF',
                cornerRadius: 10,
                width: 160,
                padding: 10,
                strokeWidth: '2',
                positions: "top",
                strokeStyle : '#000000',
                strokeHeight: '18',
                cssClass: 'white_tooltip',
                cssStyles: {
                    color: '#000000'
                }
            });
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function add_manualbatch(batchdate) {
    var url="/finance/batch_addmanual";
    var filter=$("#batchfilter").val();
    if (confirm('Add Manual Batch?')) {
        $.post(url, {'batchdate':batchdate, 'filter':filter}, function(response){
            if (response.errors=='') {
                $("div#batchday"+batchdate).empty().html(response.data.content);
                // init_batchcontent();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}