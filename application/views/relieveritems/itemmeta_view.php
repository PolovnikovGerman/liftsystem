<div class="relievers_metasearch">
    <div class="sectionlabel">META &amp; SEARCH:</div>
    <div class="sectionbody">
        <div class="itemparamlabel metatitle">Meta Title:</div>
        <div class="itemparamvalue metatitle <?=empty($item['item_meta_title']) ? 'missing_info' : ''?>"><?=$item['item_meta_title']?></div>
        <div class="content-row">
            <div class="itemparamlabel metadescription">Meta Description:</div>
            <div class="itemparamlabel itemurl">Page URL:</div>
        </div>
        <div class="content-row">
            <div class="itemparamvalue metadescription <?=empty($item['item_metadescription']) ? 'missing_info' : ''?>">
                <?=$item['item_metadescription']?>
            </div>
            <div class="itemparamvalue itemurl <?=empty($item['item_url']) ? 'missing_info' : ''?>"><?=$item['item_url']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel metakeywords">Meta Keywords:</div>
            <div class="itemparamvalue metakeywords <?=empty($item['item_metakeywords']) ? 'missing_info' : ''?>"><?=$item['item_metakeywords']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemkeywords">INTERNAL SEARCH KEYWORDS:</div>
            <div class="itemparamvalue itemkeywords <?=empty($item['item_keywords']) ? 'missing_info' : ''?>"><?=$item['item_keywords']?></div>
        </div>
    </div>
</div>
