<?php

declare(strict_types=1);

namespace OCA\GlobalQuota\Settings\Admin;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class Section implements IIconSection {
    private IL10N $l;
    private IURLGenerator $urlGenerator;

    public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
        $this->l = $l;
        $this->urlGenerator = $urlGenerator;
    }

    public function getID(): string {
        return 'globalquota';
    }

    public function getName(): string {
        return $this->l->t('Global Quota');
    }

    public function getPriority(): int {
        return 75;
    }

    public function getIcon(): string {
        return $this->urlGenerator->imagePath('globalquota', 'app-dark.svg');
    }
}
