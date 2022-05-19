<?php

namespace App\MessageHandler;

use App\Message\UpdateGasStationAddressMessage;
use App\Repository\GasStationRepository;
use App\Service\ApiAddressService;
use App\Service\GasStationService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateGasStationAddressMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationService      $gasStationService,
        private ApiAddressService      $apiAddressService,
        private GasStationRepository   $gasStationRepository
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(UpdateGasStationAddressMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $this->gasStationService->getGasStationInformationFromGovernment($gasStation);

        $this->apiAddressService->update($gasStation);

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
