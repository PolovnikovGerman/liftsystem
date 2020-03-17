<div class="netprofit_weeknote_form">
    <form id="netprofitweeknote">
        <input type="hidden" id="profit_id" name="profit_id" value="<?=$profit_id?>"/>
        <div class="netprofitweeknote_title">Notes for week <?=date('m/d/Y',$datebgn)?> <?=date('m/d/Y',$dateend)?></div>
        <div class="netprofitweeknote_data">
            <textarea id="weeknote" name="weeknote" class="weeknote"><?=$weeknote?></textarea>
        </div>
        <div class="netprofitweeknote_bottom">
            <div class="saveweeknote">Save</div>
        </div>
        
    </form>
</div>
