<div class="shipdocs_link <?=$multyship==1 ? 'multyship' : ''?>" data-shipdoc="<?=$shipdoc?>" data-link="<?=$doclink?>" data-source="<?=$docsource?>" title="<?=$docsource?>">
    <?php if ($doctype=='pdf'): ?>
    <i class="fa fa-file-pdf-o"></i>
    <?php elseif ($doctype=='word'): ?>
    <i class="fa fa-file-word-o"></i>
    <?php else: ?>
    <i class="fa fa-file-excel-o"></i>
    <?php endif; ?>
</div>
<div class="shipdocs_delete <?=$multyship==1 ? 'multyship' : ''?>" data-shipdoc="<?=$shipdoc?>"><i class="fa fa-times"></i></div>
