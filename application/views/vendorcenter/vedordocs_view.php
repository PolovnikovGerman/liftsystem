<?php foreach ($vendor_docs as $row) { ?>
    <div class="content-row">
        <div class="vendordocremove" idx="<?=$row['vendor_doc_id']?>">
            <?php if ($editmode==0) { ?>
                &nbsp;
            <?php } else { ?>
                <i class="fa fa-trash-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="vendordocname"><?=$row['doc_name']?></div>
    </div>
    <div class="content-row">
        <div class="vendorparamlabel docnote">Desc:</div>
        <?php if ($editmode==0) { ?>
            <div class="viewparamvalue docnote"><?=$row['doc_description']?></div>
        <?php } else { ?>
            <input class="vendordetailsinpt docnote" data-item="doc_description" data-idx="<?=$row['vendor_doc_id']?>" value="<?=$row['doc_description']?>"/>
        <?php } ?>
    </div>
<?php } ?>