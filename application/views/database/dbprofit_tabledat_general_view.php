<table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0">
    <?php if (count($item_dat)==0) { ?>
        <tr class="white-stroka">
            <td style="width:981px" class="bor-1 text-1 cell-center"><b>No records</b></td>
        </tr>
    <?php } else { ?>
        <?php $n_row=1+$offset;?>
        <?php foreach ($item_dat as $row) {?>
            <tr class="<?=($n_row%2==0 ? 'greydatarow' : 'whitedatarow')?>">
                <td class="bor-1 text-2 cell-center">
                    <div style="width:30px;"><?=$n_row?></div>
                </td>
                <td style="width:28px" class="bor-1 cell-center">
                    <a href="javascript:void(0)" id="<?=$row['item_id']?>" onclick="item_edit(this);">
                        <i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i>
                    </a>
                </td>
                <td style="width:60px;" class="bor-1 text-2 cell-center"><?=$row['item_number']?></td>
                <td class="bor-1 text-2 cell-left">
                    <div class="overflowtext" style="width:627px;padding-left: 5px; padding-right: 5px;height: 16px;">
                        <a href=javascript:void(0)" class="itemtitle <?=$row['itemnameclass']?>" title="<?=$row['item_name']?>"><?=$row['item_name']?></a>
                    </div>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center"><?=($row['vendor_item_cost']==''? '' : '$'.$row['vendor_item_cost'])?></td>
                <td style="width:166px;" class="bor-1 text-2 cell-left"><?=$row['vendor_name']?></td>
            </tr>
            <?php $n_row++;?>
        <?php } ?>
    <?php } ?>
</table>
