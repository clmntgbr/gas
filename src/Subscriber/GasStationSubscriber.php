<?php

namespace App\Subscriber;

use App\Entity\GasStation;
use App\Helper\GasStationStatusHelper;
use App\Repository\GasPriceRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class GasStationSubscriber implements EventSubscriber
{
    public function __construct(
        private GasPriceRepository     $gasPriceRepository,
        private GasStationStatusHelper $gasStationStatusHelper
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad => 'postLoad',
            Events::postUpdate => 'postUpdate',
        ];
    }

    public function postLoad(LifecycleEventArgs $args): void
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

        $previousGasPrices = $entity->getPreviousGasPrices();
        foreach ($previousGasPrices as $previousGasPrice) {
            $gasPrice = $this->gasPriceRepository->findOneBy(['id' => $previousGasPrice['id']]);
            if (null === $gasPrice) {
                continue;
            }
            $entity->setPreviousGasPricesDecode($gasPrice->getGasType(), $gasPrice);
        }
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof GasStation) {
            return;
        }

        $changeSet = $event->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity);

        if (!array_key_exists('gasStationStatus', $changeSet)) {
            return;
        }

        if (null !== $entity->getLastGasStationStatusHistory() && $entity->getLastGasStationStatusHistory()->getGasStationStatus()->getReference() === $changeSet['gasStationStatus'][1]->getReference()) {
            return;
        }

        $this->gasStationStatusHelper->setStatus($changeSet['gasStationStatus'][1]->getReference(), $entity, true);
    }
}