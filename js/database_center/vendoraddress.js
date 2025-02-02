let addressAutocomplete;
let shipAutocomplete;
let address1Field;
let address2Field;

function initAddressAutocomplete() {
  address1Field = document.getElementById("address_line1");
  address2Field = document.getElementById('shipaddr_line1');
  addressAutocomplete = new google.maps.places.Autocomplete(address1Field, {
    componentRestrictions: { country: ["us", "ca"] },
    fields: ["address_components", "geometry"],
    types: ["address"],
  });
  addressAutocomplete.addListener("place_changed", fillInAddress);
  shipAutocomplete = new google.maps.places.Autocomplete(address2Field, {
    componentRestrictions: { country: ["us", "ca"] },
    fields: ["address_components", "geometry"],
    types: ["address"],
  })
  shipAutocomplete.addListener('place_changed', fillInShipping);
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  const place = addressAutocomplete.getPlace();
  placeParse(place, 'address');
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
      //   postcode = `${postcode}-${component.long_name}`;
      //   break;
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
  if (address_type=='address') {
    $("#address_line1").val(address1);
    $("#address_city").val(city);
    $("#address_state").val(state);
    $("#address_zip").val(postcode);
    $("#address_country").val(country);
  } else {
    $("#shipaddr_line1").val(address1);
    $("#shipaddr_city").val(city);
    $("#shipaddr_state").val(state);
    $("#vendor_zipcode").val(postcode);
    $("#shipaddr_country").val(country);
  }
  var url='/vendors/update_vendor_address';
  var params=prepare_vendor_edit();
  params.push({name: 'address_type', value: address_type});
  params.push({name: 'line_1', value: address1});
  params.push({name: 'city', value: city});
  params.push({name: 'state', value: state});
  params.push({name: 'zip', value: postcode});
  params.push({name: 'country', value: country});
  $.post(url, params, function (response) {
    if (response.errors=='') {
    } else {
      show_error(response);
    }
  },'json');
}