<?php if ($count==0) { ?>
    <div class="vendorparam_value emptypricedocs">Empty pricing sheets list</div>
<?php } else { ?>
    <div class="content-row">
        <div class="pricedoc_icon" data-file="<?=$docs[0]['doc_url']?>" data-source="<?=$docs[0]['doc_name']?>">
            <i class="fa fa-file-text-o" aria-hidden="true"></i>
        </div>
        <div class="pricedoc_label">Pricing Sheet</div>
        <div class="priceyear_label">Year</div>
        <div class="pricedoc_year"><?=$docs[0]['doc_year']?></div>
    </div>
    <?php if ($count > 1) { ?>
        <div class="content-row">
            <div class="pricedocs_view">View historic pricing sheets</div>
        </div>
    <?php } ?>
<?php } ?>