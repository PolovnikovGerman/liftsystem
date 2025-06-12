$(document).ready(function (){
    $(".gh-btn-shopcategories").unbind('click').click(function (){
        $(".categories-dropdown").show();
        init_categories_content();
    });
})

function init_categories_content() {
    $(".btnclose").find('button.close').unbind('click').click(function (){
        $(".categories-dropdown").hide();
    })
}