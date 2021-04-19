function init_itemlist_details_view() {
    $("#sliderlist").easySlider({
        nextText : '',
        prevText : '',
        vertical : false
    });
    $(".implintdatavalue.vectorfile").unbind('click').click(function () {
        var imgurl = $(this).data('link');
        openai(imgurl, 'Vector Image');
    });
    
}