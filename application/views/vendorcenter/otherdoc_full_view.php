<div class="otherdocslabel">All documents except pricing:</div>
<?php if ($editmode==1) { ?>
    <div class="addotherdoc">&nbsp;</div>
<?php } ?>
<div class="otherdocsarea">
    <?php for ($i=0; $i<$listcnt; $i++) { ?>
        <div class="doccontent_row <?=$i%2==0 ? 'greydatarow' : 'whitedatarow'?>">
            <?php if (isset($docs[$i])) { ?>
                <div class="historicpricedoc_icon"  data-file="<?=$docs[$i]['doc_url']?>" data-source="<?=$docs[$i]['doc_name']?>">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                </div>
                <?php if ($editmode==1) { ?>
                    <div class="otherdocdel_icon"  data-doc="<?=$docs[$i]['vendor_doc_id']?>">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </div>
                <?php } ?>
                <div class="historicotherdoc_label"><?=empty($docs[$i]['doc_description']) ? $docs[$i]['doc_name'] : $docs[$i]['doc_description']?></div>
            <?php } else { ?>
                &nbsp;
            <?php } ?>
        </div>
    <?php } ?>
</div>
<div class="hidedocumentslists">
    <i class="fa fa-chevron-up" aria-hidden="true"></i>
    <span>Hide Documents</span>
</div>
