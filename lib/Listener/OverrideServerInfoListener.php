<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\Listener;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCA\GlobalQuota\Service\QuotaService;
use OCA\ServerInfo\Events\LoadAdditionalDataEvent;

class OverrideServerInfoListener implements IEventListener {
    private QuotaService $quotaService;

    public function __construct(QuotaService $quotaService) {
        $this->quotaService = $quotaService;
    }

    public function handle(Event $event): void {
        if (!($event instanceof LoadAdditionalDataEvent)) {
            return;
        }

        try {
            $status = $this->quotaService->getStatus();
            
            $event->addData('disk', [
                'used' => $status['used_bytes'],
                'available' => $status['free_bytes'],
                'total' => $status['quota_bytes'],
                'percent' => $status['percentage_used'],
                'mount' => 'GlobalQuota',
                'filesystem' => 'Global Storage'
            ]);

            $event->addData('quota_used', $status['used_bytes']);
            $event->addData('quota_total', $status['quota_bytes']);
            $event->addData('quota_free', $status['free_bytes']);
            $event->addData('quota_percentage', $status['percentage_used']);

        } catch (\Exception $e) {
            \OC::$server->getLogger()->error(
                'GlobalQuota: Error al obtener datos para ServerInfo: ' . $e->getMessage(),
                ['app' => 'globalquota']
            );
        }
    }
}
