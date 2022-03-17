<?php

namespace App\MessageHandler;

use App\Common\EntityId\GasStationId;
use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceMessage;
use App\Repository\GasStationRepository;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateGooglePlaceMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository   $gasStationRepository,
        private GooglePlaceService     $googlePlaceService,
        private GasStationStatusHelper $gasStationStatusHelper,
        private MessageBusInterface    $messageBus
    )
    {
    }

    public function __invoke(CreateGooglePlaceMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::PLACE_ID_ANOMALY === $gasStation->getGasStationStatus()->getReference()) {
            return;
        }

        $googlePlace = $gasStation->getGooglePlace();

        $googlePlace->setPlaceId($message->getPlaceId());

        $gasStationsAnomalies = $this->gasStationRepository->getGasStationGooglePlaceByPlaceId($message->getPlaceId());

        if (count($gasStationsAnomalies) > 0) {
            $this->googlePlaceService->createAnomalies($gasStation, $gasStationsAnomalies);
            return;
        }

        $this->gasStationStatusHelper->setStatus(GasStationStatusReference::FOUND_IN_TEXTSEARCH, $gasStation);

        $this->messageBus->dispatch(new CreateGooglePlaceDetailsMessage(
            new GasStationId($gasStation->getId())
        ), [new AmqpStamp('async-priority-low', AMQP_NOPARAM, [])]);

        $this->em->persist($googlePlace);
        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
