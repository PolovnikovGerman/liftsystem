<div class="cust-maininfo dumbedpanel">
    <div class="datarow">
        <div class="custom-info-txt">Customer:</div>
    </div>
    <div class="datarow">
        <div class="custom-info-name truncateoverflowtext">ABC Company - Jersey City Branch</div>
    </div>
</div>
<div class="dumbedleft-panel">
    <?php $contact_num = 1; ?>
    <?php foreach ($contacts as $contact) : ?>
        <div class="datarow">
            <div class="dumbedpanel-section">
                <div class="dumbedpanel-title">Contact<?=$contact_num > 1 ? ' '.$contact_num : ''?>:</div>
                <div class="dumbedpanel-body">
                    <div class="dp-formrow">
                        <input class="dp-contactname leadcontactedit" type="text" name="contact-name" placeholder="Name" data-contact="<?=$contact['lead_contact_id']?>"
                               data-fld="contact_name" value="<?=$contact['contact_name']?>"/>
                    </div>
                    <div class="dp-formrow">
                        <input class="dp-contactemail leadcontactedit" type="text" name="contact1-email" data-contact="<?=$contact['lead_contact_id']?>" data-fld="contact_email"
                               placeholder="Email" value="<?=$contact['contact_email']?>"/>
                        <div class="dp-contactemailbtn"><i class="fa fa-clone" aria-hidden="true"></i></div>
                    </div>
                    <div class="dp-formrow">
                        <input class="dp-contactphone leadcontactedit" type="text" name="contact1-phone" data-contact="<?=$contact['lead_contact_id']?>" data-fld="contact_phone"
                               placeholder="Phone" value="<?=$contact['contact_phone']?>"/>
                    </div>
                </div>
                <div class="dumbedpanel-line">&nbsp;</div>
            </div>
        </div>
        <?php $contact_num++; ?>
    <?php endforeach; ?>
    <div class="datarow">
        <div class="dumbedpanel-section">
            <div class="dumbedpanel-title">Address:</div>
            <div class="dumbedpanel-body">
                <div class="dp-formrow">
                    <select class="dp-country leadmainedit" data-fld="country_id">
                        <option value=""></option>
                        <?php foreach ($countries as $country) : ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$address['country_id'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dp-formrow">
                    <input class="dp-addressline leadmainedit" type="text" name="address-1" data-fld="address_line1" placeholder="Address Line 1" value="<?=$address['address_line1']?>"/>
                </div>
                <div class="dp-formrow">
                    <input class="dp-addressline leadmainedit" type="text" name="address-2" data-fld="address_line2" placeholder="Address Line 2" value="<?=$address['address_line2']?>"/>
                </div>
                <div class="dp-formrow">
                    <div id="lead_address_city">
                        <input class="dp-city leadmainedit" type="text" name="city" data-fld='city' placeholder="City" value="<?=$address['city']?>"></div>
                    <div id="lead_address_states"><?=$states?></div>
                    <div id="lead_address_zip">
                        <input class="dp-zipcode leadmainedit" type="text" name="zipcode" data-fld='zip' placeholder="Zip Code" value="<?=$address['zip']?>"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
