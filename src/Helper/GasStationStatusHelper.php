<?php

namespace App\Helper;

use App\Entity\GasStation;
use App\Entity\GasStationStatus;
use App\Entity\GasStationStatusHistory;
use Doctrine\ORM\EntityManagerInterface;

class GasStationStatusHelper
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setStatus(string $reference, GasStation $gasStation)
    {
        $gasStationStatus = $this->em->getRepository(GasStationStatus::class)->findOneBy(['reference' => $reference]);

        if (null === $gasStationStatus) {
            throw new \Exception(sprintf('Gas Station Status don\'t exist (reference : %s', $reference));
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