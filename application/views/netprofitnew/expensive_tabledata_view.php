<?php $nrow = 0; ?>
<?php foreach ($data as $row) { ?>
    <div class="detailrow <?= ($nrow % 2 == 0 ? 'grey' : 'white') ?>">
        <div class="deedcell" data-detail="<?=$row['netprofit_detail_id']?>" data-category="<?=$category?>">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </div>
        <div class="amount">
            <input class="amount purchaseinput" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="amount" data-detailtype="<?=$category?>" type="text" value="<?=MoneyOutput($row['amount'],2)?>"/>
        </div>
        <div class="vendor">
            <input class="vendor purchaseinput" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="vendor" data-detailtype="<?=$category?>" type="text" value="<?=$row['vendor']?>"/>
        </div>
        <div class="category">
            <select class="category purchaseselect" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="netprofit_category_id" data-detailtype="<?=$category?>">
                <?php if (empty($row['netprofit_category_id'])) { ?>
                    <option value="" selected="selected">Unclassified</option>
                <?php } ?>
                <?php foreach ($categories as $trow) { ?>
                    <option value="<?=$trow['netprofit_category_id']?>" <?=($trow['netprofit_category_id']==$row['netprofit_category_id'] ? 'selected="selected"' : '')?> ><?=$trow['category_name']?></option>
                <?php } ?>
            </select>
            <div class="newcategoryaddbtn" data-detail="<?=$row['netprofit_detail_id']?>" data-detailtype="<?=$category?>">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </div>
        </div>
        <div class="description">
            <input class="descript purchaseinput" data-detail="<?=$row['netprofit_detail_id']?>" data-detailtype="<?=$category?>" data-fld="description" type="text" value="<?=$row['description']?>"/>
        </div>
    </div>
    <?php $nrow++ ?>
<?php } ?>

<?php if ($nrow<10) { ?>
    <?php for ($i=$nrow; $i<10; $i++) { ?>
        <div class="detailrow <?= ($i% 2 == 0 ? 'grey' : 'white') ?>">
            <div class="deedcell">&nbsp;</div>
            <div class="amount">&nbsp;</div>
            <div class="vendor">&nbsp;</div>
            <div class="category">&nbsp;</div>
            <div class="description">&nbsp;</div>
        </div>
    <?php } ?>
<?php } ?>
