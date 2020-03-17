$(document).ready(function () {
    $(".menubutton").unbind('click').click(function () {
        var url=$(this).data('menulink');
        window.location.href=url;
    });
    $("#signout").unbind('click').click(function () {
        if (confirm('You want to sign out?')==true) {
            window.location.href='/login/logout';
        }
    })
})

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
