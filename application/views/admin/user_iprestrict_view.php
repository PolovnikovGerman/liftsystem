<?php foreach ($userip as $row) { ?>
    <div class="inputrestrictip">
        <input type="text" class="large iprestrict" data-key="<?=$row['user_restriction_id']?>"  value="<?=$row['ip_address']?>"/>
    </div>
    <div class="removerestrict" data-key="<?=$row['user_restriction_id']?>">
        <i class="fa fa-trash-o" aria-hidden="true"></i>
    </div>
<?php } ?>
