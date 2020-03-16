<div class="multishipadressdatarow">
    <div class="bl_ship_tax_content">
        <div class="multishipadressdata">
            <div class="numpp"><?=$numpp?>.</div>
            <div class="qtylabel">QTY:</div>
            <div class="qtyvalue">
                <input type="text" value="<?=$shipadr['item_qty']?>" class="shipaddrqty input_text_right shipaddrinput input_border_gray" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>" data-fldname="item_qty"/>
            </div>
            <div class="orderitemqty">of <?=$total_itemqty?></div>
            <div class="shipadrtrash">
                <i class="fa fa-trash" data-shipadr="<?=$shipadr['order_shipaddr_id']?>"></i>
            </div>
        </div>
        <div class="ship_tax_content_line1">
            <div class="ship_tax_tabs">            
                <div class="ship_tax_tabs1">
                    <select class="ship_tax_select input_border_gray shipcountryselect" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>">
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
                    <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_contact" placeholder="Contact Name" value="<?=$shipadr['ship_contact']?>"/>
                    <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_company" placeholder="Company" value="<?=$shipadr['ship_company']?>"/>
                    <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_address1" placeholder="Address Line 1" value="<?=$shipadr['ship_address1']?>"/>
                    <input class="ship_tax_textareainpt input_border_gray leftalign" data-shipadr="<?=$shipadr['order_shipaddr_id']?>" data-fldname="ship_address2" placeholder="Address Line 2" value="<?=$shipadr['ship_address2']?>"/>
                    <input type="text" class="ship_tax_input2 shipaddrinput input_border_gray" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>" data-fldname="zip" value="<?= $shipadr['zip'] ?>"/>
                    <input type="text" class="ship_tax_input1 shipaddrinput input_border_gray" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>" data-fldname="city" value="<?= $shipadr['city'] ?>">
                    <div data-content="shipstateshow" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>">
                        <?php if (count($states) == 0) { ?>
                            &nbsp;
                        <?php } else { ?>
                            <select class="ship_tax_select2 input_border_gray" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>">
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
                        <input type="checkbox" <?= $shipadr['resident'] == 1 ? 'checked="checked"' : '' ?> class="input_checkbox shipadrchk" style="float: left;" data-fldname="resident" data-shipadr="<?=$shipadr['order_shipaddr_id']?>"/>
                        <div class="label <?=$shipadr['resident']==1 ? '' : 'shipblind'?> residentlabel"  data-shipadr="<?=$shipadr['order_shipaddr_id']?>">Resd</div>             
                    </div>
                    <div class="line">
                        <input type="checkbox" <?= $shipadr['ship_blind'] == 1 ? 'checked="checked"' : '' ?> class="input_checkbox shipadrchk" style="float: left;" data-fldname="ship_blind" data-shipadr="<?=$shipadr['order_shipaddr_id']?>"/>
                        <div class="label <?=$shipadr['ship_blind']==1 ? '' : 'shipblind'?> shblindlabel"  data-shipadr="<?=$shipadr['order_shipaddr_id']?>">Ship Blind</div>
                    </div>
                </div>
                <div class="ship_tax_cont_bl3" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>"><?= $taxview ?></div>
            </div>
            <div class="ship_tax_container2" data-shipadr="<?=$shipadr['order_shipaddr_id'] ?>">                    
                <?=$shipcostview ?>
            </div>
            <div class="shipdetailsarea">
                <div class="label">Shipping</div>
                <div class="dataarea">
                    <input type="text" class="shippingcost input_text_right input_border_black shipaddrinput" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>" data-fldname="shipping" value="<?=number_format($shipadr['shipping'],2)?>"/>
                </div>
                <div class="labeltax">Sales Tax</div>
                <div class="dataarea">
                    <input type="text" class="salestaxcost input_border_black input_text_right shipaddrinput" data-shipadr="<?= $shipadr['order_shipaddr_id'] ?>" data-fldname="sales_tax" value="<?=number_format($shipadr['sales_tax'],2)?>"/>
                </div>
            </div>
        </div>
    </div>
</div>
