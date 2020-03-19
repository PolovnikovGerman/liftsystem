<div class="content-row">
    <div class="labelinpt category_name">Page Title:</div>
    <input class="category_name" readonly="readonly" value="<?=$category_name?>"/>
</div>
<div class="content-row">
    <div class="labelinpt category_keywords">Internal Search Keywords:</div>
</div>
<div class="content-row">
    <textarea class="category_keywords" readonly="readonly"><?=$category_keywords?></textarea>
</div>
<div class="content-row">
    <div class="labelinpt dropdown_title">Drop Down Name: </div>
    <input class="dropdown_title" readonly="readonly" value="<?=$dropdown_title?>"/>
</div>
<div class="content-row">
    <div class="labelinpt dropdown_icon">Drop Down Image:</div>
    <div class="category_imagesubtitle">click image to enlarge</div>
</div>
<div class="content-row">
    <div class="category_imagesubtitlesize">(438px x 524px)</div>
    <?php if ($icon_dropdown) { ?>
        <div class="dropdown_iconsrc">
            <img src="<?=$icon_dropdown?>" alt="Dropdown Image"/>
        </div>
    <?php } else { ?>
        <div class="dropdown_iconempty">&nbsp;</div>
    <?php } ?>
</div>
<div class="content-row">
    <div class="labelinpt homepage_title">Homepage Collage Name: </div>
    <input class="homepage_title" readonly="readonly" value="<?=$homepage_title?>"/>
</div>
<div class="content-row">
    <div class="labelinpt icon_homepage">Homepage Collage Image:</div>
    <div class="category_imagesubtitle">click image to enlarge</div>
</div>
<div class="content-row">
    <div class="category_imagesubtitlesize">(356px x 240px)</div>
    <?php if ($icon_homepage) { ?>
        <div class="homepage_iconsrc">
            <img src="<?=$icon_homepage?>" alt="Dropdown Image"/>
        </div>
    <?php } else { ?>
        <div class="homepage_iconempty">&nbsp;</div>
    <?php } ?>
</div>
