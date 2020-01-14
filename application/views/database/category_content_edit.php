<div class="content-row">
    <div class="labelinpt category_name">Page Title:</div>
    <input class="category_name" data-content="category" data-field="category_name" value="<?=$category_name?>"/>
</div>
<div class="content-row">
    <div class="labelinpt category_keywords">Internal Search Keywords:</div>
</div>
<div class="content-row">
    <textarea class="category_keywords" data-content="category" data-field="category_keywords"><?=$category_keywords?></textarea>
</div>
<div class="content-row">
    <div class="labelinpt dropdown_title">Drop Down Name: </div>
    <input class="dropdown_title" data-content="category" data-field="dropdown_title" value="<?=$dropdown_title?>"/>
</div>
<div class="content-row">
    <div class="labelinpt dropdown_icon">Drop Down Image:</div>
    <div class="category_imagesubtitle">click image to enlarge</div>
</div>
<div class="content-row">
    <div class="category_imagesubtitlesize">(438px x 524px)</div>
    <div id="dropdownicon_area">
        <?php if ($icon_dropdown) { ?>
            <div class="dropdown_iconsrc">
                <img src="<?=$icon_dropdown?>" alt="Dropdown Image"/>
            </div>
            <div class="remove_dropdown_icon">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        <?php } else { ?>
            <div class="dropdown_iconempty">
                <div class="dropdownupload" id="newdropdownimg"></div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="content-row">
    <div class="labelinpt homepage_title">Homepage Collage Name: </div>
    <input class="homepage_title" data-content="category" data-field="homepage_title" value="<?=$homepage_title?>"/>
</div>
<div class="content-row">
    <div class="labelinpt icon_homepage">Homepage Collage Image:</div>
    <div class="category_imagesubtitle">click image to enlarge</div>
</div>
<div class="content-row">
    <div class="category_imagesubtitlesize">(356px x 240px)</div>
    <div id="homepageicon_area">
        <?php if ($icon_homepage) { ?>
            <div class="homepage_iconsrc">
                <img src="<?=$icon_homepage?>" alt="Dropdown Image"/>
            </div>
            <div class="remove_homepage_icon">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        <?php } else { ?>
            <div class="homepage_iconempty">
                <div class="homepageupload" id="newhomepageimg"></div>
            </div>
        <?php } ?>
    </div>
</div>
