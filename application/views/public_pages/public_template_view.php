<!DOCTYPE html>
<html>
<head>
    <?= $head ?>
</head>
<body>
    <container>
        <?= $content ?>
    </container>
    <footer></footer>
    <!-- Modal for only simple informational messages (success or error)-->
    <div class="modal fade" id="modal_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 10000">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p></p>
                    <button class="btn btn-confirm" id="confirm" type="button">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for only simple informational messages (success or error)-->
    <div class="modal fade" id="modal_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p></p>
                    <button class="btn btn-confirm" id="confirm_yes" type="button">
                        YES
                    </button>
                    <button class="btn btn-confirm" id="confirm_no" type="button">
                        NO
                    </button>
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
            </div>
        </div>
    </div>
</body>
</html>
