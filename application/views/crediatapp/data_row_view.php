<div class="edit active" data-appdat="<?= $creditapp_line_id ?>"><i class="fa fa-pencil"></i></div>
<div class="status <?=$status ?>" data-appdat="<?= $creditapp_line_id ?>"><?=$status?></div>
<div class="customer truncatedfld"><?=$customer?></div>
<div class="abbrev"><?=$abbrev ?></div>
<div class="phone truncatedfld"><?=$phone?></div>
<div class="email truncatedfld"><?=$email?></div>
<div class="notes truncatedfld"><?=$notes?></div>
<div class="revision"><?=($reviewby == '' ? '&nbsp;' : $reviewby) ?></div>
<div class="doclnk <?= (empty($document_link) ? '' : 'fillingdoc') ?>" data-appdat="<?= $creditapp_line_id ?>">
    <i class="fa fa-file-text-o"></i>
</div>
