<?php

namespace OCA\GlobalQuota\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCA\GlobalQuota\Service\QuotaService;
use OCP\IRequest;

class ApiController extends Controller {
    private $quotaService;

    public function __construct($AppName, IRequest $request, QuotaService $quotaService) {
        parent::__construct($AppName, $request);
        $this->quotaService = $quotaService;
    }

    /**
     * @NoCSRFRequired
     * @AdminRequired
     */
    public function status(): DataResponse {
        return new DataResponse($this->quotaService->getStatus());
    }

    /**
     * @NoCSRFRequired
     * @AdminRequired
     */
    public function setQuota(): DataResponse {
        $data = $this->request->getParams();
        if (!isset($data['quota_bytes'])) {
            return new DataResponse(['error' => 'Missing quota_bytes'], 400);
        }
        $this->quotaService->setQuota((int)$data['quota_bytes']);
        return new DataResponse(['status' => 'ok']);
    }
}
