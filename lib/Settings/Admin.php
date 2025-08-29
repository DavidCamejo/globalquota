<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;
use OCP\Util;

class Admin implements ISettings {

	/** @var IL10N */
	private $l10n;
	/** @var IConfig */
	private $config;

	public function __construct(IL10N $l10n, IConfig $config) {
		$this->l10n = $l10n;
		$this->config = $config;
	}

	public function getForm(): TemplateResponse {
		Util::addScript('globalquota', 'admin-globalquota');
		Util::addStyle('globalquota', 'admin-globalquota');

		return new TemplateResponse('globalquota', 'admin', [
			// aquí puedes pasar valores iniciales si hiciera falta
		], 'blank');
	}

	public function getSection(): string {
		return 'server';
	}

	public function getPriority(): int {
		return 50;
	}
}
