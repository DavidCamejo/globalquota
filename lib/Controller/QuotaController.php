<?php

namespace OCA\GlobalQuota\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCA\GlobalQuota\Service\QuotaService;
use OCP\IRequest;

class QuotaController extends Controller {
    private $quotaService;

    public function __construct(string $appName, IRequest $request, QuotaService $quotaService) {
        parent::__construct($appName, $request);
        $this->quotaService = $quotaService;
    }

    /**
     * GET /apps/globalquota/status
     */
    public function status(): DataResponse {
        return new DataResponse($this->quotaService->getStatus());
    }

    /**
     * POST /apps/globalquota/set
     * Body: { "bytes": 123456 }
     */
    public function setQuota(int $bytes): DataResponse {
        $this->quotaService->setQuota($bytes);
        return new DataResponse([ 'message' => "Quota set to $bytes bytes" ]);
    }

    /**
     * POST /apps/globalquota/recalc
     */
    public function recalc(): DataResponse {
        return new DataResponse($this->quotaService->getStatus(true));
    }
}
