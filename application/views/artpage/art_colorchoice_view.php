<div class="imprintcolors" >
    <div class="imprintcolors_head">
        <input type="hidden" id="userchkcolor" value=""/>
        <h1>Please select an imprint color</h1>
    </div>
    <div class="clear"></div>
    <div class="imprintcolors_table">
        <table class="table-imprintcolors">
            <tr>
                <?php $rowcnt = 0;$numcolors = count($colors);$cnt_color = 1; ?>
                <?php foreach ($colors as $row) { ?>
                <td class="colordat">
                    <div class="radiselect">
                        <input type="radio" class="colorradio" id="color<?=substr($row['code'], 1)?>" name="imcolor" value="<?=substr($row['code'], 1) ?>"/>
                    </div>
                    <div class="colorshow" style="background-color: <?= $row['code'] ?>;">
                        &nbsp;
                    </div>
                    <div class="colorname"><?= $row['name'] ?></div>
                </td>
                <?php $rowcnt++;
                $cnt_color++; ?>
                <?php if ($rowcnt == 3 && ($cnt_color < $numcolors)) { ?>
            </tr>
            <tr>
                <?php $rowcnt = 0;
                } ?>
                <?php } ?>
            </tr>
        </table>
    </div>
    <div class="imprintcolors_table">
        <table class="table-imprintcolors">
            <tr>
                <td class="colordat">&nbsp;</td>
                <td class="colordat">&nbsp;</td>
                <td class="colordat">
                    <div class="select_color">
                        <a href="javascript:void(0)" disabled="disabled" id="select_color">select color</a>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
