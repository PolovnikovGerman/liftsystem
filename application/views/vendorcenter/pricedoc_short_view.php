<?php if ($count==0) { ?>
    <div class="row content-row">
        <div class="col-12">
            <div class="vendorparam_value emptypricedocs">Empty pricing sheets list</div>
        </div>
    </div>
<?php } else { ?>
    <div class="row content-row">
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
        <div class="row content-row">
            <div class="col-12">
                <div class="pricedocs_view">View historic pricing sheets</div>
            </div>
        </div>
    <?php } ?>
<?php } ?>