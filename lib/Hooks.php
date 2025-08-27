<?php
namespace OCA\GlobalQuota;

use OCP\Util;
use OCP\Files\NotPermittedException;
use OCP\IConfig;

class Hooks {
    public static function register() {
        Util::connectHook('OC_Filesystem', 'write', [self::class, 'checkQuota']);
    }

    public static function checkQuota($params) {
        $config = \OC::$server->get(IConfig::class);

        // Límite en GB definido en config
        $limitGB = (int) $config->getAppValue('globalquota', 'limit_gb', 500);
        $limitBytes = $limitGB * 1024 * 1024 * 1024;

        $used = self::getTotalUsage();

        if ($used >= $limitBytes) {
            throw new NotPermittedException("Se alcanzó el límite global de almacenamiento ($limitGB GB).");
        }
    }

    private static function getTotalUsage(): int {
        // Para optimizar deberías cachear este valor
        $rootFolder = \OC::$server->getRootFolder();
        $users = \OC::$server->getUserManager()->search('');
        $total = 0;

        foreach ($users as $user) {
            $folder = $rootFolder->getUserFolder($user->getUID());
            $total += $folder->getSize();
        }

        return $total;
    }
}
