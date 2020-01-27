<div class="dialog-location-body">
    <input type="hidden" id="imprsession" value="<?=$session?>"/>
    <div class="dialog-location-row">
        <div class="dialog-location-rowlegend">
            Location Name:
        </div>
        <div class="dialog-location-rowvalue">
            <input value="<?=$imprint['item_inprint_location']?>" data-fld="item_inprint_location" class="imprintlocationedit" type="text">
        </div>
    </div>
    <div class="dialog-location-row">
        <div class="dialog-location-rowlegend">
            Location Size:
        </div>
        <div class="dialog-location-rowvalue">
            <input value="<?=$imprint['item_inprint_size']?>" data-fld="item_inprint_size" class="imprintlocationedit" type="text">
        </div>
    </div>
    <!-- Div for Input -->
    <div class="dialog-location-row" id="imprintlocationviewarea">
        <div class="loactionview_preview <?=empty($imprint['item_inprint_view']) ? 'emptyview' : ''?>">
            <?php if (!empty($imprint['item_inprint_view'])) { ?>
                <img src ="<?=$imprint['item_inprint_view']?>" alt="Preview">
                <div class="delimprintview">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div id="newimprintlocationview"></div>
            <?php } ?>
        </div>
    </div>
    <div class="savelocationload">
        <img src="/img/itemdetails/save_order.png" />
    </div>
</div>
