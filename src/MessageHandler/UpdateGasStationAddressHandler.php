<?php

namespace App\MessageHandler;

use App\Entity\GasStation;
use App\Message\UpdateGasStationAddress;
use App\Service\ApiAddressService;
use App\Service\GasStationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateGasStationAddressHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationService      $gasStationService,
        private ApiAddressService      $apiAddressService
    )
    {
    }

    public function __invoke(UpdateGasStationAddress $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create($this->em->getConnection(), $this->em->getConfiguration());
        }

        /** @var GasStation $gasStation */
        $gasStation = $this->em->getRepository(GasStation::class)->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $this->gasStationService->getGasStationInformationFromGovernment($gasStation);

        $this->apiAddressService->update($gasStation);

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
