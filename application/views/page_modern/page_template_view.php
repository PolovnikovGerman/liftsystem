<!DOCTYPE html>
<html>
<head>
    <?=$head_view?>
</head>
<body>
<header><?=$header_view?></header>
<container class="container-fluid pl-0 pr-0">
    <div class="maincontent <?=$brandclass?>">
        <div class="maincontentmenu">
            <?=$main_menu?>
        </div>
        <div class="maincontent_view">
            <?=$content_view?>
        </div>
    </div>
</container>
<footer></footer>
<!-- loader -->
<div style="position: fixed; height: 100%; width: 100%; top: 0px; left: 0px; background: url(/img/page_view/overlay.png); text-align: center; z-index: 1100; display: none;" id="loader">
    <div style="width:100%;z-index: 15;" id="loaderimg">
        <div style="float: none; width:100%;z-index: 100;margin-top: 356px;">
            <img src="/img/page_view/loader.gif">
            <div class="clear"></div>
            <div style="color: #FFFFFF; font-size: 18px; font-weight: bold; padding: 14px 0 0 23px; text-align: center; text-shadow: 0 2px 2px #000000, 0 2px 2px #FFFFFF; vertical-align: middle;">
                Loading...
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="unlockContentModal" tabindex="-1" role="dialog" aria-labelledby="unlockContentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="unlockContentModalLabel">Enter Code to Unlock</h4>
            </div>
            <div class="modal-body"></div>
            <!--            style="float: left;"            -->
            <!--            <div class="modal-footer"></div>-->
        </div>
    </div>
</div>
<?php if (isset($modal_view)) : ?>
    <?=$modal_view?>
<?php endif; ?>
</body>
</html>

