<input type="hidden" id="prooftemplflag" value="0"/>
<input type="hidden" id="prooftemplatesrc" value=""/>
<input type="hidden" id="prooftemplatename" value=""/>
<input type="hidden" id="platetemplflag" value="0"/>
<input type="hidden" id="platetemplatesrc" value=""/>
<input type="hidden" id="platetemplatename" value=""/>
<input type="hidden" id="boxtemplflag" value="0"/>
<input type="hidden" id="boxtemplatesrc" value=""/>
<input type="hidden" id="boxtemplatename" value=""/>
<div class="inventoryitem_body_content">
    <div class="datarow">
        <div class="itemdatalabel">Item Name:</div>
        <div class="itemdatainput">
            <input type="text" class="itemdataname" value="<?=$item_name?>" placeholder="Item Name"/>
        </div>
    </div>
    <div class="datarow">
        <div class="itemdatalabel">Unit Type:</div>
        <div class="itemdatainput">
            <select class="itemdataunit">
                <option value="pc" <?=$item_unit=='pc' ? 'selected="selected"' : ''?>>pc</option>
                <option value="lbs" <?=$item_unit=='lbs' ? 'selected="selected"' : ''?>>lbs</option>
                <option value="yd" <?=$item_unit=='yd' ? 'selected="selected"' : ''?>>yd</option>
            </select>
        </div>
    </div>
    <div class="datarow masteritemdevide">&nbsp;</div>
    <div class="datarow" id="prooftemplatearea">
        <div class="itemtemplatelabel">Proof Template:</div>
        <div class="itemteplatevalue edittemplate">
            <?php if (empty($proof_template)) { ?>
                <div id="proof-uploader"></div>
            <?php } else { ?>
                <div>
                    <img src="/img/masterinvent/opentemplate_btn.png" alt="Open template" data-src="<?=$proof_template?>"/>
                </div>
                <div id="proofnew-uploader"></div>
            <?php } ?>
        </div>
    </div>
    <div class="datarow" id="platetemplatearea">
        <div class="itemtemplatelabel">Plate Template:</div>
        <div class="itemteplatevalue edittemplate">
            <?php if (empty($plate_template)) { ?>
                <div id="plate-uploader"></div>
            <?php } else { ?>
                <div>
                    <img src="/img/masterinvent/opentemplate_btn.png" alt="Open template" data-src="<?=$plate_template?>"/>
                </div>
                <div id="platenew-uploader"></div>
            <?php } ?>
        </div>
    </div>
    <div class="datarow" id="boxtemplatearea">
        <div class="itemtemplatelabel">Box Template:</div>
        <div class="itemteplatevalue edittemplate">
            <?php if (empty($box_template)) { ?>
                <div id="box-uploader"></div>
            <?php } else { ?>
                <div>
                    <img src="/img/masterinvent/opentemplate_btn.png" alt="Open template" data-src="<?=$box_template?>"/>
                </div>
                <div id="boxnew-uploader"></div>
            <?php } ?>
        </div>
    </div>
</div>