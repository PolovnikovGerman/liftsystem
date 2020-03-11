<div class="netproofpurchasearea">    
    <div class="title">W9 Work &amp; Purchases</div>
    <div class="subtitle">for Week of <?= date('m/d/Y', $datebgn) ?> - <?= date('m/d/Y', $dateend) ?></div>
    <div class="editdataarea">
        <div class="editnotearea">
            <div class="title">Old Notes Format:</div>
            <textarea class="weeknoteedit"><?= $weeknote ?></textarea>
        </div>
        <div class="editderailarea">
            <div class="purchasedataarea">
                <div class="title">W9 Work:</div>
                <div class="tablehead">
                    <div class="deedcell">
                        <i class="fa fa-plus-circle" aria-hidden="true" id="addneww9workdetails"></i>
                    </div>
                    <div class="amount">Amount</div>
                    <div class="vendor">Vendor</div>
                    <div class="category">Category</div>
                    <div class="description">Description</div>
                </div>                
                <div class="tablebody" data-content='w9work'><?=$w9work_tableview ?></div>
                <div class="totaldata">
                    <div class="label">Total: </div>
                    <div class="value" id="w9workpopuptotalvalue"><?= MoneyOutput($profit_w9) ?></div>
                </div>
            </div>
            <div class="purchasedataarea">
                <div class="title">Purchases:</div>
                <div class="tablehead">
                    <div class="deedcell">
                        <i class="fa fa-plus-circle" aria-hidden="true" id="addnewpurchasedetails"></i>
                    </div>
                    <div class="amount">Amount</div>
                    <div class="vendor">Vendor</div>
                    <div class="category">Category</div>
                    <div class="description">Description</div>
                </div>                
                <div class="tablebody" data-content='purchase'><?= $putchase_tableview ?></div>
                <div class="totaldata">
                    <div class="label">Total: </div>
                    <div class="value" id="purchasepopuptotalvalue"><?= MoneyOutput($profit_purchases) ?></div>
                </div>
            </div>
            <div class="savedata" id="purchasepopupsavevalue">&nbsp;</div>
        </div>
    </div>
</div>