    <div class="invarriv">
        <?php foreach ($data as $arr) { ?>
            <?php if($arr['onboat_date'] < time()) { ?>
                <div class="arriving_inventory arrived_data">arriving</div>
            <?php } else { ?>
                <div class="arriving_inventory">arriving</div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="arrived_data_tot">
        <?php foreach($data as $row) { ?>
            <?php if($row['onboat_date'] < time()) { ?>
                <div class="arrived_data"><?= date("m/d/Y", $row['onboat_date']) ?></div>
            <?php } else { ?>
                <div class="arriving_data"><?= date("m/d/Y", $row['onboat_date']) ?></div>
            <?php } ?>
        <?php } ?>
        <div class="add_boat">
            <input type="text" class="arriving_data" id="onboatdate" value="<?= date("m/d/Y", time()) ?>">
        </div>
    </div>