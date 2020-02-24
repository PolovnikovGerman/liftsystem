<select name="order_num"  class="order_numselect" id="order_num">
    <option value="">Select</option>
    <?php foreach ($orders as $row) {?>
        <option value="<?=$row['order_id']?>"><?=$row['order_num']?></option>
    <?php } ?>
</select>