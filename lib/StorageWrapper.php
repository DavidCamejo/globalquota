<?php
namespace OCA\GlobalQuota;

use OCP\Files\Storage\IStorage;

class StorageWrapper implements IStorage {
    private $storage;
    private $limitBytes;

    public function __construct(IStorage $storage, int $limitBytes) {
        $this->storage = $storage;
        $this->limitBytes = $limitBytes;
    }

    public function __call($method, $args) {
        // Delegar cualquier llamada no sobrescrita al storage real
        return $this->storage->$method(...$args);
    }

    public function free_space(string $path) {
        $used = Hooks::getTotalUsage();
        $free = max(0, $this->limitBytes - $used);
        return $free;
    }

    public function getLocalFile($path) {
        return $this->storage->getLocalFile($path);
    }

    // También podrías sobrescribir otros métodos si fuera necesario
}
