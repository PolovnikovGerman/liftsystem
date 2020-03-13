<div class="vendorcost">
    <select class="selectrepsoldcalcyear">
        <option value="<?=$start_year?>" <?=($start_year==$current_year ? 'selected="selected"' : '')?>>Show <?=$start_year?> - <?=$start_year-1?></option>
        <option value="<?=$start_year-1?>" <?=($start_year-1==$current_year ? 'selected="selected"' : '')?>>Show <?=$start_year-1?> - <?=$start_year-2?></option>
    </select>
</div>
