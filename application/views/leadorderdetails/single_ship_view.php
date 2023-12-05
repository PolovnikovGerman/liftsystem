<div class="bl_ship_tax_content">            
    <div class="ship_tax_content_line1">
        <div class="ship_tax_tabs">            
            <div class="ship_tax_tabs1">
                <select class="ship_tax_select input_border_gray shipcountryselect" disabled="disabled">
                    <?php foreach ($countries as $crow) { ?>
                        <option value="<?=$crow['country_id']?>" <?=$crow['country_id'] == $shipadr['country_id'] ? 'selected="selected"' : '' ?>><?= $crow['country_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="ship_tax_tabs2 active">&nbsp;</div>
            <div class="shipotherparamsarea">
                <div class="rushselectarea">
                    <div class="label">Ships on:</div>
                    <div class="rushdataselect" id="rushdatalistarea"><?=$rushview?></div>
                </div>
                <input type="text" class="shiprushcost input_text_right input_border_black" value="<?=MoneyOutput($shipping['rush_price'])?>" readonly="readonly"/>
            </div>
        </div>
    </div>
    <div class="ship_tax_content_line1">
        <div class="ship_tax_container">
            <div class="ship_tax_cont_bl1">
                <input class="ship_tax_textareainpt leftalign" readonly="readonly" placeholder="Contact Name" value="<?=$shipadr['ship_contact']?>"/>
                <input class="ship_tax_textareainpt leftalign" readonly="readonly" placeholder="Company" value="<?=$shipadr['ship_company']?>"/>
                <input class="ship_tax_textareainpt leftalign" readonly="readonly" placeholder="Address Line 1" value="<?=$shipadr['ship_address1']?>"/>
                <input class="ship_tax_textareainpt leftalign" readonly="readonly" placeholder="Address Line 2" value="<?=$shipadr['ship_address2']?>"/>
                <input type="text" class="ship_tax_input1 leftalign" placeholder="City" readonly="readonly" value="<?= $shipadr['city'] ?>">
                <div data-content="shipstateshow" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" style="float: left; width: 42px;">
                    <?php if (count($states) == 0) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <select class="ship_tax_select2" disabled="disabled">
                            <option value="">&nbsp;</option>
                            <?php foreach ($states as $srow) { ?>
                                <option value="<?= $srow['state_id'] ?>" <?= $srow['state_id'] == $shipadr['state_id'] ? 'selected="selected"' : '' ?>><?= $srow['state_code'] ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
                <input type="text" class="ship_tax_input2 leftalign" placeholder="Zip" value="<?= $shipadr['zip'] ?>" readonly="readonly"/>
            </div>
            <div class="shippingadrescopy">
                <i class="fa fa-copy"></i>
            </div>
            <textarea id="shipingcompileaddress" style="display: none"><?=$shipaddress?></textarea>
            <div class="ship_tax_cont_bl2 viewdata">
                <div class="line">
                    <input type="checkbox" <?=$shipadr['resident']==1 ? 'checked="checked"' : ''?> class="input_checkbox shipadrchk" disabled="disabled" style="float: left;"/>
                    <div class="label <?=$shipadr['resident']==1 ? '' : 'shipblind'?>" id="residentlabel">Resd</div>
                </div>
                <div class="line">
                    <input type="checkbox" <?=$shipadr['ship_blind']==1 ? 'checked="checked"' : ''?> class="input_checkbox shipadrchk" disabled="disabled" style="float: left;"/>
                    <div class="label <?=$shipadr['ship_blind']==1 ? '' : 'shipblind'?>" id="shblindlabel">Ship Blind</div>                    
                </div>
            </div>
            <div class="ship_tax_cont_bl3"><?=$taxview?></div>
        </div>
        <div class="ship_tax_container2" data-shipadr="<?=$shipadr['order_shipaddr_id']?>">                    
            <?= $shipcostview ?>
        </div>
        <div class="shipdetailsarea">
            <div class="label">Shipping</div>
            <div class="dataarea">
                <input type="text" class="shippingcost input_text_right input_border_black" value="<?=($order['shipping']==0 ? '' : MoneyOutput($order['shipping']))?>" readonly="readonly"/>
            </div>
            <div class="labeltax">Sales Tax</div>
            <div class="dataarea">
                <input type="text" class="salestaxcost input_text_right input_border_black" value="<?=$order['tax']==0 ? '' : MoneyOutput($order['tax'])?>" readonly="readonly"/>
            </div>
        </div>
    </div>
</div>