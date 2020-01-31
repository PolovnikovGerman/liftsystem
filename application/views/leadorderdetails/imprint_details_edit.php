<input type="hidden" id="imprintsession" value="<?=$imprintsession?>"/>
<div class="imprintdetailsarea">
    <div class="imprintdetailsdata">
        <div class="imprintdetailstitle">
            <div class="orderblank">
                <input type="checkbox" class="orderblankchk" data-fldname="order_blank" <?=($order_blank==1 ? 'checked' : '')?> />
                <div class="labeltxt">Blank, no imprinting</div>
            </div>
        </div>
        <div class="imprintdetailsdata_left">
            <?php for ($i=0; $i<6; $i++) { ?>
            <?php $row=$details[$i];?>
            <div class="imprintlocdata <?=$row['active']==0 ? '' : 'active'?>" data-details="<?=$row['order_imprindetail_id']?>">
                <div class="imprintlocrow">
                    <input type="checkbox" class="locationactive" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==1 ? 'checked="checked"' : ''?>/>
                    <div class="title"><?=$row['title']?></div>
                    <select class="locationtype" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?>>
                        <option value="NEW" <?=$row['imprint_type']=='NEW' ? 'selected="selected"' : ''?>>New</option>
                        <option value="REPEAT" <?=$row['imprint_type']=='REPEAT' ? 'selected="selected"' : ''?>>Repeat</option>
                    </select>
                    <div class="repeatdetail <?=(empty($row['repeat_note']) ? '' : 'full')?> <?=($row['active']==0 || empty($row['repeat_note'])) ? '' : 'active'?>" data-details="<?=$row['order_imprindetail_id']?>">&nbsp;</div>
                    <select class="imprintcolorschoice" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?>>
                        <option value="1" <?=$row['num_colors']==1 ? 'selected="selected"' : ''?>>1</option>
                        <option value="2" <?=$row['num_colors']==2 ? 'selected="selected"' : ''?>>2</option>
                        <option value="3" <?=$row['num_colors']==3 ? 'selected="selected"' : ''?>>3</option>
                        <option value="4" <?=$row['num_colors']==4 ? 'selected="selected"' : ''?>>4</option>
                    </select>
                    <div class="label">prints</div>
                    <div class="imprintlocprices">
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_1" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?> value="<?=number_format(floatval($row['print_1']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_2" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<2 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['print_2']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_3" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<3 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['print_3']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_4" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<4 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['print_4']),2)?>"/>
                    </div>
                </div>
                <div class="imprintlocrow">
                    <select class="imprintlocationchoice" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?>>
                        <option value="" <?=$row['location_id']=='' ? 'selected="selected"' : ''?>>Choose for me</option>
                        <?php foreach ($imprints as $irow) { ?>
                        <option value="<?=$irow['item_inprint_id']?>" <?=$row['location_id']==$irow['item_inprint_id'] ? 'selected="selected"' : ''?>><?=$irow['item_inprint_location']?></option>                        
                        <?php } ?>
                    </select>
                    <div class="extracostlabel">extra cost</div>
                    <input type="text" class="imprintprice input_text_right" data-fldname="extra_cost" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?> value="<?=number_format(floatval($row['extra_cost']),2)?>"/>
                    <div class="label">setup</div>
                    <div class="imprintlocprices">
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_1" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?> value="<?=number_format(floatval($row['setup_1']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_2" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<2 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['setup_2']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_3" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<3 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['setup_3']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_4" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<4 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['setup_4']),2)?>"/>
                    </div>                    
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="imprintdetailsdata_right">
            <?php for ($i=6; $i<12; $i++) { ?>
            <?php $row=$details[$i];?>
            <div class="imprintlocdata <?=$row['active']==0 ? '' : 'active'?>" data-details="<?=$row['order_imprindetail_id']?>">
                <div class="imprintlocrow">
                    <input type="checkbox" class="locationactive" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==1 ? 'checked="checked"' : ''?>/>
                    <div class="title"><?=$row['title']?></div>
                    <select class="locationtype" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?>>
                        <option value="NEW" <?=$row['imprint_type']=='NEW' ? 'selected="selected"' : ''?>>New</option>
                        <option value="REPEAT" <?=$row['imprint_type']=='REPEAT' ? 'selected="selected"' : ''?>>Repeat</option>
                    </select>
                    <div class="repeatdetail <?=(empty($row['repeat_note']) ? '' : 'full')?> <?=($row['active']==0 || empty($row['repeat_note'])) ? '' : 'active'?>" data-details="<?=$row['order_imprindetail_id']?>">&nbsp;</div>
                    <select class="imprintcolorschoice" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?>>
                        <option value="1" <?=$row['num_colors']==1 ? 'selected="selected"' : ''?>>1</option>
                        <option value="2" <?=$row['num_colors']==2 ? 'selected="selected"' : ''?>>2</option>
                        <option value="3" <?=$row['num_colors']==3 ? 'selected="selected"' : ''?>>3</option>
                        <option value="4" <?=$row['num_colors']==4 ? 'selected="selected"' : ''?>>4</option>
                    </select>
                    <div class="label">prints</div>
                    <div class="imprintlocprices">
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_1" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?> value="<?=number_format(floatval($row['print_1']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_2" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<2 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['print_2']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_3" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<3 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['print_3']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="print_4" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<4 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['print_4']),2)?>"/>
                    </div>
                </div>
                <div class="imprintlocrow">
                    <select class="imprintlocationchoice" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?>>
                        <option value="" <?=$row['location_id']=='' ? 'selected="selected"' : ''?>>Choose for me</option>
                        <?php foreach ($imprints as $irow) { ?>
                        <option value="<?=$irow['item_inprint_id']?>" <?=$row['location_id']==$irow['item_inprint_id'] ? 'selected="selected"' : ''?>><?=$irow['item_inprint_location']?></option>                        
                        <?php } ?>
                    </select>
                    <div class="extracostlabel">extra cost</div>
                    <input type="text" class="imprintprice input_text_right" data-fldname="extra_cost" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?> value="<?=number_format(floatval($row['extra_cost']),2)?>"/>
                    <div class="label">setup</div>
                    <div class="imprintlocprices">
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_1" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ''?> value="<?=number_format(floatval($row['setup_1']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_2" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<2 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['setup_2']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_3" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<3 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['setup_3']),2)?>"/>
                        <input type="text" class="imprintprice input_text_right" data-fldname="setup_4" data-details="<?=$row['order_imprindetail_id']?>" <?=$row['active']==0 ? 'disabled="disabled"' : ($row['num_colors']<4 ? 'disabled="disabled"' : '')?> value="<?=number_format(floatval($row['setup_4']),2)?>"/>
                    </div>                    
                </div>
            </div>            
            <?php } ?>            
        </div>
    </div>
    <div class="imprintdetailssample">
        <div class="title">Item # <?=$item_number?></div>
        <div class="title"><?=$item_name?></div>
        <div class="imprinttable_title">
            <div class="label">Imprint Areas</div>
        </div>
        <div class="imprinttablelocations">
            <div class="imprinttablehead">
                <div class="label_locationname">Location</div>
                <div class="label_locationsize">Size</div>
                <div class="label_locationtemplate">Tmp</div>
            </div>
            <div class="imprinttabledata">
                <?php $numpp=1;?>
                <?php foreach ($imprints as $row) { ?>
                <div class="locationsamplerow">
                    <div class="numpp"><?=$numpp?></div>
                    <div class="locatname"><?=$row['item_inprint_location']?></div>
                    <div class="locatsize"><?=$row['item_inprint_size']?></div>
                    <div class="locattempl active" href="/leadorder/viewimprintloc?id=<?=$row['item_inprint_id']?>">&nbsp;</div>
                </div>
                <?php $numpp++;?>
                <?php } ?>
                <?php if ($numlocs<12) { ?>
                    <?php for ($i=$numlocs; $i<12; $i++) { ?>
                    <div class="locationsamplerow">
                        <div class="numpp"><?=$numpp?></div>
                        <div class="locatname addnew">- add new -</div>
                        <div class="locatsize">&nbsp;</div>
                        <div class="locattempl">&nbsp;</div>
                    </div>
                    <?php $numpp++;?>                        
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="saveimprintdetailsarea">
        <div class="saveimprintdetailsdata">save</div>
        <div class="revertimprintdetailsdata">revert</div>
    </div>    
</div>