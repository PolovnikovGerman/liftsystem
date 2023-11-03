let adrOrderAutocomplete;
let shipOrdAutocomplete;
let adrOrder1Field;
let adrOrder2Field;

function initShipOrderAutocomplete() {
    adrOrder1Field = document.getElementById("shiporder_line1");
    // Shipping Address
    var shipcnt = $("#shipordercntcode").val();
    if (shipcnt == '') {
        shipcnt = ["us", "ca"];
    }
    shipOrdAutocomplete = new google.maps.places.Autocomplete(adrOrder1Field, {
        componentRestrictions: {country: shipcnt},
        fields: ["address_components", "geometry"],
        types: ["address"],
    })
    shipOrdAutocomplete.addListener('place_changed', fillOrderShipping);
}
function initBillOrderAutocomplete() {
    adrOrder2Field = document.getElementById('billorder_line1');
    // Billing Address
    var billcnt = $("#billordercntcode").val();
    if (billcnt=='') {
        billcnt = ["us", "ca"];
    }
    adrOrderAutocomplete = new google.maps.places.Autocomplete(adrOrder2Field, {
        componentRestrictions: { country: billcnt },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    adrOrderAutocomplete.addListener("place_changed", fillOrderAddress);
}

function fillOrderAddress() {
    // Get the place details from the autocomplete object.
    const place = adrOrderAutocomplete.getPlace();
    placeOrderParse(place, 'billing');
}

function fillOrderShipping() {
    const place = shipOrdAutocomplete.getPlace();
    placeOrderParse(place, 'shipping');
}

function placeOrderParse(place, address_type) {
    let address1 = "";
    let postcode = "";
    let city = "";
    let state = "";
    let country = "";
    for (const component of place.address_components) {
        const componentType = component.types[0];
        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }
            case "route": {
                address1 += component.short_name;
                break;
            }
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                break;
            }
            case "locality": {
                city = component.long_name;
                break;
            }
            case "administrative_area_level_1": {
                state = component.short_name;
                break;
            }
            case "country": {
                country = component.long_name;
                break;
            }
            case "postal_town": {
                city = component.long_name;
            }
        }
    }
    updateOrderAddress(address_type, address1, city, state, postcode, country);
}

function updateOrderAddress(address_type, address1, city, state, postcode, country) {
    var url='/leadorder/update_autoaddress';
    var params = new Array();
    params.push({name: 'address_type', value: address_type});
    if (address_type=='shipping') {
        var shipadr = $("#shiporder_line1").data('shipadr');
        params.push({name: 'shipadr', value: shipadr});
    }
    params.push({name: 'line_1', value: address1});
    params.push({name: 'city', value: city});
    params.push({name: 'state', value: state});
    params.push({name: 'zip', value: postcode});
    params.push({name: 'country', value: country});
    params.push({name: 'ordersession', value: $("input#ordersession").val()});
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            if (address_type=='billing') {
                $("select[data-field='billing_country']").val(response.data.country);
                $("input[data-field='address_1']").val(response.data.address_1);
                $("input.billinginput[data-field='city']").val(response.data.city);
                $("input.billinginput[data-field='zip']").val(response.data.zip);
                if (parseInt(response.data.bilstate)==1) {
                    $("#billingstateselectarea").empty().html(response.data.stateview);
                } else {
                    $("#billingstateselectarea").empty().html('&nbsp;');
                }
                $("#billingcompileaddress").val(response.data.addresscopy);
            } else {
                $("input[data-shipadr='"+shipadr+"'][data-fldname='ship_address1']").val(response.data.address_1);
                $("select.shipcountryselect[data-shipadr='"+shipadr+"']").val(response.data.country);
                $("input.ship_tax_input1[data-shipadr='"+shipadr+"']").val(response.data.city);
                $("input.ship_tax_input2[data-shipadr='"+shipadr+"']").val(response.data.zip);
                $("div[data-content='shipstateshow'][data-shipadr='"+shipadr+"']").empty();
                if (parseInt(response.data.shipstate)==1) {
                    $("div[data-content='shipstateshow'][data-shipadr='"+shipadr+"']").empty().html(response.data.stateview);
                } else {
                    $("div[data-content='shipstateshow'][data-shipadr='"+shipadr+"']").empty().html('&nbsp;');
                }
                $("#shipingcompileaddress").val(response.data.addresscopy);
            }
            if (parseInt(response.data.shipcount)==1) {
                // Update shipping cost
                $("div.ship_tax_container2[data-shipadr='"+shipadr+"']").empty().html(response.data.shipcost);
                $("input.shippingcost").val(response.data.shipping);
                $("input.salestaxcost").val(response.data.tax);
                // Tax view
                if (response.data.taxview.length>0) {
                    $(".ship_tax_cont_bl3").empty().html(response.data.taxview);
                }
                $("div#leadorderprofitarea").empty().html(response.data.profit_content);
                // Totals
                $("div.bl_items_sub-total2").empty().html(response.data.item_subtotal);
                $(".totalduedataviewarea").empty().html(response.data.total_due);
                $("#ordertotaloutput").empty().html(response.data.order_revenue);
                // Shipping Dates
                $("div.shippingdatesarea").empty().html(response.data.shipdates_content);
                init_rushpast();
            }
            $("#loader").hide();
            $("input#loctimeout").val(response.data.loctime);
            init_onlineleadorder_edit();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');

}