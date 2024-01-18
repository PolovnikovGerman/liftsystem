<div class="relievers_metasearch">
    <div class="sectionlabel">META &amp; SEARCH:</div>
    <div class="sectionbody <?=$missinfo==0 ? '' : 'missinginfo'?>">
        <div class="itemparamlabel metatitle">Meta Title:</div>
        <div class="itemparamvalue metatitle <?=empty($item['item_meta_title']) ? 'missing_info' : ''?>"
             data-event="hover" data-css="itemdetailsballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="right" data-textcolor="#000"
             data-balloon="<?=$item['item_meta_title']?>" data-timer="4000" data-delay="1000">
            <?=$item['item_meta_title']?>
        </div>
        <div class="content-row">
            <div class="itemparamlabel metadescription">Meta Description:</div>
            <div class="itemparamlabel itemurl">Page URL:</div>
        </div>
        <div class="content-row">
            <div class="itemparamvalue metadescription <?=empty($item['item_metadescription']) ? 'missing_info' : ''?>" data-event="hover" data-css="itemdetailsballonarea" data-bgcolor="#FFFFFF"
                 data-bordercolor="#000" data-position="left" data-textcolor="#000" data-balloon="<?=$item['item_metadescription']?>" data-timer="6000" data-delay="1000">
                <?=$item['item_metadescription']?>
            </div>
            <div class="itemparamvalue itemurl <?=empty($item['item_url']) ? 'missing_info' : ''?>" data-event="hover" data-css="itemdetailsballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000"
                 data-position="up" data-textcolor="#000" data-balloon="<?=$item['item_url']?>" data-timer="4000" data-delay="1000">
                <?=$item['item_url']?>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel metakeywords">Meta Keywords:</div>
            <div class="itemparamvalue metakeywords <?=empty($item['item_metakeywords']) ? 'missing_info' : ''?>" data-event="hover" data-css="itemdetailsballonarea" data-bgcolor="#FFFFFF"
                 data-bordercolor="#000" data-position="left" data-textcolor="#000" data-balloon="<?=$item['item_metakeywords']?>" data-timer="6000" data-delay="1000"><?=$item['item_metakeywords']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemkeywords">INTERNAL SEARCH KEYWORDS:</div>
            <div class="itemparamvalue itemkeywords <?=empty($item['item_keywords']) ? 'missing_info' : ''?>"  data-event="hover" data-css="itemdetailsballonarea" data-bgcolor="#FFFFFF"
                 data-bordercolor="#000" data-position="left" data-textcolor="#000" data-balloon="<?=$item['item_keywords']?>" data-timer="6000" data-delay="1000"><?=$item['item_keywords']?></div>
        </div>
    </div>
</div>
