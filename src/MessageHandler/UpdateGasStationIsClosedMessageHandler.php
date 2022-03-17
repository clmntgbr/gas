<?php

namespace App\MessageHandler;

use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\UpdateGasStationIsClosedMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateGasStationIsClosedMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository   $gasStationRepository,
        private GasStationStatusHelper $gasStationStatusHelper
    )
    {
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function __invoke(UpdateGasStationIsClosedMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $gasStation->setClosedAt(new DateTimeImmutable());
        $this->gasStationStatusHelper->setStatus(GasStationStatusReference::CLOSED, $gasStation);
    }
}
