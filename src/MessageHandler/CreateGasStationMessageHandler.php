<?php

namespace App\MessageHandler;

use App\Entity\Address;
use App\Entity\GasStation;
use App\Entity\GooglePlace;
use App\Entity\Media;
use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGasStationMessage;
use App\Message\UpdateGasStationAddress;
use App\Service\GasStationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateGasStationMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationStatusHelper $gasStationStatusHelper,
        private GasStationService      $gasStationService,
        private MessageBusInterface    $messageBus
    )
    {
    }

    public function __invoke(CreateGasStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->em->getRepository(GasStation::class)->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if ($gasStation instanceof GasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station already exist (id : %s)', $message->getGasStationId()->getId()));
        }

        $address = new Address();
        $address
            ->setCity($message->getCity())
            ->setPostalCode($message->getCp())
            ->setLongitude($message->getLongitude() ? $message->getLongitude() / 100000 : null)
            ->setLatitude($message->getLatitude() ? $message->getLatitude() / 100000 : null)
            ->setCountry($message->getCountry())
            ->setStreet($message->getStreet())
            ->setVicinity(sprintf('%s, %s %s, %s', $message->getStreet(), $message->getCp(), $message->getCity(), $message->getCountry()));

        $media = new Media();
        $media
            ->setPath(GasStationService::PREVIEW_GAS_STATIONS_PATH)
            ->setName(GasStationService::PREVIEW_GAS_STATIONS_NAME)
            ->setType('jpg')
            ->setMimeType('image/jpg')
            ->setSize(467);

        $gasStation = new GasStation();
        $gasStation
            ->setId($message->getGasStationId()->getId())
            ->setPop($message->getPop())
            ->setElement($message->getElement())
            ->setAddress($address)
            ->setPreview($media)
            ->setGooglePlace(new GooglePlace());

        $this->gasStationStatusHelper->setStatus(GasStationStatusReference::IN_CREATION, $gasStation);

        $this->gasStationService->isGasStationClosed($message->getElement(), $gasStation);

        if (null !== $gasStation->getClosedAt()) {
            $this->gasStationStatusHelper->setStatus(GasStationStatusReference::CLOSED, $gasStation);
            return;
        }

        $this->gasStationService->getGasStationInformationFromGovernment($gasStation);

        $this->em->persist($gasStation);
        $this->em->flush();

        $this->messageBus->dispatch(new UpdateGasStationAddress($message->getGasStationId()));
    }
}
