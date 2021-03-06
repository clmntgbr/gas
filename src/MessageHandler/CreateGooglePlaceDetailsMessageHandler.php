<?php

namespace App\MessageHandler;

use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Repository\GasStationRepository;
use App\Service\GooglePlaceApiService;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGooglePlaceDetailsMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private GasStationStatusHelper $gasStationStatusHelper,
        private EntityManagerInterface $em,
        private GooglePlaceApiService  $googlePlaceApiService,
        private GooglePlaceService     $googlePlaceService,
        private GasStationRepository   $gasStationRepository
    )
    {
    }

    public function __invoke(CreateGooglePlaceDetailsMessage $message): void
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

        $details = $this->googlePlaceApiService->placeDetails($gasStation);

        if (null === $details) {
            $this->gasStationStatusHelper->setStatus(GasStationStatusReference::NOT_FOUND_IN_DETAILS, $gasStation);
            return;
        }

        $gasStation->setName($details['name'] ?? null);

        $this->googlePlaceService->updateGasStationGooglePlace($gasStation, $details);
        $this->googlePlaceService->UpdateGasStationAddressMessage($gasStation, $details);

        $this->gasStationStatusHelper->setStatus(GasStationStatusReference::WAITING_VALIDATION, $gasStation);

        $this->em->flush();
    }
}
