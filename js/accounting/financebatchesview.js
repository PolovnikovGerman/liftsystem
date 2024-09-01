function init_batches_content() {
    init_batches();
    // Change Brand
    $("#finbatchesbrandmenu").find("div.brandchoseval").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#finbatchesbrand").val(brand);
        $("#finbatchesbrandmenu").find("div.brandchoseval").each(function(){
            var curbrand=$(this).data('brand');
            if (curbrand==brand) {
                $(this).empty().html('<i class="fa fa-check-square-o" aria-hidden="true"></i>').addClass('active');
                $("#finbatchesbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").addClass('active');
            } else {
                $(this).empty().html('<i class="fa fa-square-o" aria-hidden="true"></i>').removeClass('active');
                $("#finbatchesbrandmenu").find("div.brandlabel[data-brand='"+curbrand+"']").removeClass('active');
            }
        });
        init_batches();
    });
}
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
    var url="/accounting/adminbatchesdata";
    var params = new Array();
    params.push({name: 'filter', value: $("#batchfilter").val()});
    params.push({name: 'current', value: $("input#batchcurrent").val()});
    params.push({name: 'brand', value: $("#finbatchesbrand").val()});
    $("#loader").show();
    $.post(url, params , function(response){
        if (response.errors=='') {
            $("div.batchcalendar").empty().html(response.data.calendar_view);
            $("div#batchdetailsview").empty().html(response.data.details);
            var destination = $("div.curday").offset().top;
            destination=parseInt(destination)-162;
            $("#calendar_date").animate({scrollTop: destination}, 1100 );

            $("#batchcalmaxdate").val(response.data.max_date);
            $("#batchcalmindate").val(response.data.min_date);
            /* View full data about customer & note */
            leftmenu_alignment();
            $("div.batchnoteview").qtip({
                content: {
                    attr: 'data-content'
                },
                position: {
                    my: 'bottom left',
                    at: 'top right',
                },
                style: 'qtip-light'
            });
            $("div.batchpaytable_customer").qtip({
                content: {
                    attr: 'data-content'
                },
                position: {
                    my: 'bottom left',
                    at: 'top right',
                },
                style: 'qtip-light'
            });
            /* $("div.batchnoteview").bt({
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
            }); */
            /* $("div.batchpaytable_customer").bt({
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
            }); */
            // Init manage elements
            $("#loader").hide();
            init_batches_management();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function change_received(obj) {
    var objid=obj.id;
    var batch_id=objid.substr(6);
    var receiv=0;
    if ($("#"+objid).prop('checked')==true) {
        receiv=1;
    }
    var url="/accounting/batchreceived";
    var params = new Array();
    params.push({name: 'batch_id', value: batch_id});
    params.push({name: 'receiv', value: receiv});
    params.push({name: 'filter', value: $("#batchfilter").val()});
    params.push({name: 'brand', value: $("#finbatchesbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            /* rebuild calendar & current day view */
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            $("div#batch"+response.data.batch_due).empty().html(response.data.calendar_view);
            $("div.batchcalend_dateinfo").each(function(){
                /*
                $("div#"+$(this).prop('id')).bt({
                    trigger: 'click',
                    width: '463px',
                    ajaxPath: ["$(this).attr('href')"]
                });
                */
            });
            $("span.pendcc").empty().html(response.data.pendcc);
            $("span.openterm").empty().html(response.data.terms);
            init_batches_management();
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
    var url="/accounting/batchmailed";
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
    var url="/accounting/batchdetails";
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
    params.push({name: 'brand', value: $("#finbatchesbrand").val()});
    var url="/accounting/adminbatchesdata";
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div#batchdetailsview").empty().html(response.data.details);
            init_batches_management();
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
        var url="/accounting/delbatchrow";
        var params = new Array();
        params.push({name: 'batch_id', value :batch_id});
        params.push({name: 'filter', value: $("#batchfilter").val()});
        params.push({name: 'brand', value: $("#finbatchesbrand").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
                $("div#batch"+response.data.batch_due).empty().html(response.data.calendar_view);
                $("span.pendcc").empty().html(response.data.pendcc);
                $("span.openterm").empty().html(response.data.terms);
                init_batches_management();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}

function edit_batchrow(obj) {
    var batch_id=obj.id.substr(12);
    var url="/accounting/edit_batch";
    $.post(url, {'batch_id':batch_id}, function(response){
        if (response.errors=='') {
            $("div#batchrow"+batch_id).empty().html(response.data.content);
            /* Init management */
            $("img#cancenbatchrow").click(function(){
                cancel_batchedit(batch_id);
            })
            $("input#dueedit").datepicker();
            $("input#dueedit").unbind('change').change(function(){
                var newdate = $(this).val();
                batchrow_duedate(newdate);
            })
            $("input.input_batch").unbind('change').change(function(){
                change_batchrowsum(this);
            })
            $("img#acceptbatchrow").unbind('click').click(function(){
                save_batchedit();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}

function batchrow_duedate(date) {
    var url="/accounting/change_datedue";
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
        var url="/accounting/batch_paymethod";
        $.post(url, {'date':batch_date,'paymethod':paymethod}, function(response){
            if (response.errors=='') {
                $("form#editbatchdata div.batchpaytable_due").empty().html(response.data.dateeditinpt);
                $("input#datedue").val(response.data.datedue);
                if (response.data.edit_option=='1') {
                    $("input#dueedit").datepicker();
                    $("input#dueedit").unbind('change').change(function(){
                        var newdate = $(this).val();
                        batchrow_duedate(newdate);
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
    dat.push({name: 'brand', value: $("#finbatchesbrand").val()});

    var url="/accounting/save_batch";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            $("div#batch"+response.data.batch_due).empty().html(response.data.calendar_view);
            $("span.pendcc").empty().html(response.data.pendcc);
            $("span.openterm").empty().html(response.data.terms);
            if (response.data.calend_change=='2') {
                $("div#batch"+response.data.batch_due_second).empty().html(response.data.calendar_view_second);
            }
            init_batches_management();
        } else {
            alert(response.errors);
            show_error(response);
        }
    }, 'json');
}

function cancel_batchedit(batch) {
    var url="/accounting/batch_canceledit";
    var params = new Array();
    params.push({name: 'batch_id', value: batch}); // $("form#editbatchdata input#batch_id").val()
    params.push({name: 'filter', value: $("#batchfilter").val()});
    params.push({name: 'brand', value: $("#finbatchesbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            init_batches_management();
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
    var url="/accounting/batchnote";
    $.post(url, {'batch_id':batch_id}, function(response){
        if (response.errors=='') {
            // show_popup('userdata');
            // $("div#pop_content").empty().html(response.data.content);
            $("#pageModal").find('div.modal-dialog').css('width','470px');
            $("#pageModalLabel").empty().html('Order Note');
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
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
    var url="/accounting/save_batchnote";
    var params = new Array();
    params.push({name: 'batch_id', value: $("form#batchnoteedit input#batch_id").val()});
    params.push({name: 'batch_note', value: $("form#batchnoteedit textarea#batch_note").val()});
    params.push({name: 'filter', value: $("select#batchfilter").val()});
    params.push({name: 'brand', value: $("#finbatchesbrand").val()});
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
            $("div#batchday"+response.data.batch_date).empty().html(response.data.content);
            /* $("div.batchnoteview").bt({
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
            }); */
            /* $("div.batchpaytable_customer").bt({
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
            }); */
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    }, 'json');
}

function add_manualbatch(batchdate) {
    var url="/accounting/batch_addmanual";
    if (confirm('Add Manual Batch?')) {
        var params = new Array();
        params.push({name: 'batchdate', value: batchdate});
        params.push({name: 'filter', value: $("#batchfilter").val()});
        params.push({name: 'brand', value: $("#finbatchesbrand").val()});
        $.post(url, params, function(response){
            if (response.errors=='') {
                $("div#batchday"+batchdate).empty().html(response.data.content);
            } else {
                show_error(response);
            }
        }, 'json');
    }
}