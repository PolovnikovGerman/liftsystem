<div class="page_container">
    <input type="hidden" value="<?=$brand?>" id="shippingsviewbrand"/>
    <div class="left_maincontent" id="shippingsviewbrandmenu">
        <?=$left_menu?>
    </div>
    <div class="right_maincontent">
        <div class="dbcontent">
            <div class="othershiparea">
                <form id="shipzones">
                    <div class="shippping-service-title">Shipping Services:</div>
                    <div id="shipzonesdata"></div>
                </form>
                <div class="activate_btn" id="activate">
                    <div class="activate-text">Activate Editing</div>
                </div>
            </div>
            <div class='shipcalclogarea'>
                <div class='shipcalclogtitle'>
                    <span>Ship Calculator Report</span>
                    <div class="shipcalclogperiod">
                        <select class='shiplogmonth' id='shiplogmonth'>
                            <?php foreach ($months as $row) { ?>
                                <option value='<?=$row['id']?>' <?=($row['id']==$curmonth ? 'selected="selected"' : '')?>><?=$row['name']?></option>
                            <?php } ?>
                        </select>
                        <select class='shiplogyear' id='shiplogyear'>
                            <?php foreach ($years as $row) { ?>
                                <option value='<?=$row['id']?>' <?=($row['id']==$curyear ? 'selected="selected"' : '')?>><?=$row['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class='shipcalclogcalend'></div>
            </div>
        </div>
    </div>
</div>

