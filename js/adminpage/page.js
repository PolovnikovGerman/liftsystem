$(document).ready(function () {
    // autocollapse(0); // when document first loads
    // $(window).on('resize', autocollapse); // when window is resized
    $(window).resize(function() {
        // autocollapse(1);
    });
    $(".menubutton").unbind('click').click(function () {
        var url=$(this).data('menulink');
        window.location.href=url;
    });
    $("#signout").unbind('click').click(function () {
        if (confirm('You want to sign out?')==true) {
            window.location.href='/login/logout';
        }
    });
    $("#admin").unbind('click').click(function(){
        window.location.href='/admin';
    });
    $("#publicsearch_template").keypress(function (event) {
        if (event.which == 13) {
            liftsite_search();
        }
    });
    $(".publicsearch_btn").unbind('click').click(function () {
        liftsite_search();
    });
    $("select.publicsearch_type").unbind('change').change(function(){
        var newval = $(this).val();
        if (newval=='Orders') {
            $("#publicsearch_template").attr('placeholder','Find Orders');
        } else if (newval=='Items') {
            $("#publicsearch_template").attr('placeholder','Find Items');
        }
    })
});

function autocollapse(resize) {
    var tabs = $("#mainmenutabs");
    var tabsHeight = parseInt(tabs.innerHeight());
    if (tabsHeight > 34) {
        $("#lastTab").show();
        var i=0;
        while (tabsHeight > 34) {
            var children = tabs.children('li.menubutton:not(:last-child)');
            var count = children.size();
            $(children[count - 1]).prependTo('#collapsed');
            tabsHeight = tabs.innerHeight();
            i++;
            if (i > 9) {
                break;
            }
        }
    } else {
        if (resize==1) {
            // var params=new Array();
            // params.push({name: 'activelnk', value: $("#mainmenuactivelnk").val()});
            // var url = '/welcome/restore_main_menu'
            // $.post(url, params, function(response){
            //     if (response.errors=='') {
            //         $(".menurow").empty().html(response.data.content);
            //         $(".menubutton").unbind('click').click(function () {
            //             var url=$(this).data('menulink');
            //             window.location.href=url;
            //         });
            //         autocollapse(0);
            //     }
            // },'json');
        }
    }

}

function show_error(response) {
    alert(response.errors);
    if(typeof response.data.url !== "undefined" ) {
        window.location.href=response.data.url;
    }
}

/* Open Template AI file */
function openai(imgurl, imgname) {
    if (navigator.appVersion.indexOf("Mac")!=-1) {
        /* Mac OS*/
        $.fileDownload('/welcome/art_openimg', {httpMethod : "POST", data: {url : imgurl, file: imgname}});
        return false; //this is critical to stop the click event which will trigger a normal file download!            return false; //this is critical to stop the click event which will trigger a normal file download!
        window.open(imgurl, 'showfile');
    } else {
        var open = window.open(imgurl,imgname,'left=120,top=120,width=500,height=400');
        if (open == null || typeof(open)=='undefined')
            alert("Turn off your pop-up blocker!\n\nWe try to open the following url:\n"+url);

    }
}

function matchStart(params, data) {
// If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
        return data;
    }
    if (typeof data.text === 'undefined') {
        console.log('no text')
        return null;
    }

    // `params.term` should be the term that is used for searching
    // `data.text` is the text that is displayed for the data object
    if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
        var modifiedData = $.extend({}, data, true);
        modifiedData.text += ' (matched)';

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}

function liftsite_search() {
    var params = new Array();
    params.push({name: 'search_type', value: $("select.publicsearch_type").val()});
    params.push({name: 'search_template', value: $("#publicsearch_template").val()});
    var url="/welcome/liftsite_search";
    $.post(url, params, function(response) {
        if (response.errors=='') {
            if (response.data.url!=='') {
                window.location.href=response.data.url;
            }
        } else {
            show_error(response);
        }
    },'json');
}