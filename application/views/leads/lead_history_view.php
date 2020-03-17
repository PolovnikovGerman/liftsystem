<?php if ($cnt==0) {?>
    &nbsp;
<?php } else { ?>
    <table class="historytable">
        <?php foreach ($data as $row) { ?>
            <tr><td class="historydat_head"><?=($row['user_name']=='' ? 'Former Employee:' : $row['user_name'])?>-<?=date('m/d/Y H:i:s',$row['created_date'])?></td></tr>
            <tr><td class="historydat_msg"><?=$row['history_message']?></td></tr>
        <?php } ?>
    </table>
<?php } ?>