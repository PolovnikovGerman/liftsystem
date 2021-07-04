<div class="locationedit-body">
    <input type="hidden" id="imprsession" value="<?=$session?>"/>
    <div class="locationedit-row">
        <div class="locationedit-rowlegend">
            Location Name:
        </div>
        <div class="locationedit-rowvalue">
            <input value="<?=$imprint['item_inprint_location']?>" data-fld="item_inprint_location" class="inprintlocationedit" type="text">
        </div>
    </div>
    <div class="locationedit-row">
        <div class="locationedit-rowlegend">
            Location Size:
        </div>
        <div class="locationedit-rowvalue">
            <input value="<?=$imprint['item_inprint_size']?>" data-fld="item_inprint_size" class="inprintlocationedit" type="text">
        </div>
    </div>
    <!-- Div for Input -->
    <div class="locationedit-row" id="imprintlocationviewarea">
        <div class="inprintimage_preview <?=empty($imprint['item_inprint_view']) ? 'emptyview' : ''?>">
            <?php if (!empty($imprint['item_inprint_view'])) { ?>
                <img src ="<?=$imprint['item_inprint_view']?>" alt="Preview">
                <div class="delinprintview">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div id="newimprintlocationview"></div>
            <?php } ?>
        </div>
    </div>
    <div class="savelocationedit">
        <img src="/img/itemdetails/save_order.png" />
    </div>
</div>
