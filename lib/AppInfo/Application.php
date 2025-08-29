<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\EventDispatcher\IEventDispatcher;
use OCA\GlobalQuota\Listener\BeforeFileWrittenListener;
use OCA\GlobalQuota\Listener\OverrideServerInfoListener;
use OCP\Files\Events\Node\BeforeNodeWrittenEvent;

class Application extends App implements IBootstrap {
    public const APP_ID = 'globalquota';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Registro del listener para bloqueo de uploads
        $context->registerEventListener(
            BeforeNodeWrittenEvent::class,
            BeforeFileWrittenListener::class
        );

        // 🚀 DETECCIÓN AUTOMÁTICA: ServerInfo vs Panel Propio
        if (class_exists('\OCA\ServerInfo\Events\LoadAdditionalDataEvent')) {
            // Caso 1: ServerInfo soporta eventos → nos integramos
            $context->registerEventListener(
                \OCA\ServerInfo\Events\LoadAdditionalDataEvent::class,
                OverrideServerInfoListener::class
            );
        } else {
            // Caso 2: ServerInfo no soporta eventos → panel propio en Admin Settings
            $context->registerService('GlobalQuotaAdminSettings', function() {
                return new \OCA\GlobalQuota\Settings\Admin\Settings();
            });
        }
    }

    public function boot(IBootContext $context): void {
        // Boot logic if needed
    }
}
