<div class="quotecontentarea">
    <div class="datarow">
        <div class="quotetemplatetitle">Template:</div>
        <div class="quotetemplateinpt">
            <select data-entity="quotedat" data-item="quote_template">
                <?php foreach ($templlists as $templlist) { ?>
                    <option value="<?=$templlist?>" <?=$templlists==$data['quote_template'] ? 'selected="selected"' : ''?>><?=$templlist?></option>
                <?php } ?>
            </select>
        </div>
        <div class="quoteactionsarea">
            <div class="quoteactionaddorder">start order</div>
            <div class="quoteactionduplicate">duplicate</div>
            <div class="quoteactionsend">send</div>
            <div class="quoteactionpdfdoc">pdf</div>
        </div>
        <div class="quotesaveedit">

        </div>
    </div>
</div>