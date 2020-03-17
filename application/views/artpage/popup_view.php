<div class="artpopup_content">
    <form id="artdetailsform">
        <!-- COMMON DATA -->
        <input type="hidden" id="proof_id" name="proof_id" value="<?=$proof_id?>"/>
        <input type="hidden" id="order_id" name="order_id" value="<?=$order_id?>"/>
        <input type="hidden" id="artwork_id" name="artwork_id" value="<?=$artwork_id?>"/>
        <input type="hidden" id="location_num" name="location_num" value="<?=$location_num?>"/>
        <input type="hidden" id="artstage" name="artstage" value="<?=$artstage?>"/>
        <!-- <input type="hidden" id="item_id" name="item_id" value="<?=$item_id?>"/> -->
        <input type="hidden" id="artsession" name="artsession" value="<?=$artsession?>"/>
        <?=$common_data?>
        <!-- NEXT -->
        <div class="artpopup_artarea">
            <div class="artpopup_usrmsg">
                <?=$artmsg_data?>
            </div>
            <div class="artpopup_locationsarea">
                <?=$artshead?>
                <div class="artpopup_locations">
                    &nbsp;
                    <!-- List of locations -->
                    <?php foreach ($locations_data as $loc) {?>
                        <?=$loc?>
                    <?php } ?>
                </div>
                <?=$addlocations?>
            </div>
            <div class="artpopup_artwork">
                <div class="artpopup_template">
                    <?=$templates_view?>
                </div>
                <div class="artpopup_proofs">
                    <?=$proofs_view?>
                </div>
                <div class="artpopup_approved">
                    <div id="approvedarea<?=$artwork_id?>"><?=$approved_view?></div>
                </div>
                <div class="artpopup_save"><img src="/img/artpage/artpopup_savebtn.png" alt="Save"/></div>
            </div>
        </div>
    </form>
</div>
<?=$parsedalert?>
<div id="approvemailarea" style="width: 365px; height: 472px; display: none;"></div>
<div id="assignorderarea" style="width: 705px; height: 390px; display: none;"></div>
<div id="imprint_area" style="width: 640px;height: 500px;display: none;"></div>
<div id="fontselectarea" style="width: 980px;height: 542px;display: none;"></div>