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
                    <div class="overflowtext" style="width:180px;padding-left: 5px; padding-right: 5px;height: 16px;">
                        <a href=javascript:void(0)" class="itemtitle <?=$row['itemnameclass']?>" title="<?=$row['item_name']?>"><?=$row['item_name']?></a>
                    </div>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center"><?=($row['vendor_item_cost']==''? '' : '$'.$row['vendor_item_cost'])?></td>
                <td style="width:45px;" class="bor-1 text-2 cell-left"><?=$row['vendor_name']?></td>
                <td style="width:4px;" class="grey">&nbsp;</td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_25_class']?>">
                    <?=($row['profit_25_class']=='empty' ? 'n/a' : $row['profit_25'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_75_class']?>">
                    <?=($row['profit_75_class']=='empty' ? 'n/a' : $row['profit_75'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_150_class']?>">
                    <?=($row['profit_150_class']=='empty' ? 'n/a' : $row['profit_150'].' %')?>
                </td>
                <td style="width:46px;" class="bor-1 text-2 cell-center <?=$row['profit_250_class']?>">
                    <?=($row['profit_250_class']=='empty' ? 'n/a' : $row['profit_250'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_500_class']?>">
                    <?=($row['profit_500_class']=='empty' ? 'n/a' : $row['profit_500'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_1000_class']?>">
                    <?=($row['profit_1000_class']=='empty' ? 'n/a' : $row['profit_1000'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_2500_class']?>">
                    <?=($row['profit_2500_class']=='empty' ? 'n/a' : $row['profit_2500'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_5000_class']?>">
                    <?=($row['profit_5000_class']=='empty' ? 'n/a' : $row['profit_5000'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_10000_class']?>">
                    <?=($row['profit_10000_class']=='empty' ? 'n/a' : $row['profit_10000'].' %')?>
                </td>
                <td style="width:47px;" class="bor-1 text-2 cell-center <?=$row['profit_20000_class']?>">
                    <?=($row['profit_20000_class']=='empty' ? 'n/a' : $row['profit_20000'].' %')?>
                </td>
                <td style="width:4px" class="grey">&nbsp;</td>
                <td style="width:43px" class="bor-1 text-2 cell-center <?=$row['profit_print_class']?>">
                    <?=($row['profit_print_class']=='empty' ? 'n/a' : $row['profit_print'].' %')?>
                </td>
                <td style="width:44px" class="bor-1 bor-2 text-2 cell-center last_col <?=$row['profit_setup_class']?>">
                    <?=($row['profit_setup_class']=='empty' ? 'n/a' : $row['profit_setup'].' %')?>
                </td>
            </tr>
            <?php $n_row++;?>
        <?php } ?>
    <?php } ?>
</table>
