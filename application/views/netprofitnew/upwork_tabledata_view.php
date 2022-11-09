<?php $nrow = 0; ?>
<?php foreach ($data as $row) { ?>
    <div class="detailrow <?= ($nrow % 2 == 0 ? 'grey' : 'white') ?>">
        <div class="deedcell" data-detail="<?=$row['netprofit_detail_id']?>" data-category="Upwork">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </div>
        <div class="amount">
            <input class="amount upworkinput" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="amount" type="text" value="<?=MoneyOutput($row['amount'],2)?>"/>
        </div>
        <div class="vendor">
            <input class="vendor upworkinput" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="vendor" type="text" value="<?=$row['vendor']?>"/>
        </div>
        <div class="category">
            <select class="category upworkselect" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="netprofit_category_id">
                <?php foreach ($category as $trow) { ?>
                    <option value="<?=$trow['netprofit_category_id']?>" <?=($trow['netprofit_category_id']==$row['netprofit_category_id'] ? 'selected="selected"' : '')?> ><?=$trow['category_name']?></option>
                <?php } ?>
            </select>
            <div class="newcategoryaddbtn" data-detail="<?=$row['netprofit_detail_id']?>" data-detailtype="Upwork">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </div>
        </div>
        <div class="description">
            <input class="descript upworkinput" data-detail="<?=$row['netprofit_detail_id']?>" data-fld="description" type="text" value="<?=$row['description']?>"/>
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
