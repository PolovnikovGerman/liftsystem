<div class="netprofit_weeknote_form">
    <form id="batchnoteedit">
        <input type="hidden" id="batch_id" name="batch_id" value="<?=$batch_id?>"/>        
        <div class="netprofitweeknote_title">Notes for batch from <?=date('m/d/Y',$batch_date)?> for order <?=$order_num?></div>
        <div class="netprofitweeknote_data">
            <textarea id="batch_note" name="batch_note" class="weeknote"><?=$batch_note?></textarea>
        </div>
        <div class="netprofitweeknote_bottom">
            <div class="savebatchnote">Save</div>
        </div>
        
    </form>
</div>
