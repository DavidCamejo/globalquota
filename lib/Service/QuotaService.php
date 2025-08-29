<?php

namespace OCA\GlobalQuota\Service;

use OCP\IConfig;
use OCP\IUserManager;
use OCP\Files\IRootFolder;
use OCP\AppFramework\Utility\ITimeFactory;

class QuotaService {
    private $config;
    private $userManager;
    private $rootFolder;
    private $timeFactory;
    private $cacheKey = 'globalquota_usage';
    private $cacheTTL = 300; // 5 minutos

    public function __construct(
        IConfig $config,
        IUserManager $userManager,
        IRootFolder $rootFolder,
        ITimeFactory $timeFactory
    ) {
        $this->config = $config;
        $this->userManager = $userManager;
        $this->rootFolder = $rootFolder;
        $this->timeFactory = $timeFactory;
    }

    public function getQuota(): ?int {
        $val = $this->config->getSystemValue('globalquota', []);
        return $val['quota_bytes'] ?? null;
    }

    public function setQuota(int $bytes): void {
        $this->config->setSystemValue('globalquota', [
            'quota_bytes' => $bytes
        ]);
        $this->config->deleteAppValue('globalquota', $this->cacheKey);
    }

    public function getUsage(bool $forceRecalc = false): int {
        if (!$forceRecalc) {
            $cached = $this->getCache();
            if ($cached !== null) {
                return $cached['used'];
            }
        }

        $totalUsed = 0;
        foreach ($this->userManager->search('') as $user) {
            try {
                $folder = $this->rootFolder->getUserFolder($user->getUID());
                // Usar getSize() en lugar de getCache()->getUsedSpace()
                $totalUsed += $folder->getSize();
            } catch (\Exception $e) {
                // Skip user if there's an error accessing their folder
                continue;
            }
        }

        $this->setCache($totalUsed);
        return $totalUsed;
    }

    public function getStatus(bool $forceRecalc = false): array {
        $quota = $this->getQuota();
        $used = $this->getUsage($forceRecalc);
        return [
            'quota_bytes' => $quota,
            'used_bytes' => $used,
            'free_bytes' => $quota - $used,
            'usage_percentage' => $quota > 0 ? round(($used / $quota) * 100, 2) : 0
        ];
    }

    public function recalculateUsage(): array {
        // Fuerza el recálculo (sin cache)
        $status = $this->getStatus(true);

        // Borra el cache anterior y guarda el nuevo
        $this->config->deleteAppValue('globalquota', $this->cacheKey);
        $this->setCache($status['used_bytes']);

        return $status;
    }

    private function getCache(): ?array {
        $raw = $this->config->getAppValue('globalquota', $this->cacheKey, null);
        if ($raw === null) return null;
        $data = json_decode($raw, true);
        if (($this->timeFactory->getTime() - $data['timestamp']) > $this->cacheTTL) {
            return null;
        }
        return $data;
    }

    private function setCache(int $used): void {
        $data = [
            'used' => $used,
            'timestamp' => $this->timeFactory->getTime()
        ];
        $this->config->setAppValue('globalquota', $this->cacheKey, json_encode($data));
    }
}
