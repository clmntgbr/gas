<?php

namespace App\Command;

use App\Service\GasPriceUpdateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:gas-price-year',
    description: 'Creating Year GasPrices.',
)]
class GasPriceYearCommand extends Command
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
        $this->setDescription(self::getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new Question('Which year to insert ? ', '2022');

        $year = $helper->ask($input, $output, $question);

        $this->gasPriceUpdateService->update($year);

        return Command::SUCCESS;
    }
}
