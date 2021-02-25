<div class="page_container">
    <div class="right_maincontent">
        <div class="dbcontent">
            <div class="othershiparea">
                <form class="shipzones" data-brand="<?=$brand?>">
                    <div class="shippping-service-title">Shipping Services:</div>
                    <div class="shipzonesdata" id="shipzonesdata<?=$brand?>" data-brand="<?=$brand?>"></div>
                </form>
                <div class="activate_btn shipkoefmanage" data-brand="<?=$brand?>">
                    <div class="activate-text">Activate Editing</div>
                </div>
            </div>
            <div class='shipcalclogarea'>
                <div class='shipcalclogtitle'>
                    <span>Ship Calculator Report</span>
                    <div class="shipcalclogperiod">
                        <select class='shiplogmonth' data-brand="<?=$brand?>">
                            <?php foreach ($months as $row) { ?>
                                <option value='<?=$row['id']?>' <?=($row['id']==$curmonth ? 'selected="selected"' : '')?>><?=$row['name']?></option>
                            <?php } ?>
                        </select>
                        <select class='shiplogyear' data-brand="<?=$brand?>">
                            <?php foreach ($years as $row) { ?>
                                <option value='<?=$row['id']?>' <?=($row['id']==$curyear ? 'selected="selected"' : '')?>><?=$row['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class='shipcalclogcalend' data-brand="<?=$brand?>"></div>
            </div>
        </div>
    </div>
</div>

