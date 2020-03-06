<ul>
    <?php $numpp=0;?>
    <?php foreach ($lnks as $row) {?>
        <li class="monthselect <?=$row['link_class']?>" id="month<?=$row['month']?>"><?=$row['month_name']?></li>    
        <?php $numpp++;?>
        <?php if ($numpp<$numrec) {?>
        <li class="normal">-</li>
        <?php } ?>
    <?php } ?>
</ul>
