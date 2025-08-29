<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;

class Application extends App {
	public const APP_ID = 'globalquota';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);

		$container = $this->getContainer();
		$this->registerServices($container);
	}

	private function registerServices(IAppContainer $c): void {
		// Registrar servicios si aplica
	}
}
