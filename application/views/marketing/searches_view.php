<input type="hidden" id="searchesbrand" value="<?=$brand?>"/>
<input type="hidden" id="searcheswordpage" value="0"/>
<input type="hidden" id="searchesippage" value="0"/>
<input type="hidden" id="searchdayyear" value="<?=date('Y')?>"/>
<input type="hidden" id="searchdayyearmin" value="<?=$minyear?>"/>
<input type="hidden" id="searchdayyearmax" value="<?=$maxyear?>"/>
<div class="searches_content">
    <div class="searches_displayoptions datarow">
        <span class="active"><input type="radio" name="searchdisplatradio" id="searchdisplayall" value="0" checked="checked"/> All</span>
        <input type="radio" name="searchdisplatradio" id="searchdisplaypositiv" value="1"/><span class="searchpositview">With Results Only</span>
        <input type="radio" name="searchdisplatradio" id="searchdisplaynegativ" value="2"/><span class="searchnegativview">With NO Results Only</span>
    </div>
    <div class="searches_displayperiod datarow">
        <input type="radio" name="searchperiodradio" id="searchtoday" value="today" checked="checked"/> Today
        <input type="radio" name="searchperiodradio" id="searchweek" value="week"/> This Week
        <input type="radio" name="searchperiodradio" id="searchmonth"/> Month
        <input type="radio" name="searchperiodradio" id="searchyear"/>
        <select class="searchyearselect" disabled="disabled">
            <?php for($i=$maxyear; $i<=$minyear; $i--) { ?>
                <option value="<?=$i?>"><?=$i?></option>
            <?php } ?>
        </select>
        <div class="searches_customdates">
            <input type="radio" name="searchperiodradio" id="searchcustom"/> Custom Range
            <input type="text" id="custom_bgn" disabled="disabled"/>
            <span>to</span>
            <input type="text" id="custom_end" disabled="disabled"/>
        </div>
    </div>
    <div class="searcheskeywordshead <?=$brand=='SR' ? 'stressrelievers' : ''?>">
        <div class="title">Searches by Phrase / Keyword:</div>
        <div class="keywordspaginator">
            <div class="navigateprev"><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
            <div class="navigatelabel"></div>
            <div class="navigatenext"><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
        </div>
    </div>
    <div class="searcheskeywordsdata">&nbsp;</div>
    <div class="searchesipaddresshead <?=$brand=='SR' ? 'stressrelievers' : ''?>">
        <div class="title">Searches by IP Address:</div>
        <div class="ipaddresspaginator">
            <div class="navigateprev"><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
            <div class="navigatelabel"></div>
            <div class="navigatenext"><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
        </div>
    </div>
    <div class="searchesipaddressdata">&nbsp;</div>
    <div class="searchesdailyhead <?=$brand=='SR' ? 'stressrelievers' : ''?>">
        <div class="title">Number Searches by Day:</div>
        <div class="dailysearchpaginator">
            <div class="navigateprev"><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
            <div class="navigatelabel"><?=date('Y')?></div>
            <div class="navigatenext active"><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
        </div>
    </div>
    <div class="searchesdailysubhead">
        <div class="weeknum">Week</div>
        <div class="weekday">Mon</div>
        <div class="weekday">Tue</div>
        <div class="weekday">Wed</div>
        <div class="weekday">Thu</div>
        <div class="weekday">Fri</div>
        <div class="weekday">Sat</div>
        <div class="weekday">Sun</div>
    </div>
    <div class="searchesdailydata">&nbsp;</div>
</div>