<div class="maincontent">
    <div class="leftmenuarea">
        <?=$left_menu?>
    </div>
    <div class="maincontentmenuarea <?=$brand=='SB' ? 'stresballstab' : ($brand=='SR' ? 'relieverstab' : 'sigmasystem')?>" data-brand="<?=$brand?>">
        <div class="maincontentmenu">
            <div class="title"><?=$brand=='SR' ? 'stressrelievers.com' : 'stressballs.com'?>:</div>
            <?=$menu?>
        </div>
        <div class="maincontent_view"></div>
    </div>
</div>
