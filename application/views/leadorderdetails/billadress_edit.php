<div class="billing_content_linetop">
    <div class="billing_samedata_area">
        <input type="checkbox" data-field="showbilladdress" title="Billing address as Shipping Address" data-entity="order" class="input_checkbox chkboxleadorddata"/>
        <div class="label">same as main shipping address</div>
    </div>    
</div>

<div class="billing_content1">
    <div class="billing_content1_line">
        <div class="billing_customerpotitle">PO#</div>
        <input type="text" class="billing_input3 input_border_gray billinginput leftalign" data-field="customer_ponum" placeholder="CuPo" value="<?= $billing['customer_ponum'] ?>">
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input1 input_border_gray billinginput leftalign" data-field="customer_name" placeholder="First and Last Name" value="<?= $billing['customer_name'] ?>"/>
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input2 input_border_gray billinginput leftalign" data-field="company" placeholder="Company" value="<?= $billing['company'] ?>"/>
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input4 input_border_gray billinginput leftalign" data-field="address_1" placeholder="Address 1" value="<?= $billing['address_1'] ?>"/>
        <input type="text" class="billing_input4 input_border_gray billinginput leftalign" data-field="address_2" placeholder="Address 2" value="<?= $billing['address_2'] ?>"/>
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input5 input_border_gray billinginput leftalign" data-field="city" placeholder="City" value="<?= $billing['city'] ?>"/>
        <div id="billingstateselectarea" class="billingstateselectarea">
            <?php if (count($states) == 0) { ?>
                &nbsp;
            <?php } else { ?>
                <select class="billing_select1 input_border_gray">
                    <option value="" <?=$billing['state_id']=='' ? 'selected="selected"' : '' ?>>&nbsp;</option>
                    <?php foreach ($states as $row) { ?>
                        <option value="<?= $row['state_id'] ?>" <?= $row['state_id'] == $billing['state_id'] ? 'selected="selected"' : '' ?>><?= $row['state_code'] ?></option>
                    <?php } ?>
                </select>                
            <?php } ?>            
        </div>
        <input type="text" class="billing_input6 input_border_gray billinginput leftalign" data-field="zip" placeholder="Zip" value="<?= $billing['zip'] ?>"/>
        <select class="billing_select2 input_border_gray" data-field="country_id">
            <option value="" <?= ($billing['country_id'] == '' ? 'selected="selected"' : '') ?> ></option>
            <?php foreach ($countries as $row) { ?>
                <option value="<?= $row['country_id'] ?>" <?= $row['country_id'] == $billing['country_id'] ? 'selected="selected"' : '' ?>><?= $row['country_name'] ?></option>
            <?php } ?>            
        </select>
    </div>    
</div>    
