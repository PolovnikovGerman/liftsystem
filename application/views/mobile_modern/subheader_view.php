<div class="container-xl">
    <div class="row">
        <div class="col-6 d-flex align-items-end pr-0">
            <div class="deptitemmenu">
                <?php if (!empty($activelnk)) : ?>
                <div class="deptitem-active">
                    <div class="deptitem-icon">
                        <?php if (!empty($active_icon)) : ?>
                        <img src="<?=$active_icon?>">
                        <?php endif; ?>
                    </div>
                    <div class="deptitem-name"><?=$active_label?></div>
                </div>
                <?php endif; ?>
                <div class="deptitem-dropdown dropdown">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dd-deptitems" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
                    <?=$main_menu?>
                </div>
            </div>
        </div>
        <div class="col-6 pl-0">
            <div class="search-block">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Find Orders" aria-label="Find Orders" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <span class="input-group-text">
                          <img src="/img/mobile_modern/icon-magnifying-glass.svg">
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
