<div class="imprintfonts">
    <input type="hidden" id="fontselectfor" value=""/>
    <div class="imprintfonts-row">
        <div class="imprintfonts-legend">Enter the name of the font you would like:</div>
        <div class="imprintfonts-input"><input type="text" class="fontmanual" value=""/></div>
    </div>
    <div class="imprintfonts-row">
        Or select one of the following popular fonts from our library:
    </div>
    <div class="imprintfonts_table">
        <table class="table-fontpopular">
            <?php $cntrow=0;$cntall=0;$fontcnt=count($fonts_popular);?>
            <?php if ($fontcnt>0) {?>
                <tr><td colspan="6" style="text-align: center; font-size: 21px; font-weight: bold; color: #0000c0; font-style: italic;">Very Popular Fonts:</td></tr>
                <tr>
                <?php foreach ($fonts_popular as $row) { ?>
                    <td class="radiselect">
                        <input id="font<?=$row['font_id']?>" type="radio" name="fontoption" class="fontoption" value="<?=$row['font_name']?>" />
                    </td>
                    <td class="fontdat">
                        <div class="fontshow">
                            <img src="<?=$row['font_example']?>" alt="<?=$row['font_name']?>" title="<?=$row['font_name']?>" />
                        </div>
                    </td>
                    <?php $cntrow++;?>
                    <?php $cntall++;?>
                    <?php if ($cntrow==3 && $cntall<$fontcnt) {?>
                        </tr>
                        <tr>
                        <?php $cntrow=0;?>
                    <?php } ?>
                <?php } ?>
                <?php if ($cntrow<3) { echo '<td>&nbsp;</td>'; }?>
                </tr>
            <?php } ?>
        </table>
        <table class="table-imprintfonts">
            <?php $cntrow=1;$cntall=0;$fontcnt=count($fonts_other);?>
            <?php if ($fontcnt>0) {?>
                <tr><td colspan="4" style="text-align: center; color: #0000FF; font-size: 21px; font-style: italic; font-weight: bold;">Other Popular Fonts:</td></tr>
                <tr>
                <?php foreach ($fonts_other as $row) { ?>
                    <td class="radiselect">
                        <input id="font<?=$row['font_id']?>" type="radio" name="fontoption" class="fontoption" value="<?=$row['font_name']?>" />
                    </td>
                    <td class="fontdat">
                        <div class="fontshow">
                            <img src="<?=$row['font_example']?>" alt="<?=$row['font_name']?>" title="<?=$row['font_name']?>" />
                        </div>
                    </td>
                    <?php $cntrow++;?>
                    <?php $cntall++;?>
                    <?php if ($cntrow==3 && $cntall<$fontcnt) {?>
                        </tr>
                        <tr>
                        <?php $cntrow=1;?>
                    <?php } ?>
                <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="select_font_bottom">
        <div class="font_button_select">select font</div>
    </div>
</div>
