<?php
namespace OCA\GlobalQuota;

use OCA\GlobalQuota\Hooks;
use OCP\App;

$app = new \OCP\AppFramework\App('globalquota');

// Conectar hooks al sistema de archivos
Hooks::register();
