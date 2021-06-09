<div class="dbexport_form">
    <div class="dbexport_row">
        <div class="db_number">
            Item # like
            <input type="text" id="item_number" name="item_number" class="item_number"/>
        </div>
        <div class="db_name">
            Item Name like
            <input type="text" id="item_name" name="item_name" class="item_name"/>
        </div>
        <div class="db_template">
            Template
            <select name="itm_template" id="itm_template" class="select_template">
                <option value="">Select Template</option>
                <option value="Stressball">Stressball</option>
                <option value="Other Item">Other Item</option>
            </select>
        </div>
        <div class="db_itemnew">
            Active
            <select name="itm_active" id="itm_active" class="select_new">
                <option value="">All</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
    </div>
    <div class="dbexport_row">
        <div class="db_itemnew">
            New
            <select name="itm_new" id="itm_new" class="select_new">
                <option value="">All</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="lead_label">Lead </div>
        <div class="lead_item">
            A
            <input id="lead_a" name="lead_a" class="leadinput"/>
        </div>
        <div class="lead_item">
            B
            <input id="lead_b" name="lead_b" class="leadinput"/>
        </div>
        <div class="lead_item">
            C
            <input id="lead_c" name="lead_c" class="leadinput"/>
        </div>
        <div class="db_vendor">
            Vendor
            <select class="vendors" id="export_vendor">
                <option value="">Select Vendor</option>
                <?php foreach ($vendors as $vendor) { ?>
                    <option value="<?=$vendor['vendor_id']?>"><?=$vendor['vendor_name']?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="dbexport_row">
        <a class="db_search" id="db_exportsearch" href="javascript:void(0);">Search</a>
        <a class="db_search" id="db_exportclean" href="javascript:void(0);">Clear</a>
        <div id="db_searchres" class="db_search_results">&nbsp;</div>
        <a class="db_search" id="db_export" href="javascript:void(0);">Export DB</a>
    </div>
</div>
<div class="db_export_head">
    <div class="itemnumber">Item #</div>
    <div class="itemname">Item Name</div>
    <div class="itemtemplate">Template</div>
    <div class="itemnew">New</div>
    <div class="vendor">Vendor</div>
</div>
<div class="db_export_results"></div>
