<div class="relievers_metasearch">
    <div class="sectionlabel">META &amp; SEARCH:</div>
    <div class="sectionbody">
        <div class="itemparamlabel metatitle">Meta Title:</div>
        <div class="itemparamvalue metatitle editmode">
            <input type="text" class="itemkeyinfoinput metatitle <?=empty($item['item_meta_title']) ? 'missing_info' : ''?>" data-item="item_meta_title" value="<?=$item['item_meta_title']?>"/>
        </div>
        <div class="content-row">
            <div class="itemparamlabel metadescription">Meta Description:</div>
            <div class="itemparamlabel itemurl">Page URL:</div>
        </div>
        <div class="content-row">
            <div class="itemparamvalue metadescription editmode">
                <textarea class="inputkeyinfotext metadescription <?=empty($item['item_metadescription']) ? 'missing_info' : ''?>"><?=$item['item_metadescription']?></textarea>
            </div>
            <div class="itemparamvalue itemurl editmode">
                <input type="text" class="itemkeyinfoinput metaurl <?=empty($item['item_url']) ? 'missing_info' : ''?>" data-item="item_url" value="<?=$item['item_url']?>"/>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel metakeywords">Meta Keywords:</div>
            <div class="itemparamvalue metakeywords editmode">
                <textarea class="inputkeyinfotext metakeywords <?=empty($item['item_metakeywords']) ? 'missing_info' : ''?>"><?=$item['item_metakeywords']?></textarea>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemkeywords">INTERNAL SEARCH KEYWORDS:</div>
            <div class="itemparamvalue itemkeywords editmode">
                <textarea class="inputkeyinfotext itemkeywords <?=empty($item['item_keywords']) ? 'missing_info' : ''?>"><?=$item['item_keywords']?></textarea>
            </div>
        </div>
    </div>
</div>
