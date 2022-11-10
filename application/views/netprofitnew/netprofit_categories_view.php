<div class="netprofitcategoryeditarea">
    <input type="hidden" id="netprofitcategorytype" value="<?=$category_type?>" />
    <div class="title">Manage Categories <?=$category_type?></div>
    <div class="category_table">
        <div class="tablehead">
            <div class="deedcell">
                <i class="fa fa-plus-circle" aria-hidden="true" id="addnewcategoryprofit"></i>
            </div>
            <div class="category_name">Category</div>
        </div>
        <div class="tablebody">
            <?=$tableview?>
        </div>
    </div>
</div>