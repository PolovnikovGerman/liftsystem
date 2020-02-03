<div class="differences_area_title">
    <div class="label">
        Difference
    </div>
    <div class="yearselect">
        <div class="label">Compare:</div>
        <div class="dataselect">
            <select class="yearselect" data-type="<?=$type?>" data-year="compare" data-profit="<?=$profit_type?>">
                <?php foreach ($years_from as $row) { ?>
                    <option value="<?=$row?>" <?=$row==$year_from ? 'selected="selected"' : ''?>><?=$row?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="yearselect">
        <div class="label">To:</div>
        <div class="dataselect">
            <select class="yearselect" data-type="<?=$type?>" data-year="to" data-profit="<?=$profit_type?>">
                <?php foreach ($years_to as $row) { ?>
                    <option value="<?=$row?>" <?=$row==$year_to ? 'selected="selected"' : ''?>><?=$row?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="differences_map">
    <div class="label">Chg %</div>
    <div class="label">Rev</div>
</div>
<div class="montsquaterarea">
    <div class="monthsarea">
        <?php foreach ($months as $mrow) { ?>
            <div class="monthdataarea">
                <div class="month <?=$mrow['view_class']?>">
                    <div class="row <?=$mrow['chg_perc_class']?>"><?=$mrow['chg_perc']?></div>
                    <div class="row <?=$mrow['chg_revenue_class']?>"><?=$mrow['chg_revenue']?></div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="quaterarea">
        <?php foreach ($quarters as $qrow) { ?>
            <div class="quaterdatabefore <?=$qrow['view_class']?>">&nbsp;</div>
            <div class="quaterdata <?=$qrow['view_class']?>">
                <span class="quaterlabel" data-calcurl="<?=$qrow['calcurl']?>"><?=$qrow['label']?></span> <span class="<?=$qrow['chg_revenue_class']?>"><?=$qrow['chg_revenue']?></span> <span class="<?=$qrow['chg_perc_class']?>"><?=$qrow['chg_perc']?></span>
            </div>
            <div class="quaterdataafter <?=$qrow['view_class']?>">&nbsp;</div>
        <?php } ?>
    </div>
</div>
