<?php

namespace OCA\GlobalQuota\Command;

use OCA\GlobalQuota\Service\QuotaService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetQuotaCommand extends Command {
    private $quotaService;
    public function __construct(QuotaService $quotaService) {
        parent::__construct();
        $this->quotaService = $quotaService;
    }
    protected function configure() {
        $this->setName('globalquota:set')
             ->setDescription('Set global quota in bytes')
             ->addArgument('bytes', InputArgument::REQUIRED, 'Quota in bytes');
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $bytes = (int)$input->getArgument('bytes');
        $this->quotaService->setQuota($bytes);
        $output->writeln("Global quota set to $bytes bytes");
        return 0;
    }
}
