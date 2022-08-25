<div class="relievers_keyinfo">
    <div class="sectionlabel">Key Info:</div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemparamlabel itemactive">Active:</div>
            <div class="itemparamvalue editmode itemactive">
                <select class="keyinfodata itemdetailsstatus">
                    <option value="1" <?=$item['item_active']==1 ? 'selected="selected"' : ''?>>Active</option>
                    <option value="0" <?=$item['item_active']==0 ? 'selected="selected"' : ''?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemactive">Template:</div>
            <div class="itemparamvalue editmode itemtemplate">
                <select class="keyinfodata itemdetailstemplate <?=empty($item['item_template']) ? 'missing_info' : ''?>">
                    <option value="">Template...</option>
                    <option value="Stock Stress Reliever" <?=$item['item_template']=='Stock Stress Reliever' ? 'selected="selected"' : ''?>>Stock Stress Reliever</option>
                    <option value="Special Order Stress Reliever" <?=$item['item_template']=='Special Order Stress Reliever' ? 'selected="selected"' : ''?>>Special Order Stress Reliever</option>
                    <option value="Health Item" <?=$item['item_template']=='Health Item' ? 'selected="selected"' : ''?>>Health Item</option>
                </select>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel tags">Tags:</div>
            <div class="tags-checkbox editmode" data-item="item_new">
                <?php if ($item['item_new']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label <?=$item['item_new']==1 ? 'active' : ''?>" data-item="item_new">New</div>
            <div class="tags-checkbox editmode" data-item="item_sale">
                <?php if ($item['item_sale']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label <?=$item['item_sale']==1 ? 'active' : ''?>" data-item="item_sale">Sale</div>
            <div class="tags-checkbox editmode" data-item="item_topsale">
                <?php if ($item['item_topsale']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label <?=$item['item_topsale']==1 ? 'active' : ''?>" data-item="item_topsale">Top Seller</div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel subcategory">Sub-Cat 1:</div>
            <div class="itemparamvalue editmode subcategory">
                <select class="keyinfodata itemsubcategory <?=empty($item['subcategory_id']) ? 'missing_info' : ''?>">
                    <option value=""></option>
                    <?php foreach($subcategories as $subcategory) { ?>
                        <option value="<?=$subcategory['subcategory_id']?>" <?=$subcategory['subcategory_id']==$item['subcategory_id'] ? 'selected="selected"' : ''?>>
                            <?=$subcategory['subcategory_name']?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="sectionseparator">&nbsp;</div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Size:</div>
            <div class="itemparamvalue editmode itemsize">
                <input type="text" class="itemkeyinfoinput itemsize <?=empty($item['item_size']) ? 'missing_info' : ''?>" data-item="item_size" value="<?=$item['item_size']?>"/>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Material:</div>
            <div class="itemparamvalue editmode itemmaterial">
                <input type="text" class="itemkeyinfoinput itemmaterial <?=empty($item['item_material']) ? 'missing_info' : ''?>"  data-item="item_material" value="<?=$item['item_material']?>"/>
            </div>
        </div>
        <div class="sectionseparator">&nbsp;</div>
        <div class="content-row">
            <div class="itemparamlabel itemdescrip">Description:</div>
            <div class="itemparamvalue editmode itemdescrip">
                <textarea class="inputkeyinfotext itemdescription <?=empty($item['item_description1']) ? 'missing_info' : ''?>"><?=$item['item_description1']?></textarea>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel bullets">Bullet Points:</div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue editmode itembullet">
                <input type="text" class="itemkeyinfoinput itembullet" data-item="bullet1" value="<?=$item['bullet1']?>"/>
            </div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue editmode itembullet">
                <input type="text" class="itemkeyinfoinput itembullet" data-item="bullet2" value="<?=$item['bullet2']?>"/>
            </div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue editmode itembullet">
                <input type="text" class="itemkeyinfoinput itembullet" data-item="bullet3" value="<?=$item['bullet3']?>"/>
            </div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue editmode itembullet">
                <input type="text" class="itemkeyinfoinput itembullet" data-item="bullet4" value="<?=$item['bullet4']?>"/>
            </div>
        </div>
    </div>
</div>