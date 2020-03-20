<div class="page_container">
    <input type="hidden" value="<?=$brand?>" id="notificationsviewbrand"/>
    <div class="left_maincontent" id="notificationsviewbrandmenu">
        <?=$left_menu?>
    </div>
    <div class="right_maincontent">
        <div class="countriescontent">
            <input id="search_template" type="hidden" value=""/>
            <div class="search_templates">
                <div class="search_template_title">Search by:</div>
                <?php foreach ($search_templ as $row) {?>
                    <div class="search_template" data-searchid="<?=$row['template']?>"><?=$row['template']?></div>
                <?php } ?>
                <div class="search_clean">Clean</div>
            </div>
            <div class="countries_data_title">
                <div class="country_name">Country</div>
                <div class="country_code2">ALPHA2</div>
                <div class="country_code3">ALPHA3</div>
                <div class="country_shzone">Ship. Zone</div>
                <div class="country_shipallow">Ship. Allowed</div>
            </div>
            <div class="countries_data"></div>
        </div>
    </div>
</div>
