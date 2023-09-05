var timerId;
var timeLapse = 600000;
$(document).ready(function () {
    clearTimeout(timerId);
    // Calc
    rebuild_market_offset();
    leftmenu_alignment();
    $(".brandmenuitem.relievers").hover(
        function () {
            var iconsrc = $(this).find('div.brandmenuicon').children('img').prop('src').replace('-black.','-white.');
            $(this).find('div.brandmenuicon').children('img').prop('src', iconsrc);
        },
        function () {
            var iconsrc = $(this).find('div.brandmenuicon').children('img').prop('src').replace('-white.', '-black.');
            $(this).find('div.brandmenuicon').children('img').prop('src', iconsrc);
        }
    );
    $(".brandmenuitem").find('div.brandmenuicon').hover(
        function () {
            $(".brandsubmenu[data-brand='SB']").hide();
            var dataid = $(this).data('item');
            var position = $(this).position();
            var brand = $(this).data('brand');
            var loctop = parseInt(position.top)+25;
            var params = new Array();
            params.push({name: 'menu', value: dataid});
            params.push({name: 'brand', value: brand});
            $.post('/welcome/submenus', params, function (response){
                if (response.errors=='') {
                    $(".brandsubmenu[data-brand='"+brand+"']").empty().html(response.data.content).show().css('top',loctop);
                    init_submenu(brand);
                } else {
                    show_error(response);
                }
            },'json');

        },
        function () {
            // $(".brandsubmenu[data-brand='SB']").hide();
        }
    );
    $("div.rowdata").hover(
        function(){
            rowid=$(this).data('orderid');
            $("div.rowdata[data-orderid="+rowid+"]").addClass("current_row");
        },
        function(){
            rowid=$(this).data('orderid');
            $("div.rowdata[data-orderid="+rowid+"]").removeClass("current_row");
        }
    );

    // autocollapse(0); // when document first loads
    // $(window).on('resize', autocollapse); // when window is resized
    // $(window).resize(function() {
    //     autocollapse(1);
    // });
    // $(".menubutton").unbind('click').click(function () {
    //     var url=$(this).data('menulink');
    //     window.location.href=url;
    // });
    $(".brandmenuitem").unbind('click').click(function () {
        var params = new Array();
        params.push({name: 'url', value: $(this).data('url')});
        params.push({name: 'brand', value: $(this).data('brand')});
        var url='/welcome/brandnavigate';
        $.post(url, params, function (response) {
            if (response.errors=='') {
                window.location.href=response.data.url;
            } else {
                show_error(response);
            }
        },'json');
    });
    $(".content_tab_header").unbind('click').click(function () {
        if ($(this).hasClass('active')) {
        } else {
            var params = new Array();
            params.push({name: 'brand', value: $(this).data('brand')});
            var url='/welcome/brandshow';
            $.post(url, params, function (response) {
                if (response.errors=='') {
                    window.location.href='/';
                } else {
                    show_error(response);
                }
            },'json');
        }
    })
    $("#signout").unbind('click').click(function () {
        if (confirm('You want to sign out?')==true) {
            window.location.href='/login/logout';
        }
    });
    $("#admin").unbind('click').click(function(){
        window.location.href='/admin';
    });
    $("#reports").unbind('click').click(function () {
        window.location.href='/analytics';
    })
    $("#resources").unbind('click').click(function () {
        window.location.href='/resources';
    })
    $("#publicsearch_template").keypress(function (event) {
        if (event.which == 13) {
            liftsite_search('mobile');
        }
    });
    $("#publicsearch_btn").unbind('click').click(function () {
        liftsite_search('mobile');
    });
    $("#publicdescsearch_template").keypress(function (event) {
        if (event.which == 13) {
            liftsite_search('desktop');
        }
    })
    $("#publicdescsearch_btn").unbind('click').click(function () {
        liftsite_search('desktop');
    })
    $("#inventory").unbind('click').click(function () {
        window.location.href='/fulfillment?start=printshopinventview';
    })
    // $("select.publicsearch_type").unbind('change').change(function(){
    //     var newval = $(this).val();
    //     if (newval=='Orders') {
    //         $("#publicsearch_template").attr('placeholder','Find Orders');
    //     } else if (newval=='Items') {
    //         $("#publicsearch_template").attr('placeholder','Find Items');
    //     }
    // });
    $('a.test').on("click", function(e){
        $("#collapsed").toggle();
        e.stopPropagation();
        e.preventDefault();
    });
    // $("li.nav-item.dropdown").hover(
    //     function() {
    //         $( this ).children('a').next('div.dropdown-menu').toggle();
    //     }, function() {
    //         $( this ).children('a').next('div.dropdown-menu').toggle();
    //     }
    // )
    $("div.menuitem").unbind('click').click(function () {
        var url = $(this).data('menulink');
        window.location.href=url;
    })
    $("#showtotalthisweek").unbind('click').click(function () {
        window.location.href='/orders';
    })
    $("button.dropdown-item").unbind('click').click(function () {
        var url = $(this).data('menulink');
        window.location.href=url;
    })
    // Create timer
    timerId = setTimeout('ordertotalsparse()', timeLapse);
    jQuery.balloon.init();
});

function init_submenu(brand) {
    $(".brandsubmenu[data-brand='"+brand+"']").hover(
        function() {
        },
        function () {
            $(".brandsubmenu[data-brand='"+brand+"']").hide();
        }
    )
    $(".submenuitem").unbind('click').click(function (){
        var url = $(this).data('url');
        window.location.href=url;
    })
}
$(window).resize(function() {
    rebuild_market_offset();
});

function rebuild_market_offset() {
    var allwidth = parseInt(window.innerWidth);
    var freespace = (allwidth - (parseInt($(".finmenusection").css('width')) + parseInt($(".marketmenusection").css('width')) + parseInt($(".contentmenusection").css('width'))))/2;
    $(".marketmenusection").css('margin-left',freespace+'px');
}

function autocollapse(resize) {
    var tabs = $("#mainmenutabs");
    var tabsHeight = parseInt(tabs.innerHeight());
    if (tabsHeight > 34) {
        $("#lastTab").show();
        $("#lastTab").find('a').show();
        var i=0;
        while (tabsHeight > 34) {
            var children = tabs.children('li.nav-item:not(:last-child)');
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
            var params=new Array();
            params.push({name: 'activelnk', value: $("#mainmenuactivelnk").val()});
            var url = '/welcome/restore_main_menu'
            $.post(url, params, function(response){
                if (response.errors=='') {
                    $(".menurow").empty().html(response.data.content);
                    $(".menubutton").unbind('click').click(function () {
                        var url=$(this).data('menulink');
                        window.location.href=url;
                    });
                    autocollapse(0);
                }
            },'json');
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

function liftsite_search(searchtype) {
    var params = new Array();
    if (searchtype=='mobile') {
        params.push({name: 'search_template', value: $("#publicsearch_template").val()});
    } else {
        params.push({name: 'search_template', value: $("#publicdescsearch_template").val()});
    }
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

function ordertotalsparse() {
    var url="/welcome/ordertotalsparse";
    $.post(url,{}, function(response){
        if (response.errors == '') {
            $("#totalsales").empty().html(response.data.sales);
            $("#totalrevenue").empty().html(response.data.revenue);
            setTimeout('ordertotalsparse()', timeLapse);
        } else {
            show_error(response);
        }
    },'json');
}

function leftmenu_alignment() {
    var mainheight = $("div.maincontentmenuarea").css('height');
    $(".leftmenuarea").find('div.content_tab.active').css('height', mainheight);

}