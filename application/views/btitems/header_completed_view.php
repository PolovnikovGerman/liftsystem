<div class="tabledataheader" data-header="complete" style="display: block">
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
    <div class="subcategory sortable" data-sortcell="category">Subcategory
        <?php if ($sort=='category') : ?>
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
    <div class="itemname sortable" data-sortcell="item_name">Item Name
        <?php if ($sort=='item_name') : ?>
            <?php if ($direct=='asc') : ?>
                <div class="ascsort">&nbsp;</div>
            <?php else : ?>
                <div class="descsort">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="suplier sortable" data-sortcell="vendor">Supplier
        <?php if ($sort=='vendor') : ?>
            <?php if ($direct=='asc') : ?>
                <div class="ascsort">&nbsp;</div>
            <?php else : ?>
                <div class="descsort">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="missinfo">Complete or Missing Info</div>
</div>
