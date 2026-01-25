<div class="artw-area">
    <div class="artw-row">
        <div class="artw-linebox <?=$location['ready']==1 ? 'lightgreen' : 'white'?>">
            <div class="artw-type"><?=$numpp?>. <?=$location['art_type']?></div>
            <div class="artw-srclabel">
                <div class="artw-srcfont">
                    <div class="artw-srcfont-icn <?=empty($location['customer_text']) ? '' : 'filled'?>" data-art="<?=$location['artwork_art_id']?>">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    </div>
                    <div class="artw-srcfont-txt">Font:</div>
                    <div class="artw-srcfont-inp">
                        <input type="text" name="artw-srcfont-inp" readonly="readonly" data-art="<?=$location['artwork_art_id']?>">
                    </div>
                </div>
            </div>
            <div class="artw-redraw">
                <input type="checkbox" name="logoredraw" class="proofreqestlocation" data-art="<?=$location['artwork_art_id']?>" data-fld="redrawvect" <?=$location['redrawvect']==1 ? 'checked="checked"' : ''?> />
            </div>
            <div class="artw-rush">
                <input type="checkbox" name="logorush"  class="proofreqestlocation" data-art="<?=$location['artwork_art_id']?>" data-fld="rush" <?=$location['rush']==1 ? 'checked="checked"' : ''?> />
            </div>
            <div class="artw-vector">&nbsp;</div>
            <div class="artw-rdrnotes">
                <span class="artw-rdrnotes-icon <?=empty($location['redraw_message']) ? '' : 'filled'?>" data-art="<?=$location['artwork_art_id']?>">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                </span>
            </div>
            <div class="artw-redo">
                <input type="checkbox" name="redo" class="proofreqestlocation" data-art="<?=$location['artwork_art_id']?>" data-fld="redo" <?=$location['redrawvect']==1 ? 'checked="checked"' : ''?>/>
            </div>
        </div>
        <div class="artw-delete">
            <div class="artw-btndelete" data-art="<?=$location['artwork_art_id']?>"><i class="fa fa-trash" aria-hidden="true"></i></div>
        </div>
    </div>
    <div class="artw-rowoptions">
        <div class="artwoptions-title">Options:</div>
        <div class="artwoptions-subtitle"># Colors:</div>
        <div class="artwoptions-selectcolors">
            <select class="proofreqestlocation" data-art="<?=$location['artwork_art_id']?>" data-fld="art_numcolors">
                <option value="0"></option>
                <option value="1" <?=$location['art_numcolors']==1 ? 'selected="selected"' : ''?>>1 Color</option>
                <option value="2" <?=$location['art_numcolors']==2 ? 'selected="selected"' : ''?>>2 Color</option>
                <option value="3" <?=$location['art_numcolors']==3 ? 'selected="selected"' : ''?>>3 Color</option>
                <option value="4" <?=$location['art_numcolors']==4 ? 'selected="selected"' : ''?>>4 Color</option>
            </select>
        </div>
        <div class="artwoptions-subtitle"> Color:</div>
        <div class="artwoptions-colorlist">
            <?php for($i=1; $i<5; $i++) : ?>
                <div class="artwoptions-colorbox <?=$location['color'.$i.'_active']==1 ? 'activebox' : '' ?> <?=$location['color'.$i.'_style']?>" <?=$location['color'.$i.'_title']?>
                     data-art="<?=$location['artwork_art_id']?>" data-fld="art_color<?=$i?>">&nbsp;</div>
            <?php endfor; ?>
        </div>
        <div class="artwoptions-subtitle"> Loc:</div>
        <div class="selectlocation">
            <select class="proofreqestlocation" data-art="<?=$location['artwork_art_id']?>" data-fld="art_location">
                <?php foreach ($imprint_locations as $imprlocation): ?>
                    <option value="<?=$imprlocation['key']?>" <?=$imprlocation['key']==$location['art_location'] ? 'selected="selected"' : ''?>>
                        <?=$imprlocation['value']?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
