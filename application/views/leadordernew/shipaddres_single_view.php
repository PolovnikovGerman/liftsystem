<div class="shiptax_bodyleft">
    <div class="btn_blind">BLIND</div>
    <!-- Change design for 0 / 1 -->
    <div class="inptaddress_country">
        <select class="shipaddressdat" name="shipadrcntr" data-address="<?= $address['order_shipaddr_id'] ?>"
                data-fld="country_id" <?=$edit==0 ? 'disabled' : '' ?>>
            <option value="">Select country</option>
            <?php foreach ($countries as $country) : ?>
                <option value="<?= $country['country_id'] ?>" <?= $country['country_id'] == $address['country_id'] ? 'selected' : '' ?>><?= $country['country_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="shipaddress_box">
        <div class="copyaddress" data-addresstype="shipping" data-address="<?=$address['order_shipaddr_id'] ?>"><i class="fa fa-clone" aria-hidden="true"></i></div>
        <textarea class="fulladdressview" data-addresstype="shipping" data-address="<?=$address['order_shipaddr_id'] ?>"><?=$shipaddress?></textarea>
        <input class="inpt_addressarea inptaddress_name" type="text" name="shipname" <?=$edit==0 ? 'readonly="readonly"' : ''?>
               placeholder="Contact Name" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_contact"
               value="<?= $address['ship_contact'] ?>"/>
        <input class="inpt_addressarea inptaddress_company" type="text" name="shipcompany" <?=$edit==0 ? 'readonly="readonly"' : ''?>
               placeholder="Company" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_company"
               value="<?= $address['ship_company'] ?>"/>
        <input class="inpt_addressarea inptaddress_addressline" type="text" name="shipaddr1" <?=$edit==0 ? 'readonly="readonly"' : ''?>
               placeholder="Address Line 1" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_address1"
               value="<?= $address['ship_address1'] ?>"/>
        <input class="inpt_addressarea inptaddress_addressline" type="text" name="shipaddr2" <?=$edit==0 ? 'readonly="readonly"' : ''?>
               placeholder="Address Line 2" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="ship_address2"
               value="<?= $address['ship_address2'] ?>"/>
        <input class="inpt_addressarea inptaddress_city" type="text" name="shipcity" <?=$edit==0 ? 'readonly="readonly"' : ''?>
               placeholder="City" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="city"
               value="<?= $address['city'] ?>"/>
        <?php if (count($states) > 0) : ?>
            <select class="select_addressarea" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="state_id" <?=$edit==0 ? 'disabled' : ''?>>
                <option value="">State</option>
                <?php foreach ($states as $state) : ?>
                    <option value="<?= $state['state_id'] ?>" <?= $state['state_id'] == $address['state_id'] ? 'selected="selected"' : '' ?>><?= $state['state_code'] ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <input class="inpt_addressarea inptaddress_zipcode" type="text" name="shipzip" <?=$edit==0 ? 'readonly="readonly"' : ''?>
               placeholder="Zip Code" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="zip"
               value="<?= $address['zip'] ?>"/>
    </div>
</div>
<div class="shiptax_bodyright">
    <div class="shiptax_topright">
        <div class="shiptax_groupdates">
            <div class="groupdates_block">
                <div class="groupdates_title">Ships on:</div>
                <div class="groupdates_box" data-fld="shipdate"><?=empty($shipping['shipdate']) ? '' : date('M j', $shipping['shipdate'])?></div>
            </div>
            <div class="groupdates_block">
                <div class="groupdates_title">Arives on:</div>
                <div class="groupdates_box" data-fld="arrive_date"><?=empty($shipping['arrive_date']) ? '' : date('M j', $shipping['arrive_date'])?></div>
            </div>
            <div class="groupdates_block">
                <div class="groupdates_title">Event Date:</div>
                <div class="groupdates_box" data-fld="event_date"><?=empty($shipping['event_date']) ? '&nbsp;' : date('M j', $shipping['event_date'])?></div>
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
                <div class="infoshiptax_pricebox">
                    <?php if ($edit==0) : ?>
                    <?=MoneyOutput($shipping['rush_price'])?>
                    <?php else : ?>
                    <input type="text" class="inptpaymentdata" data-fld="rush_price" value="<?=$shipping['rush_price']?>"/>
                    <?php endif; ?>
                </div>
                <div class="infoshiptax_title">Production:</div>
            </div>
            <div class="infoshiptax_row">
                <div class="infoshiptax_pricebox">
                    <?php if ($edit==0) : ?>
                        <?=MoneyOutput($order['shipping'])?></div>
                    <?php else : ?>
                    <input type="text" class="inptpaymentdata" data-fld="rush_price" value="<?=$order['shipping']?>"/>
                    <?php endif; ?>
                <div class="infoshiptax_title">Shipping:</div>
                <?php foreach ($address['shipping_costs'] as $shipping_cost) : ?>
                    <?php if ($shipping_cost['current'] == 1) : ?>
                        <div class="infoshiptax_infotext">[ <?=$shipping_cost['shipping_method']?> - <?=MoneyOutput($shipping_cost['shipping_cost'])?> -
                            <span class="bluetxt"><?=date('D - M j', $shipping_cost['arrive_date'])?></span> ] -</div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="infoshiptax_row">
                <div class="infoshiptax_pricebox">
                    <?php if ($edit==0) : ?>
                        <?=MoneyOutput($address['sales_tax'])?>
                    <?php else: ?>
                        <input type="text" class="inptpaymentdata" data-fld="rush_price" value="<?=$address['sales_tax']?>"/>
                    <?php endif; ?>
                </div>
                <div class="infoshiptax_title">Sale Tax:</div>
                <?php if ($address['taxview']==0) : ?>
                    <div class="infoshiptax_infotext">[ Out of State - <span class="italictxt">No sales tax</span> ] -</div>
                <?php else : ?>
                    <div class="infoshiptax_infotext taxdataview">[ <span class="bluetxt">NJ <?=$this->config->item('outsalestax')?>% Tax </span>
                        <input type="checkbox" class="excepttax" <?=$edit==0 ? 'disabled="disabled"' : ''?> <?=($address['tax_exempt']==1 ? 'checked="checked"' : '')?> />
                        Exempt
                        <select class="taxexcept_select input_border_black" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
                            <option value="" <?=$address['tax_reason']=='' ? 'selected="selected"' : ''?>>....</option>
                            <option value="Non-profit" <?=$address['tax_reason']=='Non-profit' ? 'selected="selected"' : ''?>>Non-profit</option>
                            <option value="School" <?=$address['tax_reason']=='School' ? 'selected="selected"' : ''?>>School</option>
                            <option value="Government" <?=$address['tax_reason']=='Government' ? 'selected="selected"' : ''?>>Government</option>
                            <option value="Reseller" <?=$address['tax_reason']=='Reseller' ? 'selected="selected"' : ''?>>Reseller</option>
                        </select>
                        <span class="icon_file">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                        </span>
                        ] -
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
