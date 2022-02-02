<div class="bl_ship_tax_content">            
    <div class="ship_tax_content_line1">
        <div class="ship_tax_tabs">            
            <div class="ship_tax_tabs1">
                <select class="ship_tax_select input_border_gray shipcountryselect" data-shipadr="<?=$shipadr['order_shipaddr_id']?>">
                    <?php foreach ($countries as $crow) { ?>
                        <option value="<?=$crow['country_id']?>" <?=$crow['country_id'] == $shipadr['country_id'] ? 'selected="selected"' : '' ?>><?= $crow['country_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="ship_tax_tabs2 active">&nbsp;</div>
            <div class="shipotherparamsarea">
                <?php if ($user_role=='masteradmin') { ?>
                    <div class="rushadmindate">
                        <label>Ship in past:</label>
                        <input type="text" readonly="readonly" class="shiprushpast input_border_black" id="rushpast" value=""/>
                    </div>
                <?php } ?>
                <div class="rushselectarea <?=$user_role=='masteradmin' ? 'admin' : ''?>">
                    <div class="label">Ships on:</div>
                    <div class="rushdataselect" id="rushdatalistarea"><?=$rushview?></div>
                </div>
                <input type="text" class="shiprushcost input_text_right input_border_black" value="<?=number_format($shipping['rush_price'],2) ?>"/>
            </div>
        </div>
    </div>
    <div class="ship_tax_content_line1">
        <div class="ship_tax_container">
            <div class="ship_tax_cont_bl1">
                <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_contact" placeholder="Contact Name" value="<?=$shipadr['ship_contact']?>"/>
                <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_company" placeholder="Company" value="<?=$shipadr['ship_company']?>"/>
                <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_address1" placeholder="Address Line 1" value="<?=$shipadr['ship_address1']?>"/>
                <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_address2" placeholder="Address Line 2" value="<?=$shipadr['ship_address2']?>"/>
                <input type="text" class="ship_tax_input2 input_border_gray leftalign" placeholder="Zip" value="<?= $shipadr['zip'] ?>" data-shipadr="<?=$shipadr['order_shipaddr_id']?>"/>
                <input type="text" class="ship_tax_input1 input_border_gray leftalign" placeholder="City" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" value="<?= $shipadr['city'] ?>"/>
                <div data-content="shipstateshow" data-shipadr="<?=$shipadr['order_shipaddr_id']?>">
                    <?php if (count($states) == 0) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <select class="ship_tax_select2 input_border_gray" data-shipadr="<?=$shipadr['order_shipaddr_id']?>">                            
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
                    <input type="checkbox" <?=$shipadr['resident']==1 ? 'checked="checked"' : ''?> class="input_checkbox shipadrchk" data-fldname="resident" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" style="float: left;"/>
                    <div class="label <?=$shipadr['resident']==1 ? '' : 'shipblind'?>" id="residentlabel">Resd</div>
                </div>
                <div class="line">
                    <input type="checkbox" <?=$shipadr['ship_blind']==1 ? 'checked="checked"' : ''?> class="input_checkbox shipadrchk" data-fldname="ship_blind" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" style="float: left;"/>
                    <div class="label <?=$shipadr['ship_blind']==1 ? '' : 'shipblind'?>" id="shblindlabel">Ship Blind</div>                    
                </div>
            </div>
            <div class="ship_tax_cont_bl3" data-shipadr="<?=$shipadr['order_shipaddr_id']?>"><?=$taxview?></div>
        </div>
        <div class="ship_tax_container2" data-shipadr="<?=$shipadr['order_shipaddr_id']?>">                    
            <?= $shipcostview ?>
        </div>
        <div class="shipdetailsarea">
            <div class="label">Shipping</div>
            <div class="dataarea">
                <input type="text" class="shippingcost input_text_right input_border_black" value="<?=number_format(floatval($order['shipping']),2)?>"/>
            </div>
            <div class="labeltax">Sales Tax</div>
            <div class="dataarea">
                <input type="text" class="salestaxcost input_text_right input_border_black" readonly="readonly" value="<?=MoneyOutput($order['tax'])?>"/>
            </div>            
        </div>
    </div>
</div>
