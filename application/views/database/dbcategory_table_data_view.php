<table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0">
    <?php if (count($item_dat)==0) { ?>
        <tr class="white-stroka">
            <td colspan="12" class="text-1">No records</td>
        </tr>
    <?php } else { ?>
        <?php $n_row=$offset+1;?>
        <?php foreach ($item_dat as $row) {?>
            <tr class="<?=($n_row%2==0 ? 'grey-stroka' : 'white-stroka')?>">
                <td class="bor-1 text-2 cell-center">
                    <div style="width:30px;"><?=$n_row?></div>
                </td>
                <td class="bor-1 cell-center">
                    <div style="width:25px;">
                        <a href="javascript:void(0)" id="<?=$row['item_id']?>" onclick="item_edit(this);">
                            <i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i>
                        </a>
                    </div>
                </td>
                <td class="bor-1 text-2 cell-center">
                    <div style="width:60px;"><?=$row['item_number']?></div>
                </td>
                <td class="bor-1 text-2 cell-left">
                    <div class="overflowtext" style="width:191px;padding-left: 5px;padding-right: 3px; height: 16px;">
                        <a href=javascript:void(0)" class="itemtitle <?=$row['itemnameclass']?>" title="<?=$row['item_name']?>"><?=$row['item_name']?></a>
                    </div>
                </td>
                <td class="bor-1 text-2">
                    <div style="width:26px;">&nbsp;</div>
                </td>
                <td class="bor-1 text-2">
                    <div style="width:26px;">&nbsp;</div>
                </td>
                <?php $i=1;?>
                <?php foreach ($row['categories'] as $cat) {?>
                    <td class="bor-1 text-2 <?=($i==6 ? 'last_col' : '')?>">
                        <div style="width: 100px;" id="cell<?=$row['item_id'].'_'.$i?>">
                            <select id="<?=($cat['recid']=='' ? 'c'.$row['item_id'].'_'.$i : 'ic'.$cat['recid'].'_'.$row['item_id'].'_'.$i)?>"
                                    style="width: 99px;font-size: 10px;" <?=($pagelock==1 ? 'disabled' : '')?> <?=($cat['category']!='' ? 'class="category_exist"' : 'class="category_empty"')?> onchange="change_category(this);" >
                                <option value="0">--select--</option>
                                <?php foreach ($categ_list as $list) {?>
                                    <option value="<?=$list['category_id']?>" <?=($cat['category']==$list['category_id'] ? 'selected="selected"' : '')?>>
                                        <?=$list['category_name']?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <?php $i++;?>
                <?php } ?>
            </tr>
            <?php $n_row++;?>
        <?php } ?>
    <?php } ?>
</table>
