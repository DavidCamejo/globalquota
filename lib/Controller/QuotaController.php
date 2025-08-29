<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCA\GlobalQuota\Service\QuotaService;

class QuotaController extends Controller {
    private QuotaService $quotaService;

    public function __construct(
        string $appName,
        IRequest $request,
        QuotaService $quotaService
    ) {
        parent::__construct($appName, $request);
        $this->quotaService = $quotaService;
    }

    /** @NoAdminRequired @NoCSRFRequired */
    public function status(): JSONResponse {
        try {
            $status = $this->quotaService->getStatus();
            
            return new JSONResponse([
                'success' => true,
                'data' => [
                    'used' => $status['used_bytes'],
                    'total' => $status['quota_bytes'],
                    'free' => $status['free_bytes'],
                    'percentage' => $status['usage_percentage'],
                    'formatted' => [
                        'used' => $this->formatBytes($status['used_bytes']),
                        'total' => $this->formatBytes($status['quota_bytes']),
                        'free' => $this->formatBytes($status['free_bytes'])
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /** @NoAdminRequired @NoCSRFRequired */
    public function apiStatus(): JSONResponse {
        return $this->status();
    }

    /** 
     * @NoAdminRequired 
     * @NoCSRFRequired 
     * 
     * Endpoint for frontend quota display - returns simplified format
     */
    public function getQuota(): JSONResponse {
        try {
            $status = $this->quotaService->getStatus();
            
            return new JSONResponse([
                'used' => $status['used_bytes'],
                'available' => $status['free_bytes'],
                'total' => $status['quota_bytes'],
                'percentage' => $status['usage_percentage'],
                'formatted' => [
                    'used' => $this->formatBytes($status['used_bytes']),
                    'available' => $this->formatBytes($status['free_bytes']),
                    'total' => $this->formatBytes($status['quota_bytes'])
                ]
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /** 
     * @AdminRequired 
     * @NoCSRFRequired 
     * 
     * Recalculate quota usage
     */
    public function recalc(): JSONResponse {
        try {
            // Force recalculation of quota usage
            $status = $this->quotaService->getStatus(true);
            
            return new JSONResponse([
                'status' => 'success',
                'message' => 'Quota recalculated successfully',
                'data' => [
                    'used' => $status['used_bytes'],
                    'total' => $status['quota_bytes'],
                    'free' => $status['free_bytes'],
                    'percentage' => $status['usage_percentage'],
                    'formatted' => [
                        'used' => $this->formatBytes($status['used_bytes']),
                        'total' => $this->formatBytes($status['quota_bytes']),
                        'free' => $this->formatBytes($status['free_bytes'])
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Failed to recalculate quota: ' . $e->getMessage()
            ], 500);
        }
    }

    /** @AdminRequired @NoCSRFRequired */
    public function updateQuota(): JSONResponse {
        $quotaBytes = $this->request->getParam('quota_bytes');
        if (!is_numeric($quotaBytes) || $quotaBytes < 0) {
            return new JSONResponse(['success' => false, 'error' => 'Invalid quota value'], 400);
        }

        try {
            $this->quotaService->setQuota((int)$quotaBytes);
            return new JSONResponse([
                'success' => true,
                'message' => 'Quota updated successfully',
                'data' => $this->quotaService->getStatus()
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
