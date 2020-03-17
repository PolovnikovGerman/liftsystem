<div class="exportfields_select_content">
    <form id="exportfields">

    <div class="dataarea">
        <?php foreach ($fields as $row) { ?>
            <div class="datarow">
                <div class="chkinpt">
                    <input type="checkbox" name="<?=$row['field']?>" value="1"/>
                </div>
                <div class="labeltxt"><?=$row['label']?></div>
            </div>
        <?php } ?>
    </div>
    </form>
    <div class="startexport">
        <button class="btn btn-primary exportfldbut" id="exportflds">Export</button>
    </div>
</div>