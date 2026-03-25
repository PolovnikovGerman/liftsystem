<input type="hidden" id="artsession" value="<?=$session?>"/>
<input type="hidden" id="locationtotal" value="<?=$locrecs?>"/>
<input type="hidden" id="proofdoctotal" value="<?=$proofrecs?>"/>
<input type="hidden" id="appovdoctotal" value="<?=$approvrecs?>"/>
<div class="contant-popup">
    <div class="proofrequestsform">
        <div class="pr-greyblock"><?=$common_view?></div>
        <div class="pr-lightgreen pr-instrupdate"><?=$messages_view?></div>
        <div class="pr-artworkblock">
            <div class="artwork-contant">
                <div class="artwork-header">
                    <div class="artw-titleblock">Artwork:</div>
                    <div class="artw-srclabel">Original:</div>
                    <div class="artw-redraw">Redraw:</div>
                    <div class="artw-rush">Rush:</div>
                    <div class="artw-vector">Vector:</div>
                    <div class="artw-rdrnotes">Rdr Notes:</div>
                    <div class="artw-redo">Redo:</div>
                    <div class="artw-delete">&nbsp;</div>
                </div>
                <div class="artwork-body" id="proofreqlocation_table"><?=$logos_view?></div>
            </div>
            <div class="newartwork">
                <div class="newartwork-text" data-artwork="<?=$artwork_id?>">+ New Art:</div>
                <div class="newartwork-select">
                    <select class="artlocationaadd">
                        <option value="Logo">Logo</option>
                        <option value="Text">Text</option>
                        <option value="Repeat">Repeat</option>
                        <option value="Reference">Reference</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="pr-otherblocks">
            <div class="otherblocks-left">
                <div class="pr-lightgreen pr-proofs">
                    <div class="prproofs-top">
                        <div class="prproofs-title">Proofs</div>
                        <div class="prproofs-btnemail">Email</div>
                    </div>
                    <div class="prproofs-data-table" id="proofreqproofdocs_table">
                        <?=$proofs_view?>
                    </div>
                    <div id="uploadproofdoc" data-art="<?=$artwork_id?>"></div>
                </div>
            </div>
            <div class="otherblocks-right">
                <div class="pr-lightgreen pr-templates">
                    <div class="templates-title">Templates:</div>
                    <?=$templates_view?>
                </div>
                <div class="pr-lightgreen pr-approved">
                    <div class="approved-title">Approved:</div>
                    <div class="prapproved-data-table" id="proofreqapprovdocs_table">
                        <?=$approved_view?>
                    </div>
                </div>
            </div>
        </div>
        <div class="pr-footer">
            <div class="pr-btnsave">Save</div>
        </div>
    </div>
</div>
