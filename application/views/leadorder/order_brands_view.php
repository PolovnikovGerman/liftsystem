<div class="order_brandselectarea">
    <div class="brandselectarea">
        <label>Brand</label>
        <select class="brandselectvalue" id="neworderbrand">
            <?php foreach ($brands as $row) { ?>
                <option value="<?=$row['brand']?>"><?=$row['label']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="brandselectsave">
        <button class="btn btn-primary" id="savebrand">Choice</button>
    </div>
</div>