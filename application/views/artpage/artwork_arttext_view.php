<div class="artworksarea" data-artworkartid="<?=$artwork_art_id?>" data-contentview="text">
    <div class="artworkdata_content">
        <div class="artworkdata <?=$location_state?>" data-artworkartid="<?=$artwork_art_id?>">
            <div class="artworklabel" data-artworkartid="<?=$artwork_art_id?>"><?=$artlabel?></div>
            <div class="artworktextsource" data-artworkartid="<?=$artwork_art_id?>">
                <div class="artworkusrtxt" data-artworkartid="<?=$artwork_art_id?>">
                    <?=$texticon?>
                </div>
                <div class="artpopup_fontlabel">Font:</div>
                <div class="artpopup_fontval">
                    <input type="text" class="artfont" data-artworkartid="<?=$artwork_art_id?>" value="<?=$font?>" readonly="readonly"/>
                </div>
            </div>
            <div class="artworkredraw">
                <?=$redrawchk?>
            </div>
            <div class="artworkrush" data-artworkartid="<?=$artwork_art_id?>">
                <?=$rushchk?>
            </div>
            <div class="artworkvector" data-artworkartid="<?=$artwork_art_id?>"><?=$logo_vectorized?></div>
            <div class="artworkrdrnote" data-artworkartid="<?=$artwork_art_id?>">
                <?=$redrawicon?>
            </div>
            <div class="artworkredo">
                <?=$redochk?>
            </div>
        </div>
        <div class="artworkoptions">
            <div class="artworkoption_label">Options:</div>
            <div class="artworkoption_colors"># Colors:</div>
            <div class="artworkoption_colors_select">
                <select class="artnumcolors" data-artworkartid="<?=$artwork_art_id?>">
                    <option value="" <?=($art_numcolors=='' ? 'selected="selected"' : '')?>>&nbsp;</option>
                    <option value="1" <?=($art_numcolors==1 ? 'selected="selected"' : '')?>>1</option>
                    <option value="2" <?=($art_numcolors==2 ? 'selected="selected"' : '')?>>2</option>
                    <option value="3" <?=($art_numcolors==3 ? 'selected="selected"' : '')?>>3</option>
                    <option value="4" <?=($art_numcolors==4 ? 'selected="selected"' : '')?>>4</option>
                </select>
            </div>
            <div class="artworkoption_color">Color:</div>
            <div class="artworkoption_color_choices" data-artworkartid="<?=$artwork_art_id?>">
                <?=$optioncolors?>
            </div>
            <div class="artworkoption_loclabel">Loc:</div>
            <div class="artworkoption_locselect" data-artworkartid="<?=$artwork_art_id?>"><?=$imprloc_view?></div>
        </div>
    </div>
    <div class="artworkdelete" data-artworkartid="<?=$artwork_art_id?>">
        <img src="/img/icons/cancel.png"/>
    </div>
</div>