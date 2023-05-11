function init_users() {
    initUserPagination();
}

function initUserPagination() {
    // count entries inside the hidden content
    var num_entries = $('#totalusers').val();
    var perpage = $("#perpageusr").val();
    if (parseInt(num_entries) < parseInt(perpage)) {
        $(".Pagination").empty();
        pageUserCallback(0);
    } else {
        var curpage = $("#curpage").val();
        // Create content inside pagination element
        $(".Pagination").mypagination(num_entries, {
            current_page: curpage,
            callback: pageUserCallback,
            items_per_page: perpage, // Show only one item per page
            load_first_page: true,
            num_edge_entries : 1,
            num_display_entries : 5,
            prev_text : '<<',
            next_text : '>>'
        });
    }

}

function pageUserCallback(page_index) {
    var params=new Array();
    params.push({name:'offset', value:page_index});
    params.push({name:'limit',value:$("#perpageusr").val()});
    params.push({name:'order_by',value:$("#orderusr").val()});
    params.push({name:'direction', value:$("#direcusr").val()});
    params.push({name:'maxval', value:$('#totalusers').val()});
    var url='/admin/usersdata';
    $("#loader").show();
    $.post(url,params,function(response){
        if (response.errors=='') {
            $("div#userinfo").empty().html(response.data.content);
            leftmenu_alignment();
            $("#loader").hide();
            /* Change view */
            init_usercontent_management();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');
}

function init_usercontent_management() {
    $(".deleteuser").unbind('click').click(function(){
        var user_id=$(this).data('user');
        del_user(user_id);
    })
    $(".edituser").unbind('click').click(function(){
        var user_id=$(this).data('user');
        edit_user(user_id);
    });
    $(".userstatus").unbind('click').click(function(){
        var user_id=$(this).data('user');
        var status = $(this).find('input.userstatusval').val();
        change_status(user_id, status);
    });
    $("#addnewuserbtn").click(function() {
        add_user();
    });
}

function init_users_management() {
    $("#addnewuserbtn").click(function() {
        add_user();
    });
    $("#popupContactClose").click(function(){
        disablePopup();
    })
    /*$('#roledescr').bt({
        ajaxPath: 'users/roles',
        width: 180,
        padding: 20,
        strokeWidth: 2,
        positions: "bottom"
    });*/
}

function change_status(user_id, action) {
    var msg='';
    if (action=='1') {
        msg='Suspend User activity?';
    } else {
        msg='Activate User account?';
    }
    if (confirm(msg)) {
        var url="/admin/user_changestatus/";
        params = new Array();
        params.push({name: 'user_id', value :user_id});
        params.push({name: 'status', value : action});
        $.post(url, params, function(response){
            if (response.errors=='') {
                if (response.data.newstatus == '1') {
                    $("div.userstatus[data-user='"+user_id+"']").empty().html('<input type="hidden" class="userstatusval" value="1"/><i class="fa fa-pause-circle-o" aria-hidden="true"></i>');
                } else {
                    $("div.userstatus[data-user='"+user_id+"']").empty().html('<input type="hidden" class="userstatusval" value="2"/><i class="fa fa-play-circle-o" aria-hidden="true"></i>');
                }
                $("div.status[data-user='"+user_id+"']").empty().html(response.data.status_txt);
            } else {
                show_error(response);
            }
        }, 'json');
    }
}


function save_user() {
    // var dat=$("form#userdat").serializeArray();
    var url="/admin/userdata_save";
    var params = new Array();
    params.push({name: 'session', value: $("#session").val()});
    $("#loader").show();
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("#pageModal").modal('hide');
            initUserPagination();
            $("#loader").hide();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    }, 'json');
}

function edit_user(user_id) {
    var url="/admin/user_editdata";
    $.post(url,{'user_id':user_id},function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#pageModal").find('div.modal-dialog').css('width','948px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* Init save button */
            user_edit_init();
        } else {
            show_error(response);
        }
    },'json');
}

function user_edit_init() {
    $("ul#sbtree").Tree();
    $("ul#srtree").Tree();
    $("ul#commontree").Tree();
    $(".webbrandtitle:first").addClass('active');
    var menuview=$(".webbrandtitle:first").data('brand');
    $(".menuitemsview[data-brand='"+menuview+"']").addClass('active');
    $("#saveusr").click(function(){
        save_user();
    });
    user_edit_manage();
}

