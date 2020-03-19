<div class="commonsinfotab">
    <input type="hidden" id="commonsession" value="<?=$session?>"/>
    <div class="commonsinfo">
        <?php foreach ($terms as $row) { ?>
            <div class="commonrow">
                <input type="text" class="inputcommondata" data-idx="<?=$row['term_id']?>" value="<?=$row['common_term']?>"/>
            </div>
        <?php } ?>
    </div>
    <div class="savecommonterms">
        <img src="/img/itemdetails/save_order.png" alt="Save"/>
    </div>
</div>