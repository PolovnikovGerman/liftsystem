<?php $n_row=0;?>
<?php foreach ($templates as $row) { ?>
    <div class="templatesdata_row <?=($n_row % 2 == 0 ? 'greydatarow' : 'whitedatarow') ?>">
        <div class="template_data_itemnum"><?= $row['item_number'] ?></div>
        <div class="template_data_itemname"><?= $row['item_name'] ?></div>
        <div class="template_data_itemfile">
            <div style="float:left;padding-left: 3px">
                <!-- <img id="il<?php // $row['item_id'] ?>" src="/img/play-green.png" class="player"/> -->
                <i class="fa fa-play templateplayer" aria-hidden="true"></i>
                <a href="javascript:void(0);" <?= ($row['item_vector_img'] == '' ? 'onclick="empty_vectorfile();"' : 'onclick="openai(\'' . $row['item_vector_img'] . '\',\''.$row['item_name'].'\');"') ?>>open in Illustrator</a>
            </div>
        </div>
    </div>
    <?php $n_row++; ?>
<?php } ?>
