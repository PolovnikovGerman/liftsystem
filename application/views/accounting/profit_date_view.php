<div class="profitdates-content">
    <input type="hidden" id="maxyear" value="<?= $max_year ?>"/>
    <input type="hidden" id="cur_year" value="<?= $cur_year ?>"/>
    <input type="hidden" id="cur_month" value="<?= $cur_month ?>"/>
    <input type="hidden" id="showgrowth" value="<?= $showgrowth ?>"/>
    <div class="profitdate-control">
        <div class="profitdate-bookmark">
            <?php $i = $max_year; ?>
            <div class="tab1-cont" id="year<?= $i ?>">
                <div class="style-text1"><?= $i ?></div>
            </div>
            <?php $i--; ?>
            <?php while ($i >= $min_year) { ?>
                <div class="tab2-cont" id="year<?= $i ?>">
                    <div class="style-text2-nonactive"><?= $i ?></div>
                </div>
                <?php $i--; ?>
            <?php } ?>
        </div>
    </div>
    <div class="caledardatas">
        <div class="legenddatearea">
            <div class="legend">
                <?= $legend ?>
            </div>
        </div>
        <div class="profitdate_months">
            <?= $year_links ?>
        </div>
        <div class="profitdate-info">
            <div class="profitdate-info-note">
                Top Figure: Profit<br>
                Bottom: Revenue
            </div>
            <div class="profitdate-selected-month">
                <div class="profitdate-selected-monthname">
                    <?= $month_name ?> <?= $max_year ?>
                </div>
            </div>
            <div class="profitdate-totalmonth" id="totalsbymonth">
                &nbsp;
            </div>
        </div>
        <div class="table-calendar">
            <div class="table-calendar-title">
                <div class="table-calendar-titleedge">
                    <img src="/img/accounting/background-calendar-title-bgn.png"/>
                </div>
                <div class="calend-title-monday businessday">Monday</div>
                <div class="calend-title-other businessday">Tuesday</div>
                <div class="calend-title-other businessday">Wednesday</div>
                <div class="calend-title-other businessday">Thursday</div>
                <div class="calend-title-other businessday">Friday</div>
                <div class="calend-title-other weekend">Saturday</div>
                <div class="calend-title-other weekend">Sunday</div>
                <div class="calend-title-weektotal businessday">Total for Week*</div>
                <div class="table-calendar-titleedge">
                    <img src="/img/accounting/background-calendar-title-end.png"/>
                </div>
            </div>
            <div class="calendar-info" id="tableinfotab2">

            </div>
        </div>
        <div class="showhidegrowth"><?= $showhidegrowth ?></div>
        <div class="finance_date_filter">
            <span>Display:</span>
            <select class="filter profitstatview">
                <option value="0" selected="selected">Projected for Year</option>
                <option value="1">Compare to Portion of Year</option>
            </select>
        </div>
        <div class="finance_month_filter">
            <select class="startdate filter">
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select class="enddate filter">
                <option value="01" <?= $montfilterend == '01' ? 'selected="selected"' : '' ?>>January</option>
                <option value="02" <?= $montfilterend == '02' ? 'selected="selected"' : '' ?>>February</option>
                <option value="03" <?= $montfilterend == '03' ? 'selected="selected"' : '' ?>>March</option>
                <option value="04" <?= $montfilterend == '04' ? 'selected="selected"' : '' ?>>April</option>
                <option value="05" <?= $montfilterend == '05' ? 'selected="selected"' : '' ?>>May</option>
                <option value="06" <?= $montfilterend == '06' ? 'selected="selected"' : '' ?>>June</option>
                <option value="07" <?= $montfilterend == '07' ? 'selected="selected"' : '' ?>>July</option>
                <option value="08" <?= $montfilterend == '08' ? 'selected="selected"' : '' ?>>August</option>
                <option value="09" <?= $montfilterend == '09' ? 'selected="selected"' : '' ?>>September</option>
                <option value="10" <?= $montfilterend == '10' ? 'selected="selected"' : '' ?>>October</option>
                <option value="11" <?= $montfilterend == '11' ? 'selected="selected"' : '' ?>>November</option>
                <option value="12" <?= $montfilterend == '12' ? 'selected="selected"' : '' ?>>December</option>
            </select>
        </div>

        <div class="profitdatatotalsarea">
            <div class="profitcalend_slidermanage left <?= ($start_margin == 0 ? '' : 'active') ?>">&nbsp;</div>
            <div class="line-week-results" id="weekdays-totals">
                <div class="profitdatatotalarea" id="profitdatatotalarea" style="width: <?= $slider_width ?>px; margin-left:<?= $start_margin ?>px;">
                    <?= $yearsview ?>
                </div>
            </div>
            <div class="profitcalend_slidermanage right">&nbsp;</div>
        </div>
    </div>
</div>
<div id="profitdategoaledit" style="display: none; width: 420px; height: 280px;"></div>
<input type="hidden" id="profitcalendarbrand" value="<?= $brand ?>">
<div id="profitcalendarbrandmenu">
    <?= $top_menu ?>
</div>
