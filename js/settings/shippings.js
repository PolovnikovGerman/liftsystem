function init_shiipings_page(brand) {
    init_shipping(brand);
    // Change Brand
}

function init_shipping(brand) {
    var params=new Array();
    params.push({name:'month', value:$("select.shiplogmonth[data-brand='"+brand+"']").val()});
    params.push({name:'year', value:$("select.shiplogyear[data-brand='"+brand+"']").val()});
    params.push({name: 'brand', value: brand});
    $.post('/settings/shippingdata', params, function(response){
        if (response.errors=='') {
            // $("div.shipzonesdata[data-brand='"+brand+"']").empty().html(response.data.content);
            var areaid='#shipzonesdata'+brand;
            $(areaid).empty().html(response.data.content);
            $("div.shipcalclogcalend[data-brand='"+brand+"']").empty().html(response.data.report);
            initshipcontent(brand);
        } else {
            show_error(response);
        }
    }, 'json');
}
function initshipcontent(brand) {
    $("div.shipkoefmanage[data-brand='"+brand+"']").unbind('click').click(function(){
        $(this).removeClass('activate_btn').addClass('saveshipping').empty().html('<div class="activate-text">Save Editing</div>');
        openeditship(brand);
    });
    $("select.shiplogyear[data-brand='"+brand+"']").unbind('change').change(function(){
        show_shipcalclog(brand);
    })
    $("select.shiplogmonth[data-brand='"+brand+"']").unbind('change').change(function(){
        show_shipcalclog(brand);
    })
    init_shipcalccalend(brand);
}

// javascript:openeditship();
function openeditship(brand) {
    $("form.shipzones[data-brand='"+brand+"'] input:text").attr('readonly',false);
    $("form.shipzones[data-brand='"+brand+"'] input:checkbox").attr('disabled',false);
    $("div.saveshipping[data-brand='"+brand+"']").unbind('click').click(function(){
        saveshipping(brand);
    });
}

function saveshipping(brand) {
    var url='/settings/savezones';
    var data=$(".shipzones[data-brand='"+brand+"']").serializeArray();
    data.push({name: 'brand', value: brand});
    $.post(url, data, function(response){
        if (response.errors=='') {
            $("form.shipzones[data-brand='"+brand+"'] input:text").attr('readonly',true);
            $("form.shipzones[data-brand='"+brand+"'] input:checkbox").attr('disabled',true);
            $("div.shipkoefmanage[data-brand='"+brand+"']").removeClass('saveshipping').addClass('activate_btn').empty().html('<div class="activate-text">Activate Editing</div>');
            initshipcontent(brand);
        } else {
            show_error(response);
        }
    }, 'json');
}

function show_shipcalclog(brand) {
    var params=new Array();
    params.push({name:'month', value:$("select.shiplogmonth[data-brand='"+brand+"']").val()});
    params.push({name:'year', value:$("select.shiplogyear[data-brand='"+brand+"']").val()});
    params.push({name: 'brand', value: brand});
    var url="/settings/shipcalclog";
    $.post(url, params, function(response){
        if (response.errors=='') {
            $("div.shipcalclogcalend[data-brand='"+brand+"']").empty().html(response.data.content);
            init_shipcalccalend(brand);
        } else {
            show_error(response);
        }
    },'json');
}

function init_shipcalccalend(brand) {
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
        // hide: {
        //     event: 'click',
        // },
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