<!DOCTYPE html>
<html>
<head>
    <?=$head_view?>
</head>
<body>
<!--<container class="container-fluid pl-0 pr-0">-->
<!--    <div class="maincontent --><?php //=$brandclass?><!--">-->
<!--        <div class="maincontent_view --><?php //=$brandclass?><!--">-->
    <div id="artModal">
            <div class="modal-content" style="top: 0;">
                <div class="modal-header">
<!--                    <div class="leadorderclose"><img src="/img/leadquote/close_quote_btn.png"></div>-->
                    <h4 class="modal-title" id="artModalLabel"><?=$header?></h4>
                </div>
                <div class="modal-body" style="float: left;"><?=$content_view?></div>
                <div class="modal-footer">
                    <input type="hidden" id="root_call_page" value="newquoteorder"/><input type="hidden" id="root_brand" value="<?=$brand?>"/>
                </div>
            </div>
    </div>
    <!--        </div>-->
<!--    </div>-->
<!--</container>-->
<!--<footer></footer>-->
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
</body>
</html>




<!--<div class="modal-content">-->
<!--    <div class="modal-header">-->
<!--        <div class="leadorderclose"><img src="/img/leadquote/close_quote_btn.png"></div>-->
<!--        <h4 class="modal-title" id="artModalLabel">--><?php //=$header?><!--</h4>-->
<!--    </div>-->
<!--    <div class="modal-body" style="float: left;">--><?php //=$content?><!--</div>-->
<!--    <div class="modal-footer">-->
<!--        <input type="hidden" id="root_call_page" value="leads"/><input type="hidden" id="root_brand" value="SB"/>-->
<!--    </div>-->
<!--</div>-->