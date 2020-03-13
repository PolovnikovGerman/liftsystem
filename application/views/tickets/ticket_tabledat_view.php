<?php foreach ($tickets as $row) {?>
<div class="ticket_row">
    <div class="ticket_adjast"><?=$row['ticket_adjast']?></div>
    <div class="ticket_number_dat" id="tick<?=$row['ticket_id']?>"><?=$row['ticket_num']?></div>
    <div class="ticket_date_dat"><?=$row['ticket_date']?></div>
    <div class="<?=$row['customer_class']?> <?=$row['closedclass']?>">
        <div class="ticket_type"><?=$row['type']?></div>
        <div class="ticket_order"><?=$row['order_num']?></div>
        <div class="ticket_customer"><?=$row['customer']?></div>
        <div class="ticket_issue"><?=$row['custom_issue']?></div>
    </div>
    <div class="ticket_attachment_dat <?=$row['attach_class']?> <?=$row['closedclass']?>" title="<?=$row['attach_title']?>" id="tickatt<?=($row['ticket_id'])?>">
        -open-
    </div>
    <div class="<?=$row['vendor_class']?> <?=$row['closedclass']?>">
        <div class="vendor_cost"><?=$row['cost']?></div>
        <div class="vendor_name"><?=$row['vendor']?></div>                                
        <div class="vendor_issue_dat"><?=$row['vendor_issue']?></div>                                
    </div>                            
</div>
<?php } ?>
