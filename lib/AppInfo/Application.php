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

class Application extends App implements IBootstrap {
    public const APP_ID = 'globalquota';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerService(QuotaService::class, function($c) {
            return new QuotaService(
                $c->getServer()->getConfig(),
                $c->getServer()->getUserManager(),
                $c->getServer()->getRootFolder(),
                $c->getServer()->query('OCP\\AppFramework\\Utility\\ITimeFactory')
            );
        });

        $context->registerCommand(SetQuotaCommand::class);
        $context->registerCommand(StatusCommand::class);
        $context->registerCommand(RecalcCommand::class);
    }

    public function boot(IBootContext $context): void {
    }
}
