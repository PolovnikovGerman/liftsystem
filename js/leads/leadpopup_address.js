let addressAutocomplete;
let customerAutocomplete;
let address1Field;
let address2Field;

function initCustomerAddressAutocomplete() {
    address1Field = document.getElementById("customeraddress_line1");
    // Shipping Address
    var shipcnt = $("#customercountrycode").val();
    if (shipcnt == '') {
        shipcnt = ["us", "ca"];
    }
    customerAutocomplete = new google.maps.places.Autocomplete(address1Field, {
        componentRestrictions: {country: shipcnt},
        fields: ["address_components", "geometry"],
        types: ["address"],
    })
    customerAutocomplete.addListener('place_changed', fillInCustomerAddress);
}

function fillInCustomerAddress() {
    const place = customerAutocomplete.getPlace();
    placeCustomerAddressParse(place);
}

function placeCustomerAddressParse(place) {
    let address1 = "";
    let postcode = "";
    let city = "";
    let state = "";
    let country = "";
    for (const component of place.address_components) {
        var componentType = component.types[0];
        if (component.types.length > 1 && componentType=='political') {
            componentType = component.types[1];
        }
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
    updateCustomerAddress(address1, city, state, postcode, country);
}

function updateCustomerAddress(address1, city, state, postcode, country) {
    
}

