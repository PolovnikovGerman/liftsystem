<div class="creditappdatarow newapp" id=""></div>
<?php $nrow=0;?>
<?php foreach ($data as $row) { ?>
<div class="creditappdatarow <?=$nrow%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-appdat="<?=$row['creditapp_line_id']?>">
    <div class="edit active" data-appdat="<?=$row['creditapp_line_id']?>"><i class="fa fa-pencil"></i></div>
    <div class="status <?=$row['status']?>" data-appdat="<?=$row['creditapp_line_id']?>"><?=$row['status']?></div>
    <div class="customer truncateoverflowtext"><?=$row['customer']?></div>
    <div class="abbrev"><?=$row['abbrev']?></div>
    <div class="phone truncateoverflowtext"><?=$row['phone']?></div>
    <div class="email truncateoverflowtext"><?=$row['email']?></div>
    <div class="notes truncateoverflowtext"><?=$row['notes']?></div>
    <div class="revision"><?=($row['reviewby']=='' ? '&nbsp;' : $row['reviewby'])?></div>
    <div class="doclnk <?=(empty($row['document_link']) ? '' : 'fillingdoc')?>" data-appdat="<?=$row['creditapp_line_id']?>">
        <i class="fa fa-file-text-o"></i>
    </div>
</div>
<?php $nrow++?>
<?php } ?>
<?php for ($i=$numrec ; $i<25; $i++) { ?>
<div class="creditappdatarow <?=$nrow%2==0 ? 'grey' : 'white'?>">
    <div class="edit">&nbsp;</div>
    <div class="statusempty"></div>
    <div class="customer truncatedfld">&nbsp;</div>
    <div class="abbrev">&nbsp;</div>
    <div class="phone truncatedfld">&nbsp;</div>
    <div class="email truncatedfld">&nbsp;</div>
    <div class="notes truncatedfld">&nbsp;</div>
    <div class="revision">&nbsp;</div>
    <div class="doclnk" >&nbsp;</div>
</div>    
<?php $nrow++; ?>
<?php } ?>