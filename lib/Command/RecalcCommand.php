<?php

namespace OCA\GlobalQuota\Command;

use OCA\GlobalQuota\Service\QuotaService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecalcCommand extends Command {
    private $quotaService;
    public function __construct(QuotaService $quotaService) {
        parent::__construct();
        $this->quotaService = $quotaService;
    }
    protected function configure() {
        $this->setName('globalquota:recalc')
             ->setDescription('Recalculate global usage');
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $status = $this->quotaService->getStatus(true);
        $output->writeln("Recalculated usage: {$status['used_bytes']} bytes");
        return 0;
    }
}
