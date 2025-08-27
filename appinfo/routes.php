<?php
return [
    'routes' => [
        ['name' => 'api#status', 'url' => '/api/v1/status', 'verb' => 'GET'],
        ['name' => 'api#setQuota', 'url' => '/api/v1/quota', 'verb' => 'PUT'],
    ],
];
