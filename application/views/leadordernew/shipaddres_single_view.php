<div class="shiptax_bodyleft">
    <div class="btn_blind">BLIND</div>
    <!-- Change design for 0 / 1 -->
    <div class="inptaddress_country">
        <select class="shipaddressdat" name="shipadrcntr" data-address="<?= $address['order_shipaddr_id'] ?>"
                data-fld="country_id">
            <option value="">Select country</option>
            <?php foreach ($countries as $country) : ?>
                <option value="<?= $country['country_id'] ?>" <?= $country['country_id'] == $address['country_id'] ? 'selected' : '' ?>><?= $country['country_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="shipaddress_box">
        <div class="copyaddress"><i class="fa fa-clone" aria-hidden="true"></i></div>
        <input class="inpt_addressarea inptaddress_name" type="text" name="shipname" readonly="readonly"
               placeholder="Contact Name" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_contact"
               value="<?= $address['ship_contact'] ?>"/>
        <input class="inpt_addressarea inptaddress_company" type="text" name="shipcompany" readonly="readonly"
               placeholder="Company" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_company"
               value="<?= $address['ship_company'] ?>"/>
        <input class="inpt_addressarea inptaddress_addressline" type="text" name="shipaddr1" readonly="readonly"
               placeholder="Address Line 1" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_address1"
               value="<?= $address['ship_address1'] ?>"/>
        <input class="inpt_addressarea inptaddress_addressline" type="text" name="shipaddr2" readonly="readonly"
               placeholder="Address Line 2" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_address2"
               value="<?= $address['ship_address2'] ?>"/>
        <input class="inpt_addressarea inptaddress_city" type="text" name="shipcity" readonly="readonly"
               placeholder="City" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="city"
               value="<?= $address['city'] ?>"/>
        <?php if (count($states) > 0) : ?>
            <select class="select_addressarea" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="state_id">
                <option value="">State</option>
                <?php foreach ($states as $state) : ?>
                    <option value="<?= $state['state_id'] ?> <?= $state['state_id'] == $address['state_id'] ? 'selected="selected"' : '' ?>"><?= $state['state_code'] ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <input class="inpt_addressarea inptaddress_zipcode" type="text" name="shipzip" readonly="readonly"
               placeholder="Zip Code" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="zip"
               value="<?= $address['zip'] ?>"/>
    </div>
</div>
<div class="shiptax_bodyright">
    <div class="shiptax_topright">
        <div class="shiptax_groupdates">
            <div class="groupdates_block">
                <div class="groupdates_title">Ships on:</div>
                <div class="groupdates_box"><?=date('M j', $shipping['shipdate'])?></div>
            </div>
            <div class="groupdates_block">
                <div class="groupdates_title">Arives on:</div>
                <div class="groupdates_box"><?=date('M j', $shipping['arrive_date'])?></div>
            </div>
            <div class="groupdates_block">
                <div class="groupdates_title">Event Date:</div>
                <div class="groupdates_box"><?=empty($shipping['event_date']) ? '&nbsp;' : date('M j', $shipping['event_date'])?></div>
            </div>
        </div>
    </div>
    <div class="shiptax_bottomright">
        <div class="shipdocs">
            <div class="shipdocs_txt">Ship Docs:</div>
            <?php foreach ($shipdocs as $shipdoc) : ?>
            <div class="shipdocs_icons">
                <div class="shipdocs_file"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="shiptax_info">
            <div class="infoshiptax_row">
                <div class="infoshiptax_pricebox"><?=MoneyOutput($shipping['rush_price'])?></div>
                <div class="infoshiptax_title">Production:</div>
            </div>
            <div class="infoshiptax_row">
                <div class="infoshiptax_pricebox"><?=MoneyOutput($order['shipping'])?></div>
                <div class="infoshiptax_title">Shipping:</div>
                <?php foreach ($address['shipping_costs'] as $shipping_cost) : ?>
                    <?php if ($shipping_cost['current'] == 1) : ?>
                        <div class="infoshiptax_infotext">[ <?=$shipping_cost['shipping_method']?> - <?=MoneyOutput($shipping_cost['shipping_cost'])?> -
                            <span class="bluetxt"><?=date('D - M j', $shipping_cost['arrive_date'])?></span> ] -</div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="infoshiptax_row">
                <div class="infoshiptax_pricebox"><?=MoneyOutput($address['sales_tax'])?></div>
                <div class="infoshiptax_title">Sale Tax:</div>
<!--                --><?php //if ($address['taxview']==0) : ?>
<!--                --><?php //endif; ?>
                <div class="infoshiptax_infotext">[ Out of State - <span class="italictxt">No sales tax</span> ] -</div>
            </div>
        </div>
    </div>
</div>
