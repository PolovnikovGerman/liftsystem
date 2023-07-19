<div class="datarow previewdatarow<?=$rownum==4 ? 'last' : ''?>">
    <div class="previewname" data-link="<?=$preview_link?>"><?=$out_proofname?></div>
    <?php if ($edit==1) { ?>
        <div class="previewremove" data-preview="<?=$artwork_preview_id?>"><i class="fa fa-trash-o"></i></div>
    <?php } ?>
</div>