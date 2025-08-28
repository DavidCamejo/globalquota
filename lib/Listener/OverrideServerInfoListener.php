<?php

namespace OCA\GlobalQuota\Listener;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCA\ServerInfo\Events\LoadAdditionalDataEvent;
use OCA\GlobalQuota\Service\QuotaService;

class OverrideServerInfoListener implements IEventListener {
    private $quotaService;

    public function __construct(QuotaService $quotaService) {
        $this->quotaService = $quotaService;
    }

    public function handle(Event $event): void {
        if (!($event instanceof LoadAdditionalDataEvent)) {
            return;
        }

        $status = $this->quotaService->getStatus();

        // Añadir datos de GlobalQuota al sistema
        $systemData = $event->getData()['nextcloud']['system'] ?? [];
        
        // Sobrescribir/añadir campos de quota global
        $systemData['quota_total'] = $status['quota_bytes'];
        $systemData['quota_used'] = $status['used_bytes'];
        $systemData['quota_free'] = $status['free_bytes'];
        $systemData['quota_percentage'] = $status['usage_percentage'];

        // También añadir como 'disk' para compatibilidad con versiones anteriores
        $event->addData('disk', [
            'total' => $status['quota_bytes'],
            'used' => $status['used_bytes'],
            'free' => $status['free_bytes']
        ]);

        // Actualizar la sección system
        $nextcloudData = $event->getData()['nextcloud'] ?? [];
        $nextcloudData['system'] = $systemData;
        
        $event->setData('nextcloud', $nextcloudData);
    }
}
