<div class="multishipadressdatarow">
    <div class="bl_ship_tax_content">
        <div class="multishipadressdata">
            <div class="numpp"><?=$numpp?>.</div>
            <div class="qtylabel">QTY:</div>
            <div class="qtyvalue">
                <input type="text" readonly="readonly" value="<?=$shipadr['item_qty']?>" class="shipaddrqty input_border_gray" style="text-align: right;"/>
            </div>
            <div class="orderitemqty">of <?=$total_itemqty?></div>
        </div>
        <div class="ship_tax_content_line1">
            <div class="ship_tax_tabs">            
                <div class="ship_tax_tabs1">
                    <select class="ship_tax_select input_border_gray shipcountryselect" disabled="disabled">
                        <?php foreach ($countries as $crow) { ?>
                            <option value="<?= $crow['country_id'] ?>" <?= $crow['country_id'] == $shipadr['country_id'] ? 'selected="selected"' : '' ?>><?= $crow['country_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="ship_tax_content_line1">
            <div class="ship_tax_container">
                <div class="ship_tax_cont_bl1">
                    <input class="ship_tax_textareainpt input_border_gray leftalign" readonly="readonly" placeholder="Contact Name" value="<?= $shipadr['ship_contact'] ?>"/>
                    <input class="ship_tax_textareainpt input_border_gray leftalign" readonly="readonly" placeholder="Company" value="<?= $shipadr['ship_company'] ?>"/>
                    <input class="ship_tax_textareainpt input_border_gray leftalign" readonly="readonly" placeholder="Address Line 1" value="<?= $shipadr['ship_address1'] ?>"/>
                    <input class="ship_tax_textareainpt input_border_gray leftalign" readonly="readonly" placeholder="Address Line 2" value="<?= $shipadr['ship_address2'] ?>"/>
                    <input type="text" class="ship_tax_input2 input_border_gray" value="<?= $shipadr['zip'] ?>" readonly="readonly"/>
                    <input type="text" class="ship_tax_input1 input_border_gray" readonly="readonly" value="<?= $shipadr['city'] ?>">
                    <div data-content="shipstateshow" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>">
                        <?php if (count($states) == 0) { ?>
                            &nbsp;
                        <?php } else { ?>
                            <select class="ship_tax_select2 input_border_gray" disabled="disabled">
                                <option value="">&nbsp;</option>
                                <?php foreach ($states as $srow) { ?>
                                    <option value="<?= $srow['state_id'] ?>" <?= $srow['state_id'] == $shipadr['state_id'] ? 'selected="selected"' : '' ?>><?= $srow['state_code'] ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>                
                    </div>
                </div>
                <div class="ship_tax_cont_bl2">
                    <div class="line">
                        <input type="checkbox" <?= $shipadr['resident'] == 1 ? 'checked="checked"' : '' ?> class="input_checkbox shipadrchk" disabled="disabled" style="float: left;"/>
                        <div class="label <?=$shipadr['resident']==1 ? '' : 'shipblind'?>">Resd</div>
                    </div>
                    <div class="line">
                        <input type="checkbox" <?= $shipadr['ship_blind'] == 1 ? 'checked="checked"' : '' ?> class="input_checkbox shipadrchk" disabled="disabled" style="float: left;"/>
                        <div class="label <?=$shipadr['ship_blind']==1 ? '' : 'shipblind'?>">Ship Blind</div>
                    </div>
                </div>
                <div class="ship_tax_cont_bl3"><?= $taxview ?></div>
            </div>
            <div class="ship_tax_container2" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>">                    
                <?= $shipcostview ?>
            </div>
            <div class="shipdetailsarea">
                <div class="label">Shipping</div>
                <div class="dataarea">
                    <input type="text" class="shippingcost input_border_black" value="<?= ($shipadr['shipping'] == 0 ? '' : MoneyOutput($shipadr['shipping'])) ?>" readonly="readonly"/>
                </div>
                <div class="labeltax">Sales Tax</div>
                <div class="dataarea">
                    <input type="text" class="salestaxcost input_border_black" value="<?= $shipadr['sales_tax'] == 0 ? '' : MoneyOutput($shipadr['sales_tax']) ?>" readonly="readonly"/>
                </div>
                <div class="labelarrival">Arrival Date:</div>
                <div class="dataarea">
                    <input type="text" class="salesarrivedate input_border_black " value="<?=empty($shipadr['arrive_date']) ? '' : date('m/d/Y', $shipadr['arrive_date'])?>"  readonly="readonly"/>
                </div>
            </div>
        </div>
    </div>
</div>
