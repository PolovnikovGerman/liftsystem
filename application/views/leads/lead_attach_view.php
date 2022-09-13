<?php foreach ($attachs as $attach) { ?>
    <div class="leadattachrow">
        <div class="lead_attach_view" data-link="<?=$attach['attachment']?>"><i class="fa fa-search"></i></div>
        <div class="lead_attach_srcname" title="<?=$attach['source_name']?>"><?=$attach['source_name']?></div>
        <?php if ($attach['quoteattach']==0) { ?>
            <div class="lead_attach_remove" data-attachid="<?=$attach['leadattch_id']?>"><i class="fa fa-trash"></i></div>
        <?php } ?>
    </div>
<?php } ?>