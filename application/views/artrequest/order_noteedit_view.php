<div class="artnote_form">
    <form id="ordernoteedit">
        <input type="hidden" id="order_id" name="order_id" value="<?=$order_id?>"/>
        <!-- for order <?php // $order_num?> -->
        <!-- <div class="artnote_title">Art note for <?php // $title?></div> -->
        <div class="artnote_data">
            <textarea id="art_note" name="art_note" class="artnote"><?=$art_note?></textarea>
        </div>
        <div class="artnote_bottom">
            <div class="btn btn-primary saveordernote">Save</div>
        </div>
    </form>
</div>
