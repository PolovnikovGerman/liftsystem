<?php
if ($catval=='0') {
    $elclass='category_empty';
} else {
    $elclass='category_exist';
}
?>
<select id="<?=$el_id?>" class="<?=$elclass?>" onchange="change_category(this);" style="width: 99px;font-size: 10px;">
    <option value="0">--select--</option>
    <?php foreach ($categ_list as $cat) { ?>
        <option value="<?=$cat['category_id']?>" <?=($cat['category_id']==$catval ? 'selected="selected"' : '')?> ><?=$cat['category_name']?></option>
    <?php } ?>
</select>