function user_edit_manage() {
    $('input.pageuseraccess').unbind('change').change(function () {
        var menuitem = $(this).data('menuitem');
        var brand = $(this).data('brand');
        var newval = 0;
        if ($(this).prop('checked')==true) {
            newval=1;
        }
        // Update menu
        var url = '/admin/changepagepermission';
        var params = new Array();
        params.push({name: 'menuitem', value: menuitem});
        params.push({name: 'brand', value: brand});
        params.push({name: 'newval', value: newval});
        params.push({name: 'session', value: $("#session").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                var child = response.data.child;
                for (i=0; i<response.data.child_count; i++) {
                    $('select.sitesaccessselect[data-menuitem="'+child[i]+'"]').val(response.data.newval);
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $('select.sitesaccessselect').unbind('change').change(function(){
        var menuitem = $(this).data('menuitem');
        var newacc = $(this).val();
        var url = '/admin/changesiteaccess';
        var params = new Array();
        params.push({name: 'menuitem', value: menuitem});
        params.push({name: 'newval', value: newacc});
        params.push({name: 'session', value: $("#session").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (newacc=='') {
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').prop('checked',false);
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').parent('label.jquery-tree-title').removeClass('jquery-tree-checked').addClass('jquery-tree-unchecked');
                } else {
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').prop('checked',true);
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').parent('label.jquery-tree-title').removeClass('jquery-tree-unchecked').addClass('jquery-tree-checked');
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    // Click on check box
    $("div.brandaccesscheckfld").unbind('click').click(function () {
        var menuitem = $(this).data('menuitem');
        var brand = $(this).data('brand')
        var url = '/admin/changebrandaccess';
        var params = new Array();
        params.push({name: 'menuitem', value: menuitem});
        params.push({name: 'brand', value: brand});
        params.push({name: 'session', value: $("#session").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (parseInt(response.data.newacc)==0) {
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').prop('checked',false);
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').parent('label.jquery-tree-title').removeClass('jquery-tree-checked').addClass('jquery-tree-unchecked');
                } else {
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').prop('checked',true);
                    $('input.pageuseraccess[data-menuitem="'+menuitem+'"]').parent('label.jquery-tree-title').removeClass('jquery-tree-unchecked').addClass('jquery-tree-checked');
                }
                $("div.brandaccesscheckfld[data-menuitem='"+menuitem+"'][data-brand='"+brand+"']").empty().html(response.data.content);
            } else {
                show_error(response);
            }
        },'json');
    });
    /* IP RESTRICT */
    $("div.addrestict").unbind('click').click(function(){
        var url = '/admin/userip_restrict_add';
        var params = new Array();
        params.push({name: 'session', value: $("#session").val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                $("#iprestrictarea").empty().html(response.data.content);
                user_edit_manage();
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.iprestrict").unbind('change').change(function () {
        var key = $(this).data('key');
        var url = '/admin/userip_restrict_edit';
        var params = new Array();
        params.push({name: 'session', value: $("#session").val()});
        params.push({name: 'id', value: key});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
                if(typeof response.data.oldval !== "undefined" ) {
                    $("input.iprestrict[data-key='"+key+"']").val(response.data.oldval);
                }

            }
        },'json');
    });
    $("div.removerestrict").unbind('click').click(function(){
        if (confirm('Delete IP restriction?')==true) {
            var key = $(this).data('key');
            var url = '/admin/userip_restrict_delete';
            var params = new Array();
            params.push({name: 'session', value: $("#session").val()});
            params.push({name: 'id', value: key});
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    $("#iprestrictarea").empty().html(response.data.content);
                    user_edit_manage();
                } else {
                    show_error(response);
                }
            },'json');
        }
    });
    $("input.userpersdata").unbind('change').change(function () {
        var url = '/admin/userdata_change';
        var params = new Array();
        var itemname = $(this).data('name');
        params.push({name: 'session', value: $("#session").val()});
        params.push({name: 'item', value: itemname});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
                if (itemname=='user_passwd_txt1') {
                    if ($("#user_passwd_txt1").val()=='') {
                        $("div.retypepasswd").css('display','hide');
                        $("#user_passwd_txt2").val('');
                    } else {
                        $("div.retypepasswd").css('display','block');
                        $("input#user_passwd_txt2").focus();
                    }
                }
            } else {
                show_error(response);
            }
        },'json');
    });
    $("select.userpersdata").unbind('change').change(function () {
        var url = '/admin/userdata_change';
        var params = new Array();
        params.push({name: 'session', value: $("#session").val()});
        params.push({name: 'item', value: $(this).data('name')});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("input.userpersdatachk").unbind('change').change(function () {
        var newval = 0;
        if ($(this).prop('checked')==true) {
            newval = 1;
        }
        var url = '/admin/userdata_change';
        var params = new Array();
        params.push({name: 'session', value: $("#session").val()});
        params.push({name: 'item', value: $(this).data('name')});
        params.push({name: 'newval', value: newval});
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("textarea.userpersdata").unbind('change').change(function () {
        var url = '/admin/userdata_change';
        var params = new Array();
        var itemname = $(this).data('name');
        params.push({name: 'session', value: $("#session").val()});
        params.push({name: 'item', value: itemname});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $("#userstartpageselect").unbind('change').change(function () {
        var url = '/admin/userdata_change';
        var params = new Array();
        params.push({name: 'session', value: $("#session").val()});
        params.push({name: 'item', value: 'user_page'});
        params.push({name: 'newval', value: $(this).val()});
        $.post(url, params, function (response) {
            if (response.errors=='') {
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".webbrandtitle").unbind('click').click(function () {
        var brand = $(this).data('brand');
        $(".webbrandtitle").removeClass('active');
        $(".menuitemsview").removeClass('active');
        $(this).addClass('active');
        $(".menuitemsview[data-brand='"+brand+"']").addClass('active');
    })
}

function add_user() {
    var user_id=0;
    var url="/admin/user_editdata";
    $.post(url,{'user_id':user_id},function(response){
        if (response.errors=='') {
            $("#pageModalLabel").empty().html(response.data.title);
            $("#pageModal").find('div.modal-body').empty().html(response.data.content);
            $("#pageModal").find('div.modal-footer').empty().html(response.data.footer);
            $("#pageModal").find('div.modal-dialog').css('width','948px');
            $("#pageModal").modal({backdrop: 'static', keyboard: false, show: true});
            /* Init save button */
            user_edit_init();
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}

function del_user(user_id) {
    if (confirm('You realy want to delete user ?')) {
        var url='/admin/user_delete';
        $.post(url, {'user_id':user_id}, function(response){
            if (response.errors=='') {
                $('#totalusers').val(response.data.total);
                initUserPagination();
            } else {
                show_error(response);
            }
        }, 'json');
    }
}

function edit_restrict() {
    var params=new Array();
    for (var i=1; i<=10; i++) {
        var nameitem='userip_'+i;
        params.push({name: nameitem , value : $("input#userip_"+i).val()});
    }
    var url="/admin/user_resrictedit";
    $.post(url, params, function(response){
        if (response.errors=='') {
            show_popup1('usrrestrview');
            $("div#popupwin").empty().html(response.data.content);
            $(".ipaddressinpt").ipAddress({v:4});
            $("div.ipresricts_save").click(function(){
                save_restrict();
            })
        } else {
            show_error(response);
        }
    }, 'json');
}
/* Save User IP Restrictions */
function save_restrict() {
    var newiprest='';
    var data;
    var elemid;
    var datelem;
    $("div.ipresricts_data input").each(function(){
        elemid=this.id;
        datelem=elemid.substr(1);
        data=$("#"+elemid).val();
        data=data.replace('___.___.___.___', '');
        if (data!='') {
            newiprest=newiprest+data+',';
        }
        $("#"+datelem).val(data);
    })

    if (newiprest.length>0) {
        var len=newiprest.length;
        len=len-1;
        newiprest=newiprest.substr(0,len);
    }
    $("input#iprestrict").val(newiprest);
    disable_popup1();
}


/* Functions - ADD NEW POPUP */
function show_popup1(id) {
    var pwidth=parseInt($("#"+id).css('width'));
    var pheight=parseInt($("#"+id).css('height'));

    $("#popupwin").css('height',pheight);
    $("#popupwin").css('width',pwidth);
    $("#popupwin").empty().html($("#"+id).html());


    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;

    var wintop=parseInt(windowHeight)/2-parseInt(pheight)/2;
    var winleft=parseInt(windowWidth)/2-parseInt(pwidth)/2;


    //centering
    $("#popupwin").css({
        "position": "absolute",
        "top": wintop,
        "left" : winleft
    });

    wintop=wintop-11;
    winleft = winleft+parseInt(pwidth)-26;

    $("#popupClose").css({
        "position" : "absolute",
        "z-index" : "20",
        "top" : wintop,
        "left" :winleft
    })

    $("#usrloader").fadeIn("slow");
    $("#popupClose").live('click',function(){
        disable_popup1();
    })
}

function disable_popup1() {
    $("div#popupwin").empty();
    $("#usrloader").fadeOut("slow");
}

function get_permission() {
    var user_id=$("input#user_id").val();
    var websystem_id=$("form#userdat select#websysselect").val();
    var url="/admin/permissionsdat";
    $.post(url, {'user_id':user_id,'websystem_id':websystem_id}, function(response){
        if (response.errors=='') {
            $("div.permissioninfo").empty().html(response.data.content);
            $("div.savepermissions").empty().html(response.data.savebtn);
            $("ul#tree").Tree();
            /* Save button */
            $("a.savepermis").click(function(){
                save_usrpermiss(user_id, websystem_id);
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }
        }
    },'json');
}

function save_usrpermiss(user_id, websystem_id) {
    var dat=$("div#pop_content form#permisseditform").serializeArray();
    dat.push({name: "user_id", value: user_id});
    dat.push({name: "websystem_id", value: websystem_id});
    var url="/admin/save_permissions";
    $.post(url, dat, function(response){
        if (response.errors=='') {
            $("div.permissioninfo").empty();
            $("div.savepermissions").empty();
            $("select#websysselect").val('');
            $("select#roleselect").val('');
        } else {
            show_error(response);
        }
    },'json');

}