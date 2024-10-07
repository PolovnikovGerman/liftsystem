<?php $numpp = 0;?>
<?php foreach ($data as $row) { ?>
    <div class="historydatarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$row['newaricle']==1 ? 'newarticle' : ''?>">
        <div class="historydate"><?=$row['date']?></div>
        <div class="historyuser"><?=$row['user_name']?></div>
        <div class="historytext"><?=$row['item_key']?> - <?=$row['change_txt']?></div>
    </div>
    <?php $numpp++;?>
<?php } ?>
<?php if ($numpp < 19) { ?>
    <?php for ($i=$numpp; $i <=19; $i++) { ?>
        <div class="historydatarow <?=$i%2==0 ? 'whitedatarow' : 'greydatarow'?>">
            <div class="historydate">&nbsp;</div>
            <div class="historyuser">&nbsp;</div>
            <div class="historytext">&nbsp;</div>
        </div>
    <?php } ?>
<?php } ?>
