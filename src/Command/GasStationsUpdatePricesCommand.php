<?php

namespace App\Command;

use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use App\Service\GasPriceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-stations-update-prices',
    description: 'Add a short description for your command',
)]
class GasStationsUpdatePricesCommand extends Command
{
    public function __construct(
        private GasStationRepository $gasStationRepository,
        private GasTypeRepository $gasTypeRepository,
        private GasPriceRepository $gasPriceRepository,
        private GasPriceService $gasPriceService,
        private EntityManagerInterface $entityManager,
        string $name = null
    ){
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(self::getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $gasStations = $this->gasStationRepository->findAll();
        $gasTypes = $this->gasTypeRepository->findAll();

        foreach ($gasStations as $gasStation) {
            foreach ($gasTypes as $gasType) {
                $gasPrice = $this->gasPriceRepository->findLastGasPriceByTypeAndGasStation($gasStation, $gasType);
                if (null === $gasPrice) {
                    continue;
                }
                $this->gasPriceService->updateLastGasPrices($gasStation, $gasPrice);
            }
            $this->gasPriceService->updatePreviousGasPrices($gasStation);
            $this->entityManager->persist($gasStation);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
