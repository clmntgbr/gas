<?php

namespace App\Command;

use App\Service\GooglePlaceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-stations-details',
    description: 'Add a short description for your command',
)]
class GasStationsDetailsCommand extends Command
{
    public function __construct(
        private GooglePlaceService $googlePlaceService,
        string $name = null
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
        $this->googlePlaceService->update();

        return Command::SUCCESS;
    }
}
