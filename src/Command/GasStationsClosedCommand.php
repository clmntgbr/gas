<?php

namespace App\Command;

use App\Service\GasStationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-stations-closed',
    description: 'Add a short description for your command',
)]
class GasStationsClosedCommand extends Command
{
    public function __construct(
        private GasStationService $gasStationService,
        string                    $name = null
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
        $this->gasStationService->updateGasStationsClosed();
        return Command::SUCCESS;
    }
}
