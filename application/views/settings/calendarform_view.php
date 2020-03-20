<div class="calendform">
    <input type="hidden" id="editcalendsession_id" value="<?= $session_id ?>"/>
    <div class="tab3">
        <div class="block-1">
            <div class="calendar">
                <div class="calendar-name">Calendar Name:</div>
                <div class="calendar-name-input">
                    <input id="calendar_name" type="text" value="<?= $calendar['calendar_name'] ?>">
                </div>
            </div>
            <div class="text-days">
                <p class="text">The following are business days:</p>
            </div>
            <div class="list-days">
                <input class="busday" type="checkbox" name="sun_work" id="sun_work"
                       value="1" <?= ($calendar['sun_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_sun_work"
                      class="<?= ($calendar['sun_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Sundays</span><br/>
                <input class="busday" type="checkbox" name="mon_work" id="mon_work"
                       value="1" <?= ($calendar['mon_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_mon_work"
                      class="<?= ($calendar['mon_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Mondays</span><br/>
                <input class="busday" type="checkbox" name="tue_work" id="tue_work"
                       value="1" <?= ($calendar['tue_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_tue_work"
                      class="<?= ($calendar['tue_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Tuesdays</span><br/>
                <input class="busday" type="checkbox" name="wed_work" id="wed_work"
                       value="1" <?= ($calendar['wed_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_wed_work"
                      class="<?= ($calendar['wed_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Wednesdays</span><br/>
                <input class="busday" type="checkbox" name="thu_work" id="thu_work"
                       value="1" <?= ($calendar['thu_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_thu_work"
                      class="<?= ($calendar['thu_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Thursdays</span><br/>
                <input class="busday" type="checkbox" name="fri_work" id="fri_work"
                       value="1" <?= ($calendar['fri_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_fri_work"
                      class="<?= ($calendar['fri_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Fridays</span><br/>
                <input class="busday" type="checkbox" name="sat_work" id="sat_work"
                       value="1" <?= ($calendar['sat_work'] == 1 ? 'checked="checked"' : '') ?> />
                <span id="name_sat_work"
                      class="<?= ($calendar['sat_work'] == 1 ? 'active_bisday' : 'holiday_bisday') ?>">Saturdays</span><br/>
            </div>
            <div class="calendar_yearstatarea">
                <div class="yearstatrow">
                    Total <?= $year ?> Business Days: <strong><?= $total_days ?></strong> days
                </div>
                <div class="yearstatrow">
                    Elapsed <?= $year ?> Business Days: <strong><?= $elaps_days ?></strong> days (<?= $elaps_proc ?>)
                </div>
                <div class="yearstatrow">
                    Remaining <?= $year ?> Business Days: <strong><?= $remin_days ?></strong> days (<?= $remin_proc ?>)
                </div>
            </div>
        </div>
        <div class="block-2">
            <div class="text-holidays">
                <p class="text">EXCEPT these holidays:</p>
            </div>
            <div class="add-date">
                <input type="hidden" id="d_bgn" value=""/>
                <a id="f_btn" href="javascript:void(0);"><img src="/img/others/add-date.png"/></a>
            </div>
            <div class="list-holidays" id="holidayslist">
                <?= $holidaylist ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="line"><img src="/img/others/line.png"/></div>
        <div class="block">
                <span style="float: left;">
                    Calendar status
                    <select name="calendar_status" id="calendar_status">
                        <option value="1" <?= ($calendar['calendar_status'] == 1 ? 'selected="selected"' : '') ?>>Active</option>
                        <option value="0" <?= ($calendar['calendar_status'] == 0 ? 'selected="selected"' : '') ?>>Paused</option>
                    </select>

                </span>
            <div class="savecalendar"><a class="savecalend" id="save" href="javascript:void(0);">Save</a></div>
        </div>
        <div class="clearfix"></div>
        <div class="line"><img src="/img/others/line.png"/></div>
        <div class="clearfix"></div>
        <div class="block">
        </div>
    </div>
</div>