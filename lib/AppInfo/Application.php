<?php

namespace OCA\GlobalQuota\AppInfo;

use OCP\AppFramework\App;
use OCA\GlobalQuota\Service\QuotaService;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('globalquota', $urlParams);

        $container = $this->getContainer();

        // Registrar el servicio central de GlobalQuota con sus dependencias
        $container->registerService(QuotaService::class, function($c) {
            return new QuotaService(
                $c->query('OCP\\IConfig'),
                $c->query('OCP\\IUserManager'),
                $c->query('OCP\\Files\\IRootFolder'),
                $c->query('OCP\\AppFramework\\Utility\\ITimeFactory')
            );
        });
    }
}
