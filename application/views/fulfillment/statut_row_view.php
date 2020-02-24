<div class="status_data_row <?=($row['order_class']=='' ? ($i%2==0 ? 'whitedatarow' : 'greydatarow') : $row['order_class'])?>">
    <div class="status_orddate"><?=$row['order_out_date']?></div>
    <div class="status_lastupd"><?=$row['order_out_upd']?></div>
    <div class="status_ordernum"><?=$row['order_num']?></div>
    <div class="status_customer"><?=$row['customer_name']?></div>
    <div class="status_revenue"><?=$row['out_revenue']?></div>
    <div class="status_name"><?=$row['order_out_status']?></div>
</div>
