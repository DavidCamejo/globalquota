<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\Settings\Admin;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;
use OCA\GlobalQuota\Service\QuotaService;

class Settings implements ISettings {
    private IConfig $config;
    private IL10N $l;
    private QuotaService $quotaService;

    public function __construct(IConfig $config, IL10N $l, QuotaService $quotaService) {
        $this->config = $config;
        $this->l = $l;
        $this->quotaService = $quotaService;
    }

    public function getForm(): TemplateResponse {
        $showOwnChart = !class_exists('\OCA\ServerInfo\Events\LoadAdditionalDataEvent');
        try {
            $status = $this->quotaService->getStatus();
        } catch (\Exception $e) {
            $status = ['used_bytes' => 0,'quota_bytes' => 0,'free_bytes' => 0,'percentage_used' => 0];
        }
        return new TemplateResponse('globalquota','admin-settings',[
            'showChart' => $showOwnChart,
            'quotaStatus' => $status,
            'serverInfoIntegration' => !$showOwnChart
        ]);
    }

    public function getSection(): string {
        return 'globalquota';
    }

    public function getPriority(): int {
        return 50;
    }
}
