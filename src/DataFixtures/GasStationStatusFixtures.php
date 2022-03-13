<?php

namespace App\DataFixtures;

use App\Entity\GasStationStatus;
use App\Lists\GasStationStatusReference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GasStationStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'reference' => GasStationStatusReference::IN_CREATION,
                'label' => 'In Creation',
            ],
            [
                'reference' => GasStationStatusReference::NOT_FOUND_IN_TEXTSEARCH,
                'label' => 'Not Found In TextSearch',
            ],
            [
                'reference' => GasStationStatusReference::FOUND_ON_GOV_MAP,
                'label' => 'Found On Government Map',
            ],
            [
                'reference' => GasStationStatusReference::FOUND_IN_TEXTSEARCH,
                'label' => 'Found In TextSearch',
            ],
            [
                'reference' => GasStationStatusReference::PLACE_ID_ANOMALY,
                'label' => 'Place Id Anomaly',
            ],
            [
                'reference' => GasStationStatusReference::WAITING_VALIDATION,
                'label' => 'Waiting Validation',
            ],
            [
                'reference' => GasStationStatusReference::OPEN,
                'label' => 'Open',
            ],
            [
                'reference' => GasStationStatusReference::CLOSED,
                'label' => 'Closed',
            ],
        ];

        foreach ($data as $datum) {
            $gasStationStatus = new GasStationStatus();
            $gasStationStatus
                ->setLabel($datum['label'])
                ->setReference($datum['reference']);

            $manager->persist($gasStationStatus);
        }

        $manager->flush();
        $manager->clear();
    }
}
