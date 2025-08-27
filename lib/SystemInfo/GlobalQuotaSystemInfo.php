<?php

namespace OCA\GlobalQuota\SystemInfo;

use OCA\GlobalQuota\Service\QuotaService;
use OCP\System\ISystemInfo;

class GlobalQuotaSystemInfo implements ISystemInfo {
    private $quotaService;

    public function __construct(QuotaService $quotaService) {
        $this->quotaService = $quotaService;
    }

    public function getDiskInfo(): array {
        return $this->quotaService->getStatus();
    }
}
