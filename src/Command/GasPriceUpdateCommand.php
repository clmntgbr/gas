<?php

namespace App\Command;

use App\Service\GasPriceUpdateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-price-update',
    description: 'Creating Up To Date GasPrices.',
)]
class GasPriceUpdateCommand extends Command
{
    public function __construct(
        private GasPriceUpdateService $gasPriceUpdateService,
        string                        $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(self::getDefaultDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gasPriceUpdateService->update();

        return Command::SUCCESS;
    }
}
