<table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0">
    <?php if (count($item_dat)==0) {?>
        <tr class="white-stroka">
            <td class="bor1 text-1 cell-center">No records</td>
        </tr>
    <?php } else { ?>
        <?php $n_row=$offset+1;?>
        <?php foreach ($item_dat as $row) {?>
            <tr class="<?=($n_row%2==0 ? 'greydatarow' : 'whitedatarow')?>">
                <td style="width:30px;" class="bor-1 text-2 cell-center cell1"><?=$n_row;?></td>
                <td style="width:25px;" class="bor-1 cell-center cell2">
                    <a href="javascript:void(0)" id="<?=$row['item_id']?>" onclick="item_edit(this)">
                        <i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i>
                    </a>
                </td>
                <td style="width:60px;" class="bor-1 text-2 cell-center cell3"><?=$row['item_number']?></td>
                <td style="width:198px;" class="bor-1 text-2 cell-left cell4">
                    <div class="overflowtext" style="width:188px;padding-left: 5px; padding-right: 5px;height: 16px;">
                        <a href=javascript:void(0)" class="itemtitle <?=$row['itemnameclass']?>" title="<?=$row['item_name']?>"><?=$row['item_name']?></a>
                    </div>
                </td>
                <td style="width:163px;" class="bor-2 text-7 cell-left cell5">
                    <div style="float:left;padding-left: 3px">
                        <img id="il<?=$row['item_id']?>" src="/img/database/play-green.png" class="player"/>
                        <a href="javascript:void(0);" <?=($row['item_vector_img']=='' ? 'onclick="empty_vectorfile();"': 'onclick="openai(\''.$row['item_vector_img'].'\');"')?>>open in Illustrator</a>
                    </div>
                </td>
                <td style="width: 140px;" class="cell-center bor-2 aiupdatestatus <?=$row['update_template_class']?>"><?=$row['update_template']==0 ? 'No' : 'Yes'?></td>
                <td style="width: 346px;" class="cell-center bor-2 aiupdatestatus <?=$row['update_imprint_class']?>" data-item="<?=$row['item_id']?>">
                    <div class="imprint_update_label"><?=$row['imprint_update']==0 ? 'No' : 'Yes'?></div>
                    <div class="imprint_update_manage">
                        <input type="radio" class="updateimprintradio" name="updateimprint_<?=$row['item_id']?>" value="1" <?=$row['imprint_update']==0 ? 'disabled="disabled"' : ''?> <?=$row['imprint_update']==1 ? 'checked="checked"' : ''?> data-item="<?=$row['item_id']?>">
                        <label for="contactChoice1">Partial</label>
                        <input type="radio" class="updateimprintradio" name="updateimprint_<?=$row['item_id']?>" value="2" <?=$row['imprint_update']==0 ? 'disabled="disabled"' : ''?> <?=$row['imprint_update']==2 ? 'checked="checked"' : ''?> data-item="<?=$row['item_id']?>">
                        <label for="contactChoice2">Complete</label>
                    </div>
                </td>
            </tr>
            <?php $n_row++;?>
        <?php } ?>
    <?php } ?>
</table>
