<table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0">
    <?php if (count($item_dat)==0) { ?>
        <tr class="white-stroka">
            <td style="width:981px" class="bor-1 text-1 cell-center"><b>No records</b></td>
        </tr>
    <?php } else { ?>
        <?php $n_row=$offset+1;?>
        <?php foreach ($item_dat as $row) {?>
            <tr class="<?=($n_row%2==0 ? 'greydatarow' : 'whitedatarow')?>">
                <td  class="bor-1 text-2">
                    <div style="width:30px;"><?=$n_row?></div>
                </td>
                <td  class="bor-1">
                    <div style="width:26px;">
                        <a href="javascript:void(0)" id="<?=$row['item_id']?>" onclick="item_edit(this);">
                            <i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i>
                        </a>
                    </div>
                </td>
                <td  class="bor-1 text-2">
                    <div style="width:61px;" >
                        <?=$row['item_number']?>
                    </div>

                </td>
                <td  class="bor-1 text-2" align="left">
                    <div class="overflowtext" style="width:188px;padding-left: 5px; padding-right: 5px;height: 16px;">
                        <a href=javascript:void(0)" class="itemtitle <?=$row['itemnameclass']?>" data-content="<?=$row['item_name']?>"><?=$row['item_name']?></a>
                    </div>
                </td>
                <td  class="bor-2 bor-1 text-2 last_col">
                    <div style="width:652px;">
                        <?php $ind_miss=0; ?>
                        <?php foreach ($row['missings'] as $miss) {?>
                            <div class="misinfo_dat"><?=$miss['type']?></div>
                            <?php $ind_miss++;?>
                            <?php if ($ind_miss==10) {?>
                                <?php $less=count($row['missings']);$less=$less-$ind_miss?>
                                <div style="float:left">+ <?=$less;?> more</div>
                                <?php break;} ?>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php $n_row++;?>
        <?php } ?>
    <?php } ?>
</table>
