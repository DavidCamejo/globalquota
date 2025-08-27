<?php

namespace OCA\GlobalQuota\Command;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IConfig;
use OCP\Files\IRootFolder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Recalc extends Command {
    private $rootFolder;
    private $config;

    public function __construct(IRootFolder $rootFolder, IConfig $config) {
        parent::__construct();
        $this->rootFolder = $rootFolder;
        $this->config = $config;
    }

    protected function configure() {
        $this
            ->setName('globalquota:recalc')
            ->setDescription('Recalcula el uso total de almacenamiento y actualiza la caché');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $usage = 0;
        foreach ($this->rootFolder->getUserMounts() as $mount) {
            try {
                $usage += $mount->getStorage()->getCache()->getSize('');
            } catch (\Exception $e) {
                $output->writeln("<error>Error en mount: {$e->getMessage()}</error>");
            }
        }
        $this->config->setAppValue('globalquota', 'cached_usage', (string)$usage);
        $this->config->setAppValue('globalquota', 'cached_time', (string)time());

        $output->writeln("<info>Uso global recalculado: {$usage} bytes</info>");
        return 0;
    }
}
