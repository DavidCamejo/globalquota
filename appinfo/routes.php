<?php

return [
    'routes' => [
        ['name' => 'quota#status',      'url' => '/status',         'verb' => 'GET'],
        ['name' => 'quota#apiStatus',   'url' => '/api/v1/status',  'verb' => 'GET'],
        ['name' => 'quota#updateQuota', 'url' => '/api/v1/quota',   'verb' => 'PUT'],
        ['name' => 'quota#setQuota',    'url' => '/set',            'verb' => 'POST'],
        ['name' => 'quota#getQuota',    'url' => '/quota',          'verb' => 'GET'],
        ['name' => 'quota#recalc',      'url' => '/recalc',         'verb' => 'GET'],
    ]
];
