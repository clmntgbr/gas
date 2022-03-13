<?php

namespace App\DataFixtures;

use App\Entity\GasType;
use App\Lists\GasTypeReference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GasTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'id' => 6,
                'reference' => GasTypeReference::SP98,
                'label' => 'SP98',
            ],
            [
                'reference' => GasTypeReference::E10,
                'id' => 5,
                'label' => 'E10',
            ],
            [
                'reference' => GasTypeReference::E85,
                'id' => 3,
                'label' => 'E85',
            ],
            [
                'reference' => GasTypeReference::GAZOLE,
                'id' => 1,
                'label' => 'Gazole',
            ],
            [
                'reference' => GasTypeReference::GPLC,
                'id' => 4,
                'label' => 'GPLc',
            ],
            [
                'reference' => GasTypeReference::SP95,
                'id' => 2,
                'label' => 'SP95',
            ],
        ];

        foreach ($data as $datum) {
            $gasType = new GasType();
            $gasType
                ->setId($datum['id'])
                ->setLabel($datum['label'])
                ->setReference($datum['reference']);

            $manager->persist($gasType);
        }

        $manager->flush();
        $manager->clear();
    }
}
