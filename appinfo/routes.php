<?php
return [
  'routes' => [
    ['name' => 'quota#status', 'url' => '/status', 'verb' => 'GET'],
    ['name' => 'quota#setQuota', 'url' => '/set', 'verb' => 'POST'],
    ['name' => 'quota#recalc', 'url' => '/recalc', 'verb' => 'POST'],
  ]
];
