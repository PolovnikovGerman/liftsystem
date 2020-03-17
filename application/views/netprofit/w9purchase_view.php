<div class="w9purchaseyear">
    <select class="w9workyears">
        <?php foreach ($years as $row) { ?>
        <option value="<?=$row['year']?>" <?=($row['current']==1 ? 'selected="selected"' : '')?>><?=$row['year']?></option>
        <?php } ?>
    </select>
</div>
<div class="w9purchasetitle">
    W9 Work & Purchase Breakdown: <?=  MoneyOutput($totals,2)?>
</div>
<div class="w9purchasetablearea"><?=$table_view?></div>
   