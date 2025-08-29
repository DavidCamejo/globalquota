<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\Util;

class Admin implements ISettings {

    public function getForm(): TemplateResponse {
        // Inyecta assets
        Util::addScript('globalquota', 'admin-globalquota');
        Util::addStyle('globalquota', 'admin-globalquota');
        
        return new TemplateResponse('globalquota', 'admin');
    }

    public function getSection(): string {
        // Lo verás en Configuración → Administración → Servidor
        return 'server';
    }

    public function getPriority(): int {
        return 50;
    }
}
