<div class="content-row">
    <div class="col-1">
        <div class="pricedoc_icon" data-file="<?=$docs[0]['doc_url']?>" data-source="<?=$docs[0]['doc_name']?>">
            <i class="fa fa-file-text-o" aria-hidden="true"></i>
        </div>
    </div>
    <div class="col-6 pr-0">
        <div class="pricedoc_label">Pricing Sheet</div>
    </div>
    <div class="col-2">
        <div class="priceyear_label">Year</div>
    </div>
    <div class="col-2">
        <div class="pricedoc_year"><?=$docs[0]['doc_year']?></div>
    </div>
</div>
<?php if ($count > 1) { ?>
    <div class="content-row">
        <div class="col-12">
            <div class="pricedocs_view">View historic pricing sheets</div>
        </div>
    </div>

    <div class="historicpricingarea">
        <?php for($i=1; $i<=$listcnt; $i++) { ?>
            <div class="doccontent_row <?=$i%2==0 ? 'greydatarow' : 'whitedatarow'?>">
                <?php if (isset($docs[$i])) { ?>
                    <div class="historicpricedoc_icon" data-file="<?=$docs[$i]['doc_url']?>" data-source="<?=$docs[$i]['doc_name']?>">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    </div>
                    <div class="historicpricedoc_label">Historic Pricing</div>
                    <div class="priceyear_label">Year</div>
                    <div class="pricedoc_year"><?=$docs[$i]['doc_year']?></div>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <div class="hidepricelists">
        <i class="fa fa-chevron-up" aria-hidden="true"></i>
        <span>Hide Historic Pricing</span>
    </div>
<?php } ?>
