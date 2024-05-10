<input type="hidden" id="searchesbrand" value="<?=$brand?>"/>
<input type="hidden" id="searcheswordpage" value="0"/>
<input type="hidden" id="searchesippage" value="0"/>
<input type="hidden" id="searchdayyear" value="<?=date('Y')?>"/>
<input type="hidden" id="searchdayyearmin" value="<?=$minyear?>"/>
<input type="hidden" id="searchdayyearmax" value="<?=$maxyear?>"/>
<input type="hidden" id="searcheswordtotal" value="0"/>
<input type="hidden" id="searchesiptotal" value="0"/>
<div class="searches_content">
    <div class="searches_displayoptions datarow">
        <div class="displayoptiontitle">Display Options:</div>
        <div class="displayoptionarea active" data-option="All">
            <input type="radio" name="searchdisplatradio" id="searchdisplayall" value="0" checked="checked"/> All
        </div>
        <div class="displayoptionarea" data-option="Positiv">
            <input type="radio" name="searchdisplatradio" id="searchdisplaypositiv" value="1"/> With Results Only
        </div>
        <div class="displayoptionarea" data-option="Negativ">
            <input type="radio" name="searchdisplatradio" id="searchdisplaynegativ" value="2"/> With NO Results Only
        </div>
    </div>
    <div class="searches_displayperiod datarow">
        <div class="displayperiodtitle">Display Dates:</div>
        <div class="displayperiodarea" data-period="day"><input type="radio" name="searchperiodradio" id="searchtoday" value="today"/> Today</div>
        <div class="displayperiodarea" data-period="week"><input type="radio" name="searchperiodradio" id="searchweek" value="week"/> This Week</div>
        <div class="displayperiodarea active" data-period="month">
            <input type="radio" name="searchperiodradio" id="searchmonth" value="month" checked="checked"/> Month
            <select class="searchmonthsselect">
                <?php foreach ($months as $month) { ?>
                    <option value="<?=$month['key']?>"><?=$month['val']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="displayperiodarea" data-period="year">
            <input type="radio" name="searchperiodradio" id="searchyear" value="year"/> Year
            <select class="searchyearselect" disabled="disabled">
                <?php for($i=$maxyear; $i>=$minyear; $i--) { ?>
                    <option value="<?=$i?>"><?=$i?></option>
                <?php } ?>
            </select>
        </div>
        <div class="searches_customdates">
            <input type="radio" name="searchperiodradio" id="searchcustom" value="custom"/> Custom Range
            <input type="text" class="customdateinpt" id="custom_bgn" disabled="disabled"/>
            <span>to</span>
            <input type="text" class="customdateinpt" id="custom_end" disabled="disabled"/>
        </div>
        <div class="displaycustomresult">Show</div>
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
    <div class="searchleftcontent">
        <div class="searchesipaddresshead <?=$brand=='SR' ? 'stressrelievers' : ''?>">
            <div class="title">Searches by IP Address:</div>
            <div class="ipaddresspaginator">
                <div class="navigateprev"><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
                <div class="navigatelabel"></div>
                <div class="navigatenext"><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="searchesipaddressdata">&nbsp;</div>
    </div>
    <div class="serchrightcontent">
        <div class="searchesdailyhead <?=$brand=='SR' ? 'stressrelievers' : ''?>">
            <div class="title">Number Searches by Day:</div>
            <div class="dailysearchpaginator">
                <div class="navigateprev"><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
                <div class="navigatelabel"><?=date('Y')?></div>
                <div class="navigatenext"><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
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
</div>