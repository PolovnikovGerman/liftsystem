$(document).ready(function (){
    init_design_page();
})

function init_design_page() {
    $(".designback").unbind('click').click(function (){
        var url = '/projects';
        window.location.replace(url);
    });
    $(".designshowmenu").unbind('click').click(function (){
        $(".maincontentmenu").show();
        $(".maincontent_view").css('padding-top','7px');
        $(".designshowmenu").hide();
    });
    $(".specialhidemainmenu").unbind('click').click(function (){
        $(".maincontentmenu").hide();
        $(".maincontent_view").css('padding-top','0');
        $(".designshowmenu").show();
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