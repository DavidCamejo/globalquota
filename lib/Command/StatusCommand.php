<?php

namespace OCA\GlobalQuota\Command;

use OCA\GlobalQuota\Service\QuotaService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command {
    private $quotaService;
    public function __construct(QuotaService $quotaService) {
        parent::__construct();
        $this->quotaService = $quotaService;
    }
    protected function configure() {
        $this->setName('globalquota:status')
             ->setDescription('Show global quota and usage');
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $status = $this->quotaService->getStatus();
        $output->writeln("Quota: {$status['quota_bytes']} bytes");
        $output->writeln("Used:  {$status['used_bytes']} bytes");
        $output->writeln("Free:  {$status['free_bytes']} bytes");
        $output->writeln("Usage: {$status['usage_percentage']}%");
        return 0;
    }
}
