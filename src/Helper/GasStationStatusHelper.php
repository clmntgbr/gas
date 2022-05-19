<?php

namespace App\Helper;

use App\Entity\GasStation;
use App\Entity\GasStationStatusHistory;
use App\Repository\GasStationStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GasStationStatusHelper
{
    public function __construct(
        private EntityManagerInterface     $em,
        private GasStationStatusRepository $gasStationStatusRepository
    )
    {
    }

    public function setStatus(string $reference, GasStation $gasStation): void
    {
        $gasStationStatus = $this->gasStationStatusRepository->findOneBy(['reference' => $reference]);

        if (null === $gasStationStatus) {
            throw new Exception(sprintf('Gas Station Status don\'t exist (reference : %s', $reference));
        }

        $gasStation->setGasStationStatus($gasStationStatus);

        $gasStationStatusHistory = new GasStationStatusHistory();
        $gasStationStatusHistory
            ->setGasStationStatus($gasStationStatus)
            ->setGasStation($gasStation);

        $this->em->persist($gasStation);
        $this->em->persist($gasStationStatusHistory);

        $this->em->flush();
    }
}