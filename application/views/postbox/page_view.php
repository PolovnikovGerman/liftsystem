<input type="hidden" id="currentpostbox" value="<?=$postbox?>"/>
<input type="hidden" id="currentpostfolder" value=""/>
<input type="hidden" id="postboxsort" value="date_desc"/>
<div class="emlcontant">
    <div class="emailer-header datarow">
        <div class="btn-compose"><span class="btn-compose-icon"><i class="fa fa-pencil" aria-hidden="true"></i></span>Compose</div>
        <div class="eml-mainbtns"></div>
        <div class="btn-newfolder">+ New Folder</div>
        <div class="eml-folders">
        </div>
    </div>
    <div class="emailer-body datarow">
        <div class="emailesblock datarow">
            <div class="eml-header datarow">
                <div class="selectall">
                    <div class="allemls-inpt">
                        <input type="checkbox" name="allemls">
                    </div>
                    <div class="allemls-options">
                        <select>
                            <option>All</option>
                            <option>None</option>
                            <option>Read</option>
                            <option>Unread</option>
                            <option>Marked</option>
                            <option>Unmarked</option>
                        </select>
                    </div>
                </div>
                <div class="emlsblock-name"></div>
                <div class="sortbox">
                    <label>Sort</label>
                    <select>
                        <option>By Date</option>
                        <option>By Time</option>
                    </select>
                </div>
                <div class="emlheader-menu">
                    <div class="emlmenu-item movemsglist">
                        <div class="emlmenu-icon">
                            <img src="/img/postbox/icon-move.svg" alt="Move">
                        </div>
                        <div class="emlmenu-text">Move</div>
                    </div>
                    <div class="emlfolders-menu" id="msglistfolders"></div>
                    <div class="emlmenu-item deletemsglist">
                        <div class="emlmenu-icon">
                            <img src="/img/postbox/icon-delete.svg" alt="Delete">
                        </div>
                        <div class="emlmenu-text">Delete</div>
                    </div>
                    <div class="emlmenu-item">
                        <div class="emlmenu-icon">
                            <img src="/img/postbox/icon-spam.svg" alt="Spam">
                        </div>
                        <div class="emlmenu-text">Spam</div>
                    </div>
                    <div class="emlmenu-item">
                        <div class="emlmenu-icon emlmenu-more">
                            <img src="/img/postbox/icon-more.svg" alt="More">
                        </div>
                        <div class=""></div>
                    </div>
                    <div class="eml-moremenu">
                        <ul>
                            <li>
                                <div class="esmitem" id="msglistunread">
                                    <div class="esmitem-icon icn-unread">
                                        <img src="/img/postbox/icon-unread.svg" alt="Unread">
                                    </div>
                                    <div class="esmitem-txt">Mark as unread</div>
                                </div>
                            </li>
                            <li>
                                <div class="esmitem" id="msglistarchive">
                                    <div class="esmitem-icon">
                                        <img src="/img/postbox/icon-archive.svg" alt="Archive">
                                    </div>
                                    <div class="esmitem-txt">Archive</div>
                                </div>
                            </li>
                            <li>
                                <div class="esmitem" id="msglistignore">
                                    <div class="esmitem-icon">
                                        <i class="fa fa-bell-slash" aria-hidden="true"></i>
                                    </div>
                                    <div class="esmitem-txt">Ignore</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="eml-table datarow" id="eml-table-messages"></div>
        </div>
    </div>
    <div class="emaildetails"></div>
</div>

<!-- </div> -->
<div class="datarow">
    <div class="emailer-footer">
        <div class="emails-menu datarow">
            <div class="rowemailname">Email:</div>
            <?=$menu_view?>
        </div>
        <div class="domain-menu datarow">
            <div class="rowemailname">Domain:</div>
            <div class="domainmenu-body">
                <div class="domainmenu-tab sb-tab <?=$brand=='SB' ? 'active' : ''?>">stressball.com</div>
                <div class="domainmenu-tab sr-tab <?=$brand=='SR' ? 'active' : ''?>">stressreliever.com</div>
            </div>
        </div>
    </div>
</div>
