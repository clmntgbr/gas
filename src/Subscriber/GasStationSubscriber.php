<?php

namespace App\Subscriber;

use App\Entity\GasStation;
use App\Repository\GasPriceRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class GasStationSubscriber implements EventSubscriber
{
    public function __construct(
        private GasPriceRepository $gasPriceRepository
    )
    {
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postLoad
        ];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof GasStation) {
            return;
        }

        $lastGasPrices = $entity->getLastGasPrices();
        foreach ($lastGasPrices as $lastGasPrice) {
            $gasPrice = $this->gasPriceRepository->findOneBy(['id' => $lastGasPrice['id']]);
            if (null === $gasPrice) {
                continue;
            }
            $entity->setLastGasPricesDecode($gasPrice->getGasType(), $gasPrice);
        }
    }
}