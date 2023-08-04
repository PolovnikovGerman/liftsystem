<div class="relievers_keyinfo">
    <div class="sectionlabel">Key Info:</div>
    <div class="actionbtn"><?=$item['item_active']==1 ? 'Make Inactive' : 'Make Active'?></div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemparamlabel itemname">Item Name:</div>
            <div class="itemparamvalue editmode itemname">
                <input type="text" class="itemkeyinfoinput itemname <?=empty($item['item_name']) ? 'missing_info' : ''?>" data-item="item_name" value="<?=htmlspecialchars($item['item_name'])?>"/>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemtemplate">Template:</div>
            <div class="itemparamvalue editmode itemtemplate">
                <select class="keyinfodata itemdetailstemplate <?=empty($item['item_template']) ? 'missing_info' : ''?>">
                    <option value="">Template...</option>
                    <option value="Stressball" <?=$item['item_template']=='Stressball' ? 'selected="selected"' : ''?>>Stressball</option>
                    <option value="Other Item" <?=$item['item_template']=='Other Item' ? 'selected="selected"' : ''?>>Other Item</option>
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
        <?php $numpp=1;?>
        <?php foreach ($categories as $category) { ?>
            <div class="content-row">
                <div class="itemparamlabel subcategory">Sub-Cat <?=$numpp?>:</div>
                <div class="itemparamvalue editmode subcategory">
                    <select class="keyinfodata itemsubcategory" data-category="<?=$category['item_categories_id']?>">
                        <option value=""></option>
                        <?php foreach($subcategories as $subcategory) { ?>
                            <option value="<?=$subcategory['category_id']?>" <?=$subcategory['category_id']==$category['category_id'] ? 'selected="selected"' : ''?>>
                                <?=$subcategory['category_leftnavig']?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php $numpp++;?>
        <?php } ?>
        <div class="sectionseparator">&nbsp;</div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Size:</div>
            <div class="itemparamvalue editmode itemsize">
                <input type="text" class="itemkeyinfoinput itemsize <?=empty($item['item_size']) ? 'missing_info' : ''?>" data-item="item_size" value="<?=htmlspecialchars($item['item_size'])?>"/>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Material:</div>
            <div class="itemparamvalue editmode itemmaterial">
                <input type="text" class="itemkeyinfoinput itemmaterial <?=empty($item['item_material']) ? 'missing_info' : ''?>"  data-item="item_material" value="<?=$item['item_material']?>"/>
            </div>
        </div>
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