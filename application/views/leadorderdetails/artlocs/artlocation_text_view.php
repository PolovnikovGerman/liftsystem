<div class="art_line1">    
    <div class="artlocationarea <?=$locat_ready==1 ? 'locatready' : ''?>" data-artloc="<?=$artwork_art_id?>">        
        <div class="art_block1 viewreadyloc <?=$locat_ready==1 ? 'text_blue' : 'text_white'?> opentxtlocation" data-artloc="<?=$artwork_art_id?>" data-arttype="<?=$art_type?>">
            <div class="label">Text</div>
            <div class="customertext <?=($customer_text ? 'active' : '')?>" <?=($customer_text ? 'title="'.$customer_text.'"' : '')?> style="margin-top: 4px;" data-artloc="<?=$artwork_art_id?>">&nbsp;</div>
        </div>        
        <div class="art_block2">
            <?=$redrawchk?>
        </div>
        <div class="art_block2">
            <?=$rushchk?>            
        </div>
        <div class="art_block3">
            <div class="art_block3_text <?=$locat_ready==1 ? 'text_blue' : 'text_white'?> fonttype">Font:</div>
            <input type="text" class="art_input input_border_gray" readonly="readonly" data-artloc="<?=$artwork_art_id?>" value="<?=$font?>"/>
        </div>
        <div class="art_block4">
            <div class="redrawmsgarea <?=($redraw_message ? 'active' : '')?>" data-artloc="<?=$artwork_art_id?>">&nbsp;</div>
            <div class="art_block2" style="padding-top: 0"><?=$redochk?></div>            
        </div>
    </div>
</div>
