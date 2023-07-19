<div class="datarow claydatarow<?=$rownum==4 ? 'last' : ''?>">
    <div class="clayname" data-link="<?=$clay_link?>"><?=$out_proofname?></div>
    <?php if ($edit==1) { ?>
    <div class="clayremove" data-clay="<?=$artwork_clay_id?>"><i class="fa fa-trash-o"></i></div>
    <?php } ?>
</div>