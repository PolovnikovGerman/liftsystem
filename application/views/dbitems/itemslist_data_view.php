<?php $numpp = 0;?>
<?php foreach ($datas as $data) { ?>
    <div class="itemlist-tablerow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="listaction">
            <div class="listitempreview" data-viewsrc="/dbitems/itemmainimage/<?=$data['item_id']?>"><i class="fa fa-search" aria-hidden="true"></i></div>
            <div class="listitemedit" data-item="<?=$data['item_id']?>"><i class="fa fa-pencil" aria-hidden="true"></i></div>
        </div>
        <div class="listnumrow"><?=$data['numpp']?></div>
        <div class="liststatus"><?=$data['item_active']==1 ? 'Active' : 'Inactive'?></div>
        <div class="listitemnumber"><?=$data['item_number']?></div>
        <div class="listitemname"><?=$data['item_name']?></div>
        <div class="listvendor" id="vendordet<?=$data['item_id']?>" data-content="<?=$data['vendor_details']?>"><?=$data['vendor']?></div>
        <div class="listcategoryname">
            <select class="itemlist_category <?=empty($data['category1']) ? '' : 'selected'?>" data-item="<?=$data['item_id']?>" data-brand="<?=$brand?>" data-categ="category1" disabled="disabled">
                <option value="0" <?=$data['category1']=='' ? 'selected="selected"' : ''?>>------------- select -------------</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?=$category['category_id']?>" <?=$data['category1']==$category['category_id'] ? 'selected="selected"' : ''?>><?=$category['category_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="listcategoryname">
            <select class="itemlist_category <?=empty($data['category2']) ? '' : 'selected'?>" data-item="<?=$data['item_id']?>" data-brand="<?=$brand?>" data-categ="category2" disabled="disabled">
                <option value="0" <?=$data['category2']=='' ? 'selected="selected"' : ''?>>------------- select -------------</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?=$category['category_id']?>" <?=$data['category2']==$category['category_id'] ? 'selected="selected"' : ''?>><?=$category['category_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="listcategoryname">
            <select class="itemlist_category <?=empty($data['category3']) ? '' : 'selected'?>" data-item="<?=$data['item_id']?>" data-brand="<?=$brand?>" data-categ="category3" disabled="disabled">
                <option value="0" <?=$data['category2']=='' ? 'selected="selected"' : ''?>>------------- select -------------</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?=$category['category_id']?>" <?=$data['category3']==$category['category_id'] ? 'selected="selected"' : ''?>><?=$category['category_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="listmissinginfo <?=$data['misinfo_class']?>" data-brand="<?=$brand?>" id="missinfo<?=$data['item_id']?>" data-content="<?=$data['misinfo_content']?>">
            <?=$data['misinfo_name']?>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>
