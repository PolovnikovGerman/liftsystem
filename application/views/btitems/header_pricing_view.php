<div class="tabledataheader" data-header="pricing" style="display: none">
    <div class="numberpp" id="addnewbtitems">
        <img src="/img/masterinvent/addinvitem_bg.png" alt="Add New"/>
    </div>
    <div class="status sortable" data-sortcell="item_active">Active
        <?php if ($sort=='item_active') : ?>
            <?php if ($direct=='asc') : ?>
                <div class="ascsort">&nbsp;</div>
            <?php else : ?>
                <div class="descsort">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="edit">Edit</div>
    <div class="suplier sortable" data-sortcell="vendor">Supplier
        <?php if ($sort=='vendor') : ?>
            <?php if ($direct=='asc') : ?>
                <div class="ascsort">&nbsp;</div>
            <?php else : ?>
                <div class="descsort">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="itemnumber sortable" data-sortcell="item_number">Item #
        <?php if ($sort=='item_number') : ?>
            <?php if ($direct=='asc') : ?>
                <div class="ascsort">&nbsp;</div>
            <?php else : ?>
                <div class="descsort">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="itemnameprice sortable" data-sortcell="item_name">Item Name
        <?php if ($sort=='item_name') : ?>
            <?php if ($direct=='asc') : ?>
                <div class="ascsort">&nbsp;</div>
            <?php else : ?>
                <div class="descsort">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php foreach ($prices as $price) : ?>
    <div class="itemprice"><?=$price['display']?></div>
    <?php endforeach; ?>
    <div class="lastupdate">Updated</div>
</div>