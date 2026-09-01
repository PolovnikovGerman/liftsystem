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
<div style="position: fixed; height: 100%; width: 100%; top: 0; left: 0; background: url(/img/page_view/overlay.png); text-align: center; z-index: 1100; display: none;" id="loader">
    <div style="width:100%;z-index: 15;" id="loaderimg">
        <div style="float: none; width:100%;z-index: 100;margin-top: 356px;">
            <img src="/img/page_view/loader.gif" alt="Loader"/>
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
<div class="modal fade" id="artNextModal" tabindex="-1" role="dialog" aria-labelledby="artNextModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="artNextModalLabel">New message</h4>
            </div>
            <div class="modal-body" style="float: left;"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade modal-prpopups" id="proofReqEditparams" tabindex="-1" role="dialog" aria-labelledby="proofReqEditparams" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="closemodal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="contant-popup">
                    <div class="prpopup-contant"></div>
                </div>
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