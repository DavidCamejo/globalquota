<?php

namespace OCA\GlobalQuota\Listener;

use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\BeforeFileCreatedEvent;
use OCP\Files\Events\Node\BeforeFileUpdatedEvent;
use OCP\Files\ForbiddenException;
use OCA\GlobalQuota\Service\QuotaService;

class GlobalQuotaListener implements IEventListener {
    private $quotaService;

    public function __construct(QuotaService $quotaService) {
        $this->quotaService = $quotaService;
    }

    /**
     * Maneja eventos de creación/actualización de archivos
     * Bloquea uploads si exceden la cuota global
     */
    public function handle($event): void {
        if ($event instanceof BeforeFileCreatedEvent || $event instanceof BeforeFileUpdatedEvent) {
            $quota = $this->quotaService->getQuota();

            // Si no hay cuota global definida → no bloquea
            if ($quota === null) {
                return;
            }

            // Tamaño actual del sistema
            $used = $this->quotaService->getUsage();

            // Tamaño esperado del nuevo archivo
            $newSize = 0;
            if (method_exists($event, 'getSize')) {
                $newSize = $event->getSize();
            }

            // Si el archivo ya existía (update), restar su tamaño actual
            $currentSize = 0;
            if ($event instanceof BeforeFileUpdatedEvent) {
                try {
                    $node = $event->getNode();
                    if ($node && method_exists($node, 'getSize')) {
                        $currentSize = $node->getSize();
                    }
                } catch (\Exception $e) {
                    $currentSize = 0;
                }
            }

            // Diferencia neta que sumará al uso total
            $delta = max(0, $newSize - $currentSize);

            // Verificar si excede la cuota
            if (($used + $delta) > $quota) {
                throw new ForbiddenException("Global quota exceeded. Upload blocked.");
            }
        }
    }
}
