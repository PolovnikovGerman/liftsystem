let addressAutocomplete;
let shipAutocomplete;
let address1Field;
let address2Field;

function initShipQuoteAutocomplete() {
    address1Field = document.getElementById("shiplead_line1");
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
    shipAutocomplete.addListener('place_changed', fillInShipping);
}
function initBillQuoteAutocomplete() {
    address2Field = document.getElementById('bill_line1');
    // Billing Address
    var billcnt = $("#billcountrycode").val();
    if (billcnt=='') {
        billcnt = ["us", "ca"];
    }
    addressAutocomplete = new google.maps.places.Autocomplete(address2Field, {
        componentRestrictions: { country: billcnt },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    addressAutocomplete.addListener("place_changed", fillInAddress);
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    const place = addressAutocomplete.getPlace();
    placeParse(place, 'billing');
}

function fillInShipping() {
    const place = shipAutocomplete.getPlace();
    placeParse(place, 'shipping');
}

function placeParse(place, address_type) {
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
            // case "postal_code_suffix": {
            //     postcode = `${postcode}-${component.long_name}`;
            //     break;
            // }
            case "locality": {
                if (city=='') {
                    city = component.long_name;
                }
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
                if (city=='') {
                    city = component.long_name;
                }
                break;
            }
            case "sublocality_level_1": {
                if (city=='') {
                    city = component.long_name;
                }
                break;
            }
        }
    }
    updateAddress(address_type, address1, city, state, postcode, country);
}

function updateAddress(address_type, address1, city, state, postcode, country) {
    var url='/leadquote/update_autoaddress';
    var params = new Array();
    params.push({name: 'address_type', value: address_type});
    params.push({name: 'line_1', value: address1});
    params.push({name: 'city', value: city});
    params.push({name: 'state', value: state});
    params.push({name: 'zip', value: postcode});
    params.push({name: 'country', value: country});
    params.push({name: 'session', value: $("#quotesessionid").val()});
    $("#loader").show();
    $.post(url, params, function (response) {
        if (response.errors=='') {
            if (address_type=='billing') {
                $("select[data-item='billing_country']").val(response.data.country);
                $("input[data-item='billing_address1']").val(response.data.address_1);
                $("input[data-item='billing_city']").val(response.data.city);
                $("input[data-item='billing_zip']").val(response.data.zip);
                if (parseInt(response.data.bilstate)==1) {
                    $(".quotebilladdresdistrict").empty().html(response.data.stateview);
                } else {
                    $(".quotebilladdresdistrict").empty();
                }
                $("#billingcompileaddress").val(response.data.billaddress);
                $(".quotebilladdrother[data-item='billing_address2']").focus().addClass('flashed');
                setTimeout(function() {
                    $(".quotebilladdrother[data-item='billing_address2']").removeClass('flashed');
                }, 5000);
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
                $("#shipingcompileaddress").val(response.data.shipaddress);
                $(".quoteshipadrother[data-item='shipping_address2']").focus().addClass('flashed');
                setTimeout(function() {
                    $(".quoteshipadrother[data-item='shipping_address2']").removeClass('flashed');
                }, 5000);
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
            init_leadquotes_content();
        } else {
            $("#loader").hide();
            show_error(response);
        }
    },'json');

}