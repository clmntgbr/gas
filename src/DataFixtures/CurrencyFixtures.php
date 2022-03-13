<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Lists\CurrencyReference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'reference' => CurrencyReference::EUR,
                'label' => 'EUR / €',
            ],
            [
                'reference' => CurrencyReference::USD,
                'label' => 'USD / $',
            ]
        ];

        foreach ($data as $datum) {
            $currency = new Currency();
            $currency
                ->setLabel($datum['label'])
                ->setReference($datum['reference']);

            $manager->persist($currency);
        }

        $manager->flush();
        $manager->clear();
    }
}
