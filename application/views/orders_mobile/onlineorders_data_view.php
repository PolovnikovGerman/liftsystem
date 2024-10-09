<?php $numpp = 0;?>
<?php foreach ($orders as $order): ?>
    <tr class="<?=$numpp%2==0 ? 'whiteline' : 'greyline'?>">
        <th scope="col"><div class="oor-td oor-statbox">
                <input class="form-check-input" type="checkbox" value="" data-order="<?=$order['order_id']?>">
            </div></th>
        <th scope="col"><div class="oor-td oor-statnum"><?=$order['order_num']?></div></th>
        <th scope="col"><div class="oor-td oor-stat"><?=$order['order_out_status']?></div></th>
        <th scope="col"><div class="oor-td oor-our"><input type="text" value="<?=$order['order_num']?>" disabled="disabled"></div></th>
        <th scope="col"><div class="oor-td oor-rep"><input type="text" value="<?=$order['order_rep']?>" disabled="disabled"></div></th>
        <th scope="col"><div class="oor-td oor-date"><?=$order['order_date_show']?></div></th>
        <th scope="col"><div class="oor-td oor-confirmation" data-order="<?=$order['order_id']?>"><?=$order['order_confirmation']?></div></th>
        <th scope="col"><div class="oor-td oor-name"><?=$order['customer_name']?></div></th>
        <th scope="col"><div class="oor-td oor-company"><?=$order['customer_company']?></div></th>
        <th scope="col"><div class="oor-td oor-item"><?=$order['item_name']?></div></th>
        <th scope="col"><div class="oor-td oor-amount"><?=$order['order_amount']?></div></th>
        <th scope="col"><div class="oor-td oor-export">&nbsp;</div></th>
    </tr>
    <?php $numpp++;?>
<?php endforeach; ?>
