<?php

namespace App\MessageHandler;

use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceAnomalyMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGooglePlaceAnomalyMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private GasStationStatusHelper $gasStationStatusHelper,
        private EntityManagerInterface $em,
        private GasStationRepository   $gasStationRepository
    )
    {
    }

    public function __invoke(CreateGooglePlaceAnomalyMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        foreach ($message->getGasStationIds() as $gasStationId) {
            $gasStation = $this->gasStationRepository->findOneBy(['id' => $gasStationId->getId()]);

            if (null === $gasStation) {
                throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s', $gasStationId->getId()));
            }

            $this->gasStationStatusHelper->setStatus(GasStationStatusReference::PLACE_ID_ANOMALY, $gasStation);
        }
    }
}
