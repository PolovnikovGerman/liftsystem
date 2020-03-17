<div class="batchcontent">
    <input type="hidden" id="batchcurrent" value=""/>
    <div class="batch_details">
        <div class="batchfilter">
            <select name="batchfilter" id="batchfilter" class="batchviewselect">                
                <option value="0" selected="selected">View Only Not Received</option>
                <option value="">View Received & Not Received</option>
            </select>
            <select id="batchview_year" class="batchviewyearselect">
                <?php foreach ($years as $row) { ?>
                <option value="<?=$row['year']?>"><?=$row['year']?></option>
                <?php } ?>
            </select>
        </div>
        <div id="batchdetailsview" class="batchdaydata_content">
            <?=$details?>
        </div>
    </div>
    <div class="batchcalendar">
        <?=$calendar?>
    </div>
</div>
<input type="hidden" id="finbatchesbrand" value="<?=$brand?>">
<div id="finbatchesbrandmenu">
    <?=$top_menu?>
</div>
