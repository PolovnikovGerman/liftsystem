<div class="itemname"><?=$item_name?></div>
<div class="itemsourcearea">
    <div class="labeltxt">Source Class:</div>
    <select class="itemsourceselect" <?=$mode=='edit' ? '' : 'disabled="disabled"'?>>
        <option value="Stock" <?=($item_source=='Stock' ? 'selected' : '')?>>Stock Source</option>
        <option value="Domestic" <?=($item_source=='Domestic' ? 'selected' : '')?>>Domestic Source</option>
        <option value="Chinese" <?=($item_source=='Chinese' ? 'selected' : '')?>>Chinese Source</option>
    </select>
    <?=$inventory_view?>
</div