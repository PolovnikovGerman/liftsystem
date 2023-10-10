let addressAutocomplete;
let shipAutocomplete;
let address1Field;
let address2Field;

function initShipOrderAutocomplete() {
    address1Field = document.getElementById("quoteshipaddress_line1");
    // Shipping Address
    var shipcnt = $("#shipquotecntcode").val();
    if (shipcnt == '') {
        shipcnt = ["us", "ca"];
    }
    shipAutocomplete = new google.maps.places.Autocomplete(address1Field, {
        componentRestrictions: {country: shipcnt},
        fields: ["address_components", "geometry"],
        types: ["address"],
    })
    shipAutocomplete.addListener('place_changed', fillOrderShipping);
}
function initBillOrderAutocomplete() {
    address2Field = document.getElementById('billorder_line1');
    // Billing Address
    var billcnt = $("#billordercntcode").val();
    if (billcnt=='') {
        billcnt = ["us", "ca"];
    }
    addressAutocomplete = new google.maps.places.Autocomplete(address2Field, {
        componentRestrictions: { country: billcnt },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    addressAutocomplete.addListener("place_changed", fillOrderAddress);
}

function fillOrderAddress() {
    // Get the place details from the autocomplete object.
    const place = addressAutocomplete.getPlace();
    placeOrderParse(place, 'billing');
}

function fillOrderShipping() {
    const place = shipAutocomplete.getPlace();
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
                $("input[data-field='city']").val(response.data.city);
                $("input[data-field='zip']").val(response.data.zip);
                if (parseInt(response.data.bilstate)==1) {
                    $("#billingstateselectarea").empty().html(response.data.stateview);
                } else {
                    $("#billingstateselectarea").empty();
                }
            } else {
                $("input[data-item='shipping_address1']").val(response.data.address_1);
                $("select[data-item='shipping_country']").val(response.data.country);
                $("input[data-item='shipping_city']").val(response.data.city);
                $("input[data-item='shipping_zip']").val(response.data.zip);
                if (parseInt(response.data.shipstate)==1) {
                    $(".quoteshipaddresdistrict").empty().html(response.data.stateview);
                } else {
                    $(".quoteshipaddresdistrict").empty(); // .val(response.data.state);
                }
            }
            if (parseInt(response.data.ship)==1) {
                // Update shipping cost
                $(".quoteleadshipcostinpt[data-item='shipping_cost']").val(response.data.shipping_cost);
                $(".quoteshippingcostarea").empty().html(response.data.shippingview);
                // Tax view
                $(".quotetaxarea").empty().html(response.data.taxview);
                // Totals
                $(".quotetotalvalue").empty().html(response.data.total);
                $(".quotecommondatainpt[data-item='sales_tax']").val(response.data.tax);
                $("input[data-item='shipping_cost']").val(response.data.shipping_cost);
            }
            $("#loader").hide();
            init_onlineleadorder_edit();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');

}