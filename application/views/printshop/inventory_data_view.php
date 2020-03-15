<?php /*$numpp = 0 */?><!--
<?php /*foreach ($data as $row) { */?>
    <div class="inventorydatarow <?/*= $numpp % 2 == 0 ? 'white' : 'grey' */?> <?/*= $row['type'] == 'item' ? 'itemdata' : '' */?>" data-item="<?/*= $row['printshop_item_id'] */?>" data-color="<?/*=$row['printshop_color_id']*/?>">
        <div class="numpp">
            <?php /*if ($row['type']=='item') { */?>
                <div class="edititem" data-item="<?/*= $row['printshop_item_id'] */?>">
                    <i class="fa fa-pencil"></i>
                </div>                
            <?php /*} else { */?>
                <?/*= $row['numpp'] */?>
            <?php /*} */?>
        </div>        
        <div class="itemnum <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">
            <?php /*if ($row['type']=='item') { */?>
                <?/*= $row['item_num'] */?>
            <?php /*} else { */?>
                <div class="editcolor" data-color="<?/*=$row['printshop_color_id']*/?>" data-item="<?/*= $row['printshop_item_id'] */?>">
                    <i class="fa fa-pencil"></i>
                </div>            
            <?php /*} */?>
        </div>
        <?php /*if ($row['type'] == 'item') { */?>
            <div class="itemname"><?/*= $row['item_name'] */?></div>
            <div class="additemcolor" data-item="<?/*= $row['printshop_item_id'] */?>">&nbsp;</div>
        <?php /*} else { */?>
            <div class="coloritemname" data-color="<?/*=$row['printshop_color_id']*/?>"><?/*= $row['item_name'] */?></div>
        <?php /*} */?>
        <div class="itempercent <?/*=$row['stockclass']*/?> <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['percent'] */?></div>
        <div class="instock <?/*=$row['stockclass']*/?> <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['instock'] */?></div>
        <div class="reserved <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['reserved'] */?></div>
        <div class="available <?/*=$row['stockclass']*/?> <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['availabled'] */?></div>
        <div class="devider <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">&nbsp;</div>
        <div class="have">4564</div>
        <div class="max">8000</div>
        <div class="to_get">8436</div>
        <div class="devider <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">&nbsp;</div>
        <div class="back_up">90,436</div>
        <div class="devider <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">&nbsp;</div>
        <div class="on_route <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['onroutestock'] */?></div>
        <div class="devider <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>">&nbsp;</div>
        <div class="costea <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['price'] */?></div>
        <div class="totalea <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['total'] */?></div>
        <div class="colororder <?/*= ($row['type'] == 'item' ? 'border_b' : '') */?>"><?/*= $row['color_order'] */?></div>
        <div class="colordesript <?/*=($row['type'] == 'item' ? 'border_b' : '')*/?>"><?/*=$row['color_descript']*/?></div>
    </div>
    <?php /*$numpp++; */?>
<?php /*} */?>
<?php /*if ($numpp < 22) { */?>
    <?php /*for ($i = $numpp; $i < 22; $i++) { */?>
        <div class="inventorydatarow <?/*= $i % 2 == 0 ? 'white' : 'grey' */?>">&nbsp;</div>
    <?php /*} */?>
--><?php /*} */?>