<?php

use OCA\GlobalQuota\Command\Recalc;
use OCP\AppFramework\App;

$app = new App('globalquota');
$c = $app->getContainer();

return [
    new Recalc(
        $c->getServer()->get(IRootFolder::class),
        $c->getServer()->get(IConfig::class)
    )
];
