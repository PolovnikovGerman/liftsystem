<div class="profitorder-content">
    <input type="hidden" id="orderbytab1" value="<?=$order?>"/>
    <input type="hidden" id="directiontab1" value="<?=$direc?>"/>
    <input type="hidden" id="totaltab1" value="<?=$total?>"/>
    <input type="hidden" id="curpagetab1" value="<?=$curpage;?>"/>
    <div class="profitorder-head-row">
        <div class="legend">
            <?=$legend?>
        </div>
        <div>
            <select id="order_filtr" name="order_filtr" class="order_filtr_select">
                <option value="0">Display All</option>
                <option value="1">Display Projected Only</option>
                <option value="2">Green Profit Only</option>
                <option value="3">White Profit Only</option>
                <option value="4">Orange Profit Only</option>
                <option value="5">Red Profit Only</option>
                <option value="8">Maroon Profit Only</option>
                <option value="6">Black Profit Only</option>
                <option value="7">Canceled Orders Only</option>
            </select>
        </div>
        <div><?=$perpage_view?></div>
    </div>
</div>
<input type="hidden" id="salestypereportbrand" value="<?=$brand?>">
<?=$top_menu?>

