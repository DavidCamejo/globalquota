<?php

namespace OCA\GlobalQuota\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCA\GlobalQuota\Service\QuotaService;
use OCA\GlobalQuota\Command\SetQuotaCommand;
use OCA\GlobalQuota\Command\StatusCommand;
use OCA\GlobalQuota\Command\RecalcCommand;
use OCA\GlobalQuota\Listener\GlobalQuotaListener;

class Application extends App implements IBootstrap {
    public const APP_ID = 'globalquota';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        // Servicio principal
        $context->registerService(QuotaService::class, function($c) {
            return new QuotaService(
                $c->getServer()->getConfig(),
                $c->getServer()->getUserManager(),
                $c->getServer()->getRootFolder(),
                $c->getServer()->query('OCP\\AppFramework\\Utility\\ITimeFactory')
            );
        });

        // Commands OCC
        $context->registerCommand(SetQuotaCommand::class);
        $context->registerCommand(StatusCommand::class);
        $context->registerCommand(RecalcCommand::class);

        // Event Listeners PSR-14 para bloqueo de uploads
        $context->registerEventListener(
            \OCP\Files\Events\Node\BeforeFileCreatedEvent::class,
            GlobalQuotaListener::class
        );
        $context->registerEventListener(
            \OCP\Files\Events\Node\BeforeFileUpdatedEvent::class,
            GlobalQuotaListener::class
        );
    }

    public function boot(IBootContext $context): void {
        // Boot logic if needed
    }
}
