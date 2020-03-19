function init_shiipings_page() {
    init_shipping();
    // Change Brand
    $("#shippingsviewbrandmenu").find("div.left_tab").unbind('click').click(function(){
        var brand = $(this).data('brand');
        $("#shippingsviewbrand").val(brand);
        $("#shippingsviewbrandmenu").find("div.left_tab").removeClass('active');
        $("#shippingsviewbrandmenu").find("div.left_tab[data-brand='"+brand+"']").addClass('active');
        init_shipping();
    });
}

function init_shipping() {
    var params=new Array();
    params.push({name:'month', value:$("select#shiplogmonth").val()});
    params.push({name:'year', value:$("select#shiplogyear").val()});
    params.push({name: 'brand', value: $("#shippingsviewbrand").val()});
    $.post('/settings/shippingdata', params, function(response){
        if (response.errors=='') {
            $("div#shipzonesdata").empty().html(response.data.content);
            $("div.shipcalclogcalend").empty().html(response.data.report);
            initshipcontent();
        } else {
            show_error(response);
        }
    }, 'json');
}
function initshipcontent() {
    $("div#activate").unbind('click').click(function(){
        $(this).removeClass('activate_btn').addClass('saveshipping').empty().html('<div class="activate-text">Save Editing</div>');
        openeditship();
    });
    $("select#shiplogyear").unbind('change').change(function(){
        show_shipcalclog();
    })
    $("select#shiplogmonth").unbind('change').change(function(){
        show_shipcalclog();
    })
    init_shipcalccalend();
}

// javascript:openeditship();
function openeditship() {
    $("form#shipzones input:text").attr('readonly',false);
    $("form#shipzones input:checkbox").attr('disabled',false);
    $("div#activate").unbind('click').click(function(){
        saveshipping();
    });
}

function saveshipping() {
    var url='/settings/savezones';
    var data=$("#shipzones").serializeArray();
    data.push({name: 'brand', value: $("#shippingsviewbrand").val()});
    $.post(url, data, function(response){
        if (response.errors=='') {
            $("form#shipzones  input:text").attr('readonly',true);
            $("form#shipzones input:checkbox").attr('disabled',true);
            $("div#activate").removeClass('saveshipping').addClass('activate_btn').empty().html('<div class="activate-text">Activate Editing</div>');
            $("div#activate").unbind('click').click(function(){
                openeditship();
            })
        } else {
            alert(response.errors);
            if(response.data.url !== undefined) {
                window.location.href=response.data.url;
            }

        }
    }, 'json');
}

function show_shipcalclog() {
    var params=new Array();
    params.push({name:'month', value:$("select#shiplogmonth").val()});
    params.push({name:'year', value:$("select#shiplogyear").val()});
    params.push({name: 'brand', value: $("#shippingsviewbrand").val()});
    var url="/settings/shipcalclog";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.shipcalclogcalend").empty().html(response.data.content);
            init_shipcalccalend();
        } else {
            show_error(response);
        }
    },'json');
}

function init_shipcalccalend() {
    $("div.shiprepdaycell.active").qtip({
        content: {
            text: function(event, api) {
                $.ajax({
                    url: api.elements.target.data('content') // Use href attribute as URL
                }).then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to error
                    api.set('content.text', status + ': ' + error);
                });
                return 'Loading...'; // Set some initial text
            }
        },
        show: {
            event: 'click'
        },
        hide: {
            event: 'click',
        },
        position: {
            my: 'bottom left',
            at: 'top right',
        },
        style: 'shiprepdaycell_tooltip'
    });

    /* $("div.shiprepdaycell").each(function(){
        $("div#"+$(this).prop('id')).bt({
            trigger: 'click',
            ajaxCache: false,
            width: '820px',
            ajaxPath: ["$(this).attr('href')"]
        });
    });
    */
}