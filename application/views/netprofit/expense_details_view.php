<div class="label"><?=$label?></div>
<div class="totals">
    <div class="label">Totals</div>
    <div class="value"><?=MoneyOutput($total,2)?></div>
</div>
<div class="tablehead">
    <div class="amount">Amount</div>
    <div class="vendor">Vendor</div>
    <div class="category_name">Category</div>
    <div class="description">Description</div>
</div>
<div class="tablebody">
    <?php $nrow=0;?>
    <?php foreach ($data as $row) { ?>
    <div class="tablerow <?=$nrow%2==0 ? 'grey' : 'white'?>">
        <div class="amount <?=$row['amount_class']?>"><?=$row['amount_out']?></div>
        <div class="vendor"><?=(empty($row['vendor']) ? '&nbsp;' : $row['vendor'])?></div>
        <div class="category_name"><?=(empty($row['category_name']) ? 'Unclassified' : $row['category_name'])?></div>
        <div class="description"><?=(empty($row['description']) ? '&nbsp;' : $row['description'])?></div>        
    </div>
    <?php $nrow++;?>
    <?php } ?>
</div>
