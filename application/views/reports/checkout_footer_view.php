<table cellspacing="0" cellpadding="0">
    <tr class="table-reports-text1">
        <td width="110" >&nbsp;</td>
        <td width="111" >Total Mondays:</td>
        <td width="111" >Total Tuesdays:</td>
        <td width="110" >Total Wednesdays:</td>
        <td width="111" >Total Thursdays:</td>
        <td width="112" >Total Fridays:</td>
        <td width="110" >Total Saturdays:</td>
        <td width="111" >Total Sundays:</td>
        <td width="118" >Total All Days:</td>
    </tr>
    <tr class="table-reports-text1">
        <td>&nbsp;</td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $mon['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $mon['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $tue['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $tue['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $wed['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $wed['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $thu['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $thu['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $fri['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $fri['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $sat['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $sat['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok">
                <div class="ordernums"><?= $sun['cnt'] ?> orders</div>
                <div class="revenuesum">$<?= $sun['sum'] ?></div>
            </div>
        </td>
        <td style="text-align: center;">
            <div class="grey-blok" style="margin-left: 5px;">
                <div class="ordernums"><?=$total['cnt'] ?> orders</div>
                <div class="revenuesum">$<?=$total['sum'] ?></div>
            </div>
        </td>
    </tr>
</table>
