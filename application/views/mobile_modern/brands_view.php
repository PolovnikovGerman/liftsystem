<div class="nav-tabs-sites">
    <ul>
        <?php if (in_array('SB', $brands)) : ?>
            <li class="tab-site <?=$brand=='SB' ? 'active' : ''?> tab-stressballs">
                <img src="/img/mobile_modern/logo-stressballs.svg">
            </li>
        <?php endif; ?>
        <?php if (in_array('SR', $brands)) : ?>
            <li class="tab-site <?=$brand=='SR' ? 'active' : ''?> tab-stressrelievers">
                <img src="/img/mobile_modern/logo-stressrelievers.svg">
            </li>
        <?php endif; ?>
        <?php if (in_array('SG', $brands)) : ?>
            <li class="tab-site <?=$brand=='SG' ? 'active' : ''?> tab-sigma">
                <img src="/img/mobile_modern/sigma-logo-black.svg">
            </li>
        <?php endif; ?>
    </ul>
</div>
