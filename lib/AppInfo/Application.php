<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\AppInfo;

use OCA\GlobalQuota\Settings\Admin;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
    public const APP_ID = 'globalquota';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        // Registrar el panel de configuración de administración
        $context->registerSetting(Admin::class);
    }

    public function boot(IBootContext $context): void {
        // Aquí puedes agregar inicialización si la necesitas
    }
}
