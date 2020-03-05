<div class="exportfields_select_content">
    <div class="title">Select Fields for Export</div>
    <form id="exportfields">

    <div class="dataarea">
        <?php foreach ($fields as $row) { ?>
            <div class="datarow">
                <div class="chkinpt">
                    <input type="checkbox" name="<?=$row['field']?>" value="1"/>
                </div>
                <div class="label"><?=$row['label']?></div>
            </div>
        <?php } ?>
    </div>
    </form>
    <div class="startexport">
        <a class="button exportfldbut" id="exportflds">Export</a>
    </div>
</div>