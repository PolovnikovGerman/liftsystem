<div class="billingbymethods">
    <div class="billingbymethods-bgn">&nbsp;</div>
    <div class="billingbymethods-middle">
        <table>
            <tr>
                <td rowspan="2">
                    <select class="vendpayyear">
                        <?php foreach ($years as $row) {?>
                            <option value="<?=$row?>" <?=($row==$year ? 'selected="selected"' : '')?>><?=$row?></option>
                        <?php } ?>
                    </select>
                </td>
                <?php foreach ($payments as $row) { ?>
                    <td><?=$row['vendor']?></td>
                <?php } ?>
            </tr>
            <tr>
                <?php foreach ($payments as $row) { ?>
                    <td><?=$row['pay']?></td>
                <?php } ?>
            </tr>
        </table>
    </div>
    <div class="billingbymethods-end">&nbsp;</div>
    <div class="manage-purchase-methods">Manage Methods</div>
</div>
