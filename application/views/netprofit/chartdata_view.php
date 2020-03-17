<input type="hidden" id="projincome" value="<?=$paceincome?>"/>
<input type="hidden" id="projexpence" value="<?=$paceexpense?>"/>
<div class="totalsarea">
    <div class="weektotalsheadrow">
        <select class="weektotalsviewtype">
            <option value="">Projected for Year</option>
            <option value="1">Compare to Portion of Year</option>
        </select>    
    </div>
    <div class="weekselectforcompare">
        <!-- <div>Display:</div> -->
        <select class="weekcompareselect" id="strweek">
            <?php foreach ($weeklist as $row) { ?>
            <option value="<?=$row['weeknum']?>">#<?=$row['weeknum']?> <?=$row['label']?></option>
            <?php } ?>
        </select>
        <select class="weekcompareselect" id="endweek">
            <?php foreach ($weeklist as $row) { ?>
            <option value="<?=$row['weeknum']?>" <?=$row['current']==1 ? 'selected="selected"' : ''?>>#<?=$row['weeknum']?> <?=$row['label']?></option>
            <?php } ?>
        </select>
    </div>
    
    <div class="baseperiodselectarea">
        <div class="periodtypeselect">
            <div class="label">Projected Income</div>
            <div class="periodtypeselectarea">
                <div class="datarow">
                    <div class="inputplace switchon" data-pace="income" data-proj="1">
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i>                        
                    </div>
                    <div class="inputlabel">Current Pace</div>
                </div>
                <div class="datarow">
                    <div class="inputplace" data-pace="income" data-proj="2">
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                    </div>
                    <div class="inputlabel">Last Year&apos;s Pace</div>
                </div>                
            </div>
        </div>
        <div class="periodtypeselect">
            <div class="label">Projected Expenses</div>
            <div class="periodtypeselectarea">
                <div class="datarow">
                    <div class="inputplace switchon" data-pace="expenses" data-proj="1">
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i>                        
                    </div>
                    <div class="inputlabel">Current Pace</div>
                </div>
                <div class="datarow">
                    <div class="inputplace" data-pace="expenses" data-proj="2">
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                    </div>
                    <div class="inputlabel">Last Year&apos;s Pace</div>
                </div>                
            </div>
        </div>
    </div>
    <!-- table -->
    <div class="weektotalsdataarea"><?=$table_view?></div>
</div>