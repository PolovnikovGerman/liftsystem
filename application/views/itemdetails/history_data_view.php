<?php $curcode=(count($data) > 0 ? $data[0]['article'] : '');?>
<?php $numpp = 0;?>
<?php foreach ($data as $row) { ?>
    <div class="historydatarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$row['article']!==$curcode?> ? 'newarticle' : ''?>" >
        <div class="historydate"><?=date('M j, Y - H:i:s', strtotime($row['added_at']))?></div>
        <div class="historyuser"><?=$row['user_name']?></div>
        <div class="historytext"><?=$row['item_key']?> - <?=$row['change_txt']?></div>
    </div>
    <?php if ($curcode!==$row['article']) $curcode = $row['item_key']?>
    <?php $numpp++;?>
<?php } ?>
<?php if ($numpp < 20) { ?>
    <?php for ($i=$numpp; $i <=20; $i++) { ?>
        <div class="historydatarow <?=$i%2==0 ? 'whitedatarow' : 'greydatarow'?>">
            <div class="historydate">&nbsp;</div>
            <div class="historyuser">&nbsp;</div>
            <div class="historytext">&nbsp;</div>
        </div>
    <?php } ?>
<?php } ?>
