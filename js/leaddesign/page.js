$(document).ready(function (){
    init_design_page();
})

function init_design_page() {
    $(".designback").unbind('click').click(function (){
        var url = '/projects';
        window.location.replace(url);
    });
    $(".designshowmenu").unbind('click').click(function (){
        $(".designshowmenu").hide();
        $(".designback").addClass('showmenu');
        $(".maincontent_view").addClass('showmenu');
        $(".contentdata_view").addClass('showmenu');
        $(".customerdataarea").addClass('showmenu');
        $(".emaildataarea").addClass('showmenu');
        $(".leaddataarea").addClass('showmenu');
        $(".orderdataarea").addClass('showmenu');
        $(".maincontentmenu").show();
    });
    $(".specialhidemainmenu").unbind('click').click(function (){
        $(".designback").removeClass('showmenu');
        $(".maincontent_view").removeClass('showmenu');
        $(".contentdata_view").removeClass('showmenu');
        $(".customerdataarea").removeClass('showmenu');
        $(".emaildataarea").removeClass('showmenu');
        $(".leaddataarea").removeClass('showmenu');
        $(".orderdataarea").removeClass('showmenu');
        $(".designshowmenu").show();
        $(".maincontentmenu").hide();
    })
    $("img.emailpic").unbind('click').click(function (){
        var mode = $("#emailview").val();
        if (mode=='inbox') {
            $(".emaildataarea").find('img.emailpic').attr('src','/img/leaddesign/block-email-03-1.png');
            $(".emaildataarea").find('button.close').show();
            $("#emailview").val('message');
            $(".emaildataarea").unbind('click');
        }
    })
    $(".emaildataarea").find('button.close').unbind('click').click(function(){
        $(".emaildataarea").find('img.emailpic').attr('src','/img/leaddesign/block-email-03-2.png');
        $(".emaildataarea").find('button.close').hide();
        $("#emailview").val('inbox');
        init_design_page();
    })
}