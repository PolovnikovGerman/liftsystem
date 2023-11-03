<div class="billing_content_linetop">
    <div class="billing_samedata_area">
        <input type="checkbox" data-field="showbilladdress" title="Billing address as Shipping Address"
               data-entity="order" class="input_checkbox chkboxleadorddata"/>
        <div class="label">same as main shipping address</div>
    </div>
</div>

<div class="billing_content1">
    <div class="billing_content1_line">
        <div class="billing_customerpotitle">PO#</div>
        <input type="text" class="billing_input3 input_border_gray billinginput leftalign" data-field="customer_ponum"
               placeholder="CuPo" value="<?= $billing['customer_ponum'] ?>">
    </div>
    <div class="billing_content1_line">
        <select class="billing_select2 input_border_gray" data-field="country_id">
            <option value="" <?= ($billing['country_id'] == '' ? 'selected="selected"' : '') ?> ></option>
            <?php foreach ($countries as $row) { ?>
                <option value="<?= $row['country_id'] ?>" <?= $row['country_id'] == $billing['country_id'] ? 'selected="selected"' : '' ?>><?= $row['country_name'] ?></option>
            <?php } ?>
        </select>
        <input type="hidden" id="billordercntcode" value="<?=$billcntcode?>"/>
    </div>
    <div class="billing_content1_line">
        <div class="billingaddressbox">
            <input type="text" class="billing_input1 billinginput leftalign" data-field="customer_name"
                   placeholder="First and Last Name" value="<?= $billing['customer_name'] ?>"/>
            <input type="text" class="billing_input2 billinginput leftalign" data-field="company"
                   placeholder="Company" value="<?= $billing['company'] ?>"/>

            <div id="billingaddresslinearea">
                <input type="text" class="billing_input4 billinginput leftalign" data-field="address_1"
                       id="billorder_line1" value="<?= $billing['address_1'] ?>"
                       autocomplete="new-password"/>
                <!-- placeholder="Address 1" -->
            </div>
            <input type="text" class="billing_input4 billinginput leftalign" data-field="address_2"
                   placeholder="Address 2" value="<?= $billing['address_2'] ?>" autocomplete="new-password"/>
            <input type="text" class="billing_input5 billinginput leftalign" data-field="city"
                   placeholder="City" value="<?= $billing['city'] ?>"/>
            <div id="billingstateselectarea" class="billingstateselectarea">
                <?php if (count($states) == 0) { ?>
                    &nbsp;
                <?php } else { ?>
                    <select class="billing_select1">
                        <option value="" <?= $billing['state_id'] == '' ? 'selected="selected"' : '' ?>>&nbsp;</option>
                        <?php foreach ($states as $row) { ?>
                            <option value="<?= $row['state_id'] ?>" <?= $row['state_id'] == $billing['state_id'] ? 'selected="selected"' : '' ?>><?= $row['state_code'] ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>
            <input type="text" class="billing_input6 billinginput leftalign" data-field="zip"
                   placeholder="Zip" value="<?= $billing['zip'] ?>"/>
        </div>
        <div class="billingaddresscopy">
            <i class="fa fa-copy"></i>
        </div>
        <textarea id="billingcompileaddress" style="display: none"><?=$billaddress?></textarea>
    </div>
</div>    
