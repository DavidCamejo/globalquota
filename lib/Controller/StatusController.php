<?php

namespace OCA\GlobalQuota\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;

class StatusController extends Controller {
    private $config;

    public function __construct(string $appName, IRequest $request, IConfig $config) {
        parent::__construct($appName, $request);
        $this->config = $config;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): DataResponse {
        $limitGb = (int)$this->config->getAppValue('globalquota', 'limit_gb', '0');
        $limit = $limitGb > 0 ? $limitGb * 1024 * 1024 * 1024 : 0;

        $used = (int)$this->config->getAppValue('globalquota', 'cached_usage', '0');
        $free = $limit > 0 ? max(0, $limit - $used) : 0;

        return new DataResponse([
            'limit_bytes' => $limit,
            'used_bytes'  => $used,
            'free_bytes'  => $free,
            'limit_human' => $limitGb . ' GB',
            'used_human'  => $this->humanFileSize($used),
            'free_human'  => $this->humanFileSize($free),
        ]);
    }

    private function humanFileSize($bytes): string {
        if ($bytes <= 0) return "0 B";
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}
