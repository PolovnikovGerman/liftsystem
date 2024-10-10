<?php $numpp = 1 ?>
<?php foreach ($data as $row) : ?>
    <tr class="<?=$numpp%2==0 ? 'whiteline' : 'greyline'?>">
        <th scope="col"><div class="olt-td olt-number"><?=$row['numpp']?></div></th>
        <th scope="col"><div class="olt-td olt-date"><?=$row['order_date']?></div></th>
        <th scope="col"><div class="olt-td olt-order"><?=$row['order_num']?></div></th>
        <th scope="col"><div class="olt-td olt-customer"><?=(empty($row['customer_name']) ? '&nbsp;' : $row['customer_name'])?></div></th>
        <th scope="col"><div class="olt-td olt-qty"><input class="leadordlistorderqty" type="text" data-orderqty="<?=$row['order_id']?>" value="<?=($row['order_qty']==0 ? '' : $row['order_qty'])?>"/></div></th>
        <th scope="col"><div class="olt-td olt-itemnum"><?=$row['order_itemnumber']?></div></th>
        <th scope="col"><div class="olt-td olt-item"><?=$row['out_item']?></div></th>
        <th scope="col"><div class="olt-td olt-revenue"><?=$row['revenue']?></div></th>
        <th scope="col"><div class="olt-td olt-null leadorderform_save" style="visibility: hidden" data-order="<?=$row['order_id']?>"><img src="/img/icons/accept.png"></div></th>
    </tr>
    <?php $numpp++?>
<?php endforeach;?>
