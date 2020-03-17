<div class="netproofw9workarea">
    <input type="hidden" id="w9workeditsession" value="<?=$session?>"/>
    <div class="title">W9 Work for Week of <?=date('m/d/Y', $datebgn)?> - <?=date('m/d/Y', $dateend)?></div>
    <div class="editdataarea">
        <div class="editdetailarea">
            <div class="tablehead">
                <div class="deedcell">
                    <i class="fa fa-plus-circle" aria-hidden="true" id="addneww9workdetails"></i>
                </div>
                <div class="amount">Amount</div>
                <div class="vendor">Vendor</div>
                <div class="category">Category</div>
                <div class="description">Description</div>
            </div>
            <div class="tablebody"><?=$tableview?></div>
            <div class="totaldata">
                <div class="label">Total: </div>
                <div class="value" id="w9workpopuptotalvalue"><?=MoneyOutput($profit_w9)?></div>
            </div>
            <div class="savedata" id="w9workpopupsavevalue">Save</div>
        </div>
    </div>
</div>